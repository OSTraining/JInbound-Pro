<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundmailchimp
 **********************************************
 * jInbound
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

defined('_JEXEC') or die;

class JFormFieldJinboundmailchimpcontactinfo extends JFormField
{
    /**
     * @var string
     */
    protected $layout = 'jinbound.field.mailchimp.contactinfo';

    /**
     * @var JFormField
     */
    protected $emailField = null;

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (parent::setup($element, $value, $group)) {
            if ($emailField = (string)$element['emailfield']) {
                $this->emailField = $this->form->getField($emailField, $group);
            }

            return $this->emailField instanceof JFormField;
        }

        return false;
    }

    protected function getRenderer($layoutId = 'default')
    {
        $renderer = parent::getRenderer($layoutId);

        if ($layoutId == $this->layout) {
            $renderer->addIncludePath(JPATH_PLUGINS . '/system/jinboundmailchimp/layouts');
        }

        return $renderer;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getLayoutData()
    {
        $email = $this->emailField ? $this->emailField->value : '';
        if ($email) {
            $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
            require_once realpath(__DIR__ . '/../library/helper.php');
            $helper = new JinboundMailchimp(array('params' => $plugin->params));

            $memberships = $helper->getMemberships($email);
        }

        $fieldData = array(
            'email'   => $email,
            'memberships' => empty($memberships) ? array() : $memberships
        );

        return array_merge(parent::getLayoutData(), $fieldData);
    }

    protected function getLabel()
    {
        return '';
    }
}
