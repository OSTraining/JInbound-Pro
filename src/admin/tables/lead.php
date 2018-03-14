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

$e = new Exception(__FILE__);
JLog::add('JInboundTableLead is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

/**
 * As of 1.1.0 this is no longer used
 *
 * @deprecated
 */
class JInboundTableLead extends JInboundTable
{
    private $_contactData;

    function __construct(&$db)
    {
        parent::__construct('#__jinbound_leads', 'id', $db);
    }

    public function load($keys = null, $reset = true)
    {
        // load
        $load = parent::load($keys, $reset);
        // convert formdata to an object
        $registry = new JRegistry;
        if (is_string($this->formdata)) {
            $registry->loadString($this->formdata);
        } else {
            if (is_array($this->formdata)) {
                $registry->loadArray($this->formdata);
            } else {
                if (is_object($this->formdata)) {
                    $registry->loadObject($this->formdata);
                }
            }
        }
        $this->formdata = $registry;
        // set data from contact
        $contact            = $this->getContact();
        $this->address      = $contact->address;
        $this->phone_number = $contact->telephone;
        $this->email        = $contact->email_to;
        $this->website      = $contact->webpage;
        $this->suburb       = $contact->suburb;
        $this->state        = $contact->state;
        $this->country      = $contact->country;
        $this->postcode     = $contact->postcode;
        // set company name from formdata
        $lead = $this->formdata->get('lead');
        if (is_object($lead) && property_exists($lead, 'company_name')) {
            $this->company = $lead->company_name;
        }
        return $load;
    }

    public function getContact()
    {
        $debug = JInbound::config("debug", 0);
        $app   = JFactory::getApplication();
        // either update or add a contact
        jimport('joomla.database.table');
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_contact/tables');
        $this->_contact = JTable::getInstance('Contact', 'ContactTable');

        if ($this->contact_id) {
            if ($debug) {
                $app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_CONTACT_ID_FOUND', $this->contact_id));
            }
            $this->_contact->load($this->contact_id);
        } else {
            if (!empty($this->_email)) {
                if ($debug) {
                    $app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_CONTACT_EMAIL_SEARCH', $this->_email));
                }
                $this->_contact->load(array('email_to' => $this->_email));
            } else {
                if (!empty($this->first_name) && !empty($this->last_name)) {
                    if ($debug) {
                        $app->enqueueMessage(JText::sprintf('COM_JINBOUND_DEBUG_CONTACT_NAME_SEARCH', $this->first_name,
                            $this->last_name));
                    }
                    $this->_contact->load(array('name' => $this->first_name . ' ' . $this->last_name));
                }
            }
        }

        return $this->_contact;
    }

    public function bind($array, $ignore = '')
    {
        $columns = $this->getFields();
        $unset   = array();
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                if (false !== array_search($key, $columns)) {
                    continue;
                }
                $var        = '_' . $key;
                $this->$var = $value;
                $unset[]    = $var;
            }
            if (!empty($unset)) {
                foreach ($unset as $var) {
                    unset($array[$var]);
                }
            }
        }
        if (isset($array['formdata'])) {
            $registry = new JRegistry;
            if (is_array($array['formdata'])) {
                $registry->loadArray($array['formdata']);
            } else {
                if (is_string($array['formdata'])) {
                    $registry->loadString($array['formdata']);
                } else {
                    if (is_object($array['formdata'])) {

                    }
                }
            }
            $array['formdata'] = (string)$registry;
        }
        return parent::bind($array, $ignore);
    }

    public function check()
    {
        // set empty id to null
        if (empty($this->id)) {
            $this->id = null;
        }
        // validate user info
        if ('' == trim($this->first_name) && '' == trim($this->last_name)) {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_PLEASE_ENTER_YOUR_NAME'));
            return false;
        }
        $email = empty($this->email) ? $this->_email : $this->email;
        if ('' == trim($email) || !JMailHelper::isEmailAddress($email)) {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_VALID_MAIL'));
            return false;
        }
        return true;
    }

    /**
     * override to save a contact with this lead
     *
     * (non-PHPdoc)
     * @see JInboundTable::store()
     */
    public function store($updateNulls = false)
    {
        $app   = JFactory::getApplication();
        $isNew = empty($this->id);
        foreach (array(
                     'address',
                     'company',
                     'email',
                     'phone_number',
                     'website',
                     'suburb',
                     'state',
                     'country',
                     'postcode'
                 ) as $col) {
            if (property_exists($this, $col)) {
                unset($this->$col);
            }
        }

        // add ip to new records
        if ($isNew && array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        // check status for this lead
        if (empty($this->status_id)) {
            $this->_db->setQuery($this->_db->getQuery(true)
                ->select($this->_db->quoteName('id'))
                ->from('#__jinbound_lead_statuses')
                ->where($this->_db->quoteName('default') . ' = 1')
                ->where($this->_db->quoteName('published') . ' = 1')
            );
            try {
                $this->status_id = (int)$this->_db->loadResult();
            } catch (Exception $e) {
                $this->status_id = 0;
            }
        }

        $store = parent::store();
        if ($store) {
            // get the category id for jinbound contacts
            $this->_db->setQuery($this->_db->getQuery(true)
                ->select('id')
                ->from('#__categories')
                ->where($this->_db->quoteName('extension') . ' = ' . $this->_db->quote('com_contact'))
                ->where($this->_db->quoteName('published') . ' = 1')
                ->where($this->_db->quoteName('note') . ' = ' . $this->_db->quote('com_jinbound'))
            );
            try {
                $catid = $this->_db->loadResult();
            } catch (Exception $e) {
                $app->enqueueMessage(JText::_('COM_JINBOUND_NO_CONTACT_CATEGORY'), 'error');
                return $store;
            }
            // either update or add a contact
            $contact = $this->getContact();

            $bind = array(
                'name'       => $this->first_name . ' ' . $this->last_name
            ,
                'address'    => $this->_address
            ,
                'suburb'     => $this->_suburb
            ,
                'state'      => $this->_state
            ,
                'country'    => $this->_country
            ,
                'postcode'   => $this->_postcode
            ,
                'telephone'  => $this->_phone_number
            ,
                'email_to'   => $this->_email
            ,
                'webpage'    => $this->_website
            ,
                'catid'      => $catid
            ,
                'published'  => $this->published
            ,
                'xreference' => $this->id
            ,
                'language'   => '*'
            );

            // before saving contact be sure to load the contact language file
            JFactory::getLanguage()->load('com_contact', JPATH_ADMINISTRATOR);

            if (!$contact->bind($bind)) {
                $this->setError(JText::_($contact->getError()));
                return false;
            }

            if (!$contact->check()) {
                $this->setError(JText::_($contact->getError()));
                return false;
            }

            if (!$contact->store()) {
                $this->setError(JText::_($contact->getError()));
                return false;
            }

            $this->contact_id = $contact->id;
            $k                = $this->_tbl_key;

            if ($this->$k) {
                $stored = $this->_db->updateObject($this->_tbl, $this, $k, $updateNulls);
            } else {
                $stored = $this->_db->insertObject($this->_tbl, $this, $k);
            }
            // add a subscriptions value, if one doesn't exist
            $this->_db->setQuery($this->_db->getQuery(true)
                ->select('id')
                ->from('#__jinbound_subscriptions')
                ->where('contact_id = ' . (int)$this->contact_id)
            );
            try {
                $sub = $this->_db->loadResult();
            } catch (Exception $e) {
                $this->setError($e->getMessage());
                return false;
            }
            if (empty($sub)) {
                $this->_db->setQuery($this->_db->getQuery(true)
                    ->insert('#__jinbound_subscriptions')
                    ->columns(array('contact_id', 'enabled'))
                    ->values((int)$this->contact_id . ', 1')
                );
                try {
                    $this->_db->query();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }
        return $store;
    }
}
