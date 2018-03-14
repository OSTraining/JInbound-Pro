<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
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

defined('JPATH_PLATFORM') or die;


JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');
JInbound::registerHelper('form');

class JInboundModelPage extends JInboundAdminModel
{
    public    $_context = 'com_jinbound.page';
    protected $context  = 'com_jinbound.page';

    private $_registryColumns = array('formbuilder');

    /**
     * force frontend lead form
     *
     * (non-PHPdoc)
     * @see JInboundAdminModel::getForm()
     */
    public function getForm($data = array(), $loadData = true)
    {
        $app        = JFactory::getApplication();
        $fieldtypes = array(
            'select' => 'list'
        );
        // Get the form.
        $form = $this->loadForm(JInbound::COM . '.lead_front', 'lead_front',
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        // get the fields attached to the form associated with this page
        $formid = intval($this->getItem($app->input->get('page_id', 0, 'int'))->formid);
        // load our fields with the helper lib
        $fields = JInboundHelperForm::getFields($formid);
        // don't bother continuing if we have no fields
        if (empty($fields)) {
            return $form;
        }
        // now that we have the fields, we need to add them to the form
        $this->addFieldsToForm($fields, $form, JText::_('COM_JINBOUND_FIELDSET_LEAD'));
        // return the form
        return $form;
    }

    public function addFieldsToForm(&$fields, &$form, $label)
    {
        // our custom fields should not have the following as extras
        $banned = array('name', 'type', 'default', 'label', 'description', 'class', 'classname');
        // now that we have the fields, we need to add them to the form
        // we do this by creating a new JXMLElement and passing it to JForm::load
        $xml = new JXMLElement('<form></form>');
        // add a new "fields" element named "lead" to hold them
        $xmlFields = $xml->addChild('fields');
        $xmlFields->addAttribute('name', 'lead');
        // next up, we create a fieldset (and we'll name it based on the form)
        $xmlFieldset = $xmlFields->addChild('fieldset');
        $xmlFieldset->addAttribute('name', 'lead');
        $xmlFieldset->addAttribute('label', $label);
        // finally, we loop through each of our fields and create elements for them
        foreach ($fields as $field) {
            $thisbanned = array_merge(array(), $banned);
            // start our xml field
            $xmlField = $xmlFieldset->addChild('field');
            $xmlField->addAttribute('name', $field->name);
            $xmlField->addAttribute('type', $field->type);
            $xmlField->addAttribute('default', $field->default);
            $xmlField->addAttribute('label', $field->title);
            $xmlField->addAttribute('description', $field->description);
            $classes = array();
            // special case for emails
            if (($isEmail = ('email' === $field->name || 'email' === $field->type))) {
                $xmlField->addAttribute('validate', 'email');
                $classes[]    = 'validate-email';
                $thisbanned[] = 'validate';
            }
            // handle class
            if (array_key_exists('classname', $field->params) && !empty($field->params['classname'])) {
                $parts   = explode(' ', $field->params['classname']);
                $classes = array_merge($classes, $parts);
            }
            if (!empty($classes)) {
                $xmlField->addAttribute('class', implode(' ', $classes));
            }
            // required fields
            if (array_key_exists('required', $field->params) && is_numeric(trim($field->params['required']))) {
                $xmlField->addAttribute('required', $field->params['required']);
                $thisbanned[] = 'required';
            }

            // some jinbound-specific attributes
            $transpose = false;
            $mirror    = true;
            $blank     = false;

            // handle extra attributes
            if (array_key_exists('attrs', $field->params)
                && !empty($field->params['attrs'])
                && is_array($field->params['attrs'])) {
                // loop keys
                foreach ($field->params['attrs'] as $key => $value) {
                    if (empty($key) || in_array($key, $thisbanned)) {
                        continue;
                    }
                    switch ($key) {
                        case 'transpose':
                        case 'mirror':
                            $$key = ('true' == strtolower($value) || '1' == "$value" || 'yes' == strtolower($value));
                            break;
                        case 'blank':
                            $blank = $value;
                            break;
                    }
                    // set attribute
                    $xmlField->addAttribute($key, $value);
                    $thisbanned[] = $key;
                }
            }
            // handle options, if any
            if (array_key_exists('opts', $field->params)
                && !empty($field->params['opts'])
                && is_array($field->params['opts'])) {
                if ($blank) {
                    $xmlOption = $xmlField->addChild('option', JText::_($blank));
                    $xmlOption->addAttribute('value', '');
                }
                // loop keys
                foreach ($field->params['opts'] as $key => $value) {
                    if (empty($key)) {
                        continue;
                    }
                    // set attribute
                    $xmlOption = $xmlField->addChild('option', ($transpose ? $key : $value));
                    $xmlOption->addAttribute('value', ($transpose ? $value : $key));
                }
            }
        }
        // BUGFIX we removed the necessary plugin trigger for allowing alterations
        JDispatcher::getInstance()->trigger('onJinboundFormbuilderDisplay', array(&$xml));
        // ok, we should have enough now to add to the form
        $form->load($xml, false);
        // we have to repopulate the form data now so our custom form fields get populated with data
        $formData = $this->loadFormData();
        $form->bind($formData);
    }

    protected function loadFormData()
    {
        if (!property_exists($this, 'data')) {
            $this->data = null;
        }
        if (empty($this->data)) {
            $this->data = new stdClass;
            $app        = JFactory::getApplication();
            // Override the base user data with any data in the session.
            $temp = (array)$app->getUserState('com_jinbound.page.data', array());
            foreach ($temp as $k => $v) {
                $this->data->$k = $v;
            }
        }
        return $this->data;
    }
}
