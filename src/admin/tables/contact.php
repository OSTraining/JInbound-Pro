<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundAssetTable', 'tables/asset');

class JInboundTableContact extends JInboundAssetTable
{
    function __construct(&$db)
    {
        parent::__construct('#__jinbound_contacts', 'id', $db);
    }

    public function delete($pk = null)
    {
        // run delete
        $result = parent::delete($pk);
        // no deletion? just return
        if (!$result) {
            return $result;
        }
        $tables = array(
            // contacts have campaigns
            '#__jinbound_contacts_campaigns'  => 'contact_id'
            // contacts have conversions
        ,
            '#__jinbound_conversions'         => 'contact_id'
            // contacts have statuses
        ,
            '#__jinbound_contacts_statuses'   => 'contact_id'
            // contacts have priorities
        ,
            '#__jinbound_contacts_priorities' => 'contact_id'
            // contacts have email records
        ,
            '#__jinbound_emails_records'      => 'lead_id'
            // contacts have notes
        ,
            '#__jinbound_notes'               => 'lead_id'
            // contacts have subscriptions
        ,
            '#__jinbound_subscriptions'       => 'contact_id'
        );
        foreach ($tables as $table => $key) {
            $this->_db->setQuery($this->_db->getQuery(true)
                ->delete($table)
                ->where($this->_db->quoteName($key) . ' = ' . $this->id)
            )->query();
        }
    }

    /**
     * Redefined asset name, as we support action control
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jinbound.contact.' . (int)$this->$k;
    }

    /**
     * We provide our global ACL as parent
     *
     * @see JTable::_getAssetParentId()
     */
    protected function _compat_getAssetParentId($table = null, $id = null)
    {
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jinbound.contact');
        return $asset->id;
    }
}
