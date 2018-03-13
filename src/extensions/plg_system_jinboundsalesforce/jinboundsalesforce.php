<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundsalesforce
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

class plgSystemJInboundsalesforce extends JPlugin
{
    protected static $client = null;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * Constructor
     *
     * @param JEventDispatcher $subject
     * @param array            $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage('plg_system_jinboundsalesforce.sys');

        $this->enabled = is_dir(JPATH_ADMINISTRATOR . '/components/com_jinbound');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();

        if ($app->isClient('site') || !$this->enabled) {
            return;
        }

        $option = $app->input->getCmd('option');
        if ($option != 'plg_system_jinboundsalesforce') {
            return;
        }

        $view   = $app->input->getCmd('view');
        $method = 'execTask' . ucwords($view);
        if (method_exists($this, $method)) {
            $this->$method();
        }
        jexit();
    }

    /**
     * @param JForm $form
     *
     * @return bool
     */
    public function onContentPrepareForm($form)
    {
        if (!$this->enabled) {
            return true;
        }

        if (!$form instanceof JForm) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }

        if ($form->getName() == 'com_jinbound.field') {
            $file = 'jinboundsalesforce';

            JForm::addFormPath(dirname(__FILE__) . '/form');
            JForm::addFieldPath(dirname(__FILE__) . '/field');

            $result = $form->loadFile($file, false);

            return $result;
        }

        return true;
    }

    public function onContentAfterSave($context, $conversion, $isNew)
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        // only operate on jinbound conversion contexts
        if ('com_jinbound.conversion' !== $context || !PLG_SYSTEM_JINBOUNDSALESFORCE) {
            return;
        }
        // get fields
        $fields = $this->getFieldsByPage($conversion->page_id);
        if (empty($fields)) {
            return;
        }
        // store the object's fields in an array
        $objectfields = array();
        // decode data
        $formdata = json_decode($conversion->formdata);
        // loop fields
        foreach ($fields as $name => $field) {
            // decode params
            $params = json_decode($field->params);
            // check that params is an object
            if (!(is_object($params) && property_exists($params, 'salesforce')
                && is_object($params->salesforce) && property_exists($params->salesforce, 'mapped_field'))) {
                continue;
            }
            // add mapped field
            if (!empty($params->salesforce->mapped_field)) {
                $objectfields[$params->salesforce->mapped_field] = $formdata->lead->$name;
            }
        }
        if (empty($objectfields)) {
            return;
        }
        $client = $this->getClient();
        if ($client) {
            // create a new SObject
            $object         = new SObject();
            $object->type   = 'Contact';
            $object->fields = $objectfields;
            // check response
            try {
                $response = $client->create(array($object));
            } catch (Exception $e) {
                // TODO
            }
        }
        // TODO save response ids?
    }

    protected function getFieldsByPage($page_id)
    {
        $db     = JFactory::getDbo();
        $rows   = $db->setQuery($db->getQuery(true)
            ->select('Field.*')
            ->from('#__jinbound_fields AS Field')
            ->leftJoin('#__jinbound_form_fields AS FormFields ON FormFields.field_id = Field.id')
            ->leftJoin('#__jinbound_pages AS Page ON FormFields.form_id = Page.formid AND Page.id = ' . (int)$page_id)
            ->where('Field.published = 1')
            ->group('Field.id')
        )->loadObjectList();
        $result = array();
        foreach ($rows as $row) {
            $result[$row->name] = $row;
        }
        return $result;
    }

    public function getClient()
    {
        if (empty($this->client)) {
            require_once dirname(__FILE__) . '/library/force/SforcePartnerClient.php';
            $this->startClient();
        }
        return $this->client;
    }

    private function startClient()
    {
        // check session for client info
        $session       = JFactory::getSession();
        $sess_wsdl     = $session->get('jinboundsalesforce.wsdl', '');
        $sess_location = $session->get('jinboundsalesforce.location', '');
        $sess_id       = $session->get('jinboundsalesforce.id', '');
        // confirm session info first
        if (!empty($sess_wsdl) && !empty($sess_location) && !empty($sess_id)) {
            // if this works, it's all done
            try {
                $this->client = new SforcePartnerClient();
                $this->client->createConnection($sess_wsdl);
                $this->client->setEndpoint($sess_location);
                $this->client->setSessionHeader($sess_id);
                return;
            } // failure - kill the session data and continue connecting fresh
            catch (Exception $e) {
                $session->set('jinboundsalesforce.wsdl', null);
                $session->set('jinboundsalesforce.location', null);
                $session->set('jinboundsalesforce.id', null);
            }
        }
        // get config from plugin params
        $wsdl     = $this->params->get('wsdl', '');
        $username = $this->params->get('username', '');
        $password = $this->params->get('password', '');
        $token    = $this->params->get('security_token', '');
        $usetoken = (int)$this->params->get('use_token', 1);
        $wsdlfile = dirname(__FILE__) . '/wsdl/' . $wsdl;
        // confirm that required settings are available
        if (empty($wsdl) || !file_exists($wsdlfile) || !preg_match('/\.xml$/i', $wsdlfile)
            || empty($username) || empty($password) || (empty($token) && $usetoken)) {
            return;
        }
        // attempt to connect to salesforce
        try {
            $this->client = new SforcePartnerClient();
            $this->client->createConnection($wsdlfile);
            $this->client->login($username, $password . ($usetoken ? $token : ''));
        } // could not connect, bail
        catch (Exception $e) {
            $this->client = null;
            return;
        }
        // set data into session for reuse later
        $session->set('jinboundsalesforce.wsdl', $wsdlfile);
        $session->set('jinboundsalesforce.location', $this->client->getLocation());
        $session->set('jinboundsalesforce.id', $this->client->getSessionId());
    }

    /**
     * @return array
     */
    public function onJInboundSalesforceFields()
    {
        if ($this->enabled) {
            return array();
        }

        $options = array();
        $client  = $this->getClient();

        if (is_object($client) && method_exists($client, 'describeSObject')) {
            $contact = $client->describeSObject('Contact');

            if (is_object($contact) && property_exists($contact, 'fields')
                && is_array($contact->fields) && !empty($contact->fields)
            ) {
                foreach ($contact->fields as $field) {
                    // only show fields that can be created
                    if (!$field->createable || $field->deprecatedAndHidden) {
                        continue;
                    }
                    $options[] = JHtml::_('select.option', $field->name, $field->label);
                }
            }
        }

        return $options;
    }

    private function execTaskClose($file = null)
    {
        if (is_null($file)) {
            $this->execTaskUpload();
            return;
        }
        $view = $this->getFieldView();
        $view->setLayout('close');
        $view->field = $this->app->input->get('field', '', 'string');
        $view->file  = $file;
        echo $view->display();
    }

    private function execTaskUpload()
    {
        JFactory::getSession()->checkToken() or die(JText::_('JINVALID_TOKEN'));
        $file = JFactory::getApplication()->input->files->get('wsdl');
        if (empty($file) || !is_array($file)) {
            $this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_NO_UPLOAD_FILE');
            $this->execTaskForm(false);
            return;
        }
        $filename = JFile::makeSafe($file['name']);
        $ext      = JFile::getExt($filename);
        $src      = $file['tmp_name'];
        $dest     = dirname(__FILE__) . "/wsdl/" . $filename;
        if (strtolower($ext) !== 'xml') {
            $this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_FILE_MUST_BE_XML');
            $this->execTaskForm(false);
            return;
        }
        if (!JFile::upload($src, $dest)) {
            $this->errors[] = JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_COULD_NOT_MOVE_FILE');
            $this->execTaskForm(false);
            return;
        }
        $this->execTaskClose($filename);
    }

    private function execTaskForm($token = true)
    {
        if ($token) {
            JFactory::getSession()->checkToken('get') or die(JText::_('JINVALID_TOKEN'));
        }
        $view         = $this->getFieldView();
        $view->errors = $this->errors;
        $view->field  = $this->app->input->get('field', '', 'string');
        echo $view->display();
    }

    /**
     * gets a new instance of the base field view
     *
     * @return JInboundFieldView
     */
    protected function getFieldView()
    {
        $viewConfig = array('template_path' => dirname(__FILE__) . '/field/wsdl');
        $view       = new JInboundFieldView($viewConfig);
        return $view;
    }
}
