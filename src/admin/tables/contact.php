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
JInbound::registerLibrary('JinboundTable', 'table');

class JInboundTableContact extends JInboundTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__jinbound_contacts', 'id', $db);
    }

    public function delete($pk = null)
    {
        if ($result = parent::delete($pk)) {
            $tables = array(
                '#__jinbound_contacts_campaigns'  => 'contact_id',
                '#__jinbound_conversions'         => 'contact_id',
                '#__jinbound_contacts_statuses'   => 'contact_id',
                '#__jinbound_contacts_priorities' => 'contact_id',
                '#__jinbound_emails_records'      => 'lead_id',
                '#__jinbound_notes'               => 'lead_id',
                '#__jinbound_subscriptions'       => 'contact_id'
            );

            $db = $this->getDbo();
            foreach ($tables as $table => $key) {
                $db->setQuery(
                    $db->getQuery(true)
                        ->delete($table)
                        ->where($db->quoteName($key) . ' = ' . $this->id)
                )
                    ->execute();
            }

            return true;
        }

        return false;
    }

    /**
     * Redefined asset name, as we support action control
     *
     * @return string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jinbound.contact.' . (int)$this->$k;
    }

    /**
     * @param JTable $table
     * @param null   $id
     *
     * @return int
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        /** @var JTableAsset $asset */
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jinbound.contacts');
        return $asset->id;
    }
}
