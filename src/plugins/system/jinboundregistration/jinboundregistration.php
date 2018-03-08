<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundregistration
 **********************************************
 * JInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

$db      = JFactory::getDbo();
$plugins = $db->setQuery($db->getQuery(true)
    ->select('extension_id')->from('#__extensions')
    ->where($db->qn('element') . ' = ' . $db->q('com_jinbound'))
    ->where($db->qn('enabled') . ' = 1')
)->loadColumn();
defined('PLG_SYSTEM_JINBOUNDREGISTRATION') or define('PLG_SYSTEM_JINBOUNDREGISTRATION', 1 === count($plugins));

class plgSystemJInboundregistration extends JPlugin
{
    protected $app;

    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->app = JFactory::getApplication();
        $this->loadLanguage('plg_system_jinboundregistration.sys', JPATH_ADMINISTRATOR);
    }

    public function onAfterInitialise()
    {
        if ($this->app->isSite() || !PLG_SYSTEM_JINBOUNDREGISTRATION || JFactory::getUser()->guest) {
            return;
        }
        $option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
        $view   = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
        if ('plg_system_jinboundregistration' === $option && 'liveupdate' === $view) {
            require_once JPATH_ROOT . '/plugins/system/jinboundregistration/liveupdate/liveupdate.php';
            $updateInfo = LiveUpdate::getUpdateInformation();
            if ($updateInfo->hasUpdates) {
                echo JText::sprintf('PLG_SYSTEM_JINBOUNDREGISTRATION_UPDATE_HASUPDATES', $updateInfo->version);
            }
            jexit();
        }
    }

    public function onJinboundDashboardUpdate()
    {
        return "index.php?option=plg_system_jinboundregistration&view=liveupdate";
    }

    public function onContentAfterSave($context, &$contact, $isNew)
    {
        if ('com_jinbound.contact' !== $context) {
            return;
        }
        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }
        if (!$isNew) {
            if (JDEBUG) {
                $this->app->enqueueMessage('Not registering - not a new record');
            }
            return;
        }
        if (property_exists($contact, 'user_id') && $contact->user_id) {
            if (JDEBUG) {
                $this->app->enqueueMessage('Not registering - user exists');
            }
            return;
        }
        if (!($user_id = $this->createContactUser($contact))) {
            if (JDEBUG) {
                $this->app->enqueueMessage('Not registering - could not create');
            }
            return;
        }
        try {
            $contact->bind(array('user_id' => $user_id));
            $contact->check();
            $contact->store();
        } catch (Exception $e) {
            if (JDEBUG) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
        }
    }

    protected function createContactUser(&$contact)
    {
        $this->loadUserAssets();
        // get the user params
        $userparams = JComponentHelper::getParams('com_users');
        // check if user creation is activated in the first place
        if (!($userparams->get('allowUserRegistration'))) {
            if (JDEBUG) {
                $this->app->enqueueMessage('Registration disabled');
            }
            return false;
        }
        // process the data using the user model (see com_users)
        $model = new UsersModelRegistration();
        if (!($form = $model->getForm())) {
            if (JDEBUG) {
                $this->app->enqueueMessage('No registration form');
            }
            return false;
        }
        // ack, captcha seems to screw this up - remove captcha from form before validate
        $form->removeField('captcha');
        // build request
        $request = $this->buildRegistrationRequestFromContact($contact);
        $data    = $model->validate($form, $request);
        if (false === $data) {
            // Get the validation messages.
            $errors = $model->getErrors();
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $this->app->enqueueMessage($errors[$i], 'warning');
                }
            }
            return false;
        }
        // register the user
        $status = $model->register($data);
        // handle failures
        if (false === $status) {
            if (JDEBUG) {
                $this->app->enqueueMessage('An error occurred');
            }
            return false;
        }
        // registration was a success - send back user id
        $db = JFactory::getDbo();
        try {
            $user_id = $db->setQuery($db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('username') . ' = ' . $db->quote($data['username']))
            )->loadResult();
        } catch (Exception $e) {
            if (JDEBUG) {
                $this->app->enqueueMessage($e->getMessage(), 'error');
            }
            return false;
        }
        return $user_id;
    }

    protected function loadUserAssets()
    {
        // load com_users language file
        JFactory::getLanguage()->load('com_users');
        // user helper
        jimport('joomla.user.helper');
        // use the core user model to handle registration
        $classname = 'UsersModelRegistration';
        if (!class_exists($classname)) {
            jimport('joomla.filesystem.file');
            if (JFile::exists($require = JPATH_ROOT . '/components/com_users/models/registration.php')) {
                require_once $require;
            }
        }
        // forms
        jimport('joomla.form.form');
        JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
    }

    protected function buildRegistrationRequestFromContact($contact)
    {
        $password = JUserHelper::genRandomPassword();
        $request  = array(
            'name'      => trim($contact->first_name . ' ' . $contact->last_name)
        ,
            'username'  => $contact->email
        ,
            'email1'    => $contact->email
        ,
            'email2'    => $contact->email
        ,
            'password1' => $password
        ,
            'password2' => $password
        );
        return $request;
    }
}
