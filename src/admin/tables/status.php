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

class JInboundTableStatus extends JInboundAssetTable
{

    function __construct(&$db)
    {
        parent::__construct('#__jinbound_lead_statuses', 'id', $db);
    }

    /**
     * Redefined asset name, as we support action control
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jinbound.status.' . (int)$this->$k;
    }

    /**
     * We provide our global ACL as parent
     *
     * @see JTable::_getAssetParentId()
     */
    protected function _compat_getAssetParentId($table = null, $id = null)
    {
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jinbound.status');
        return $asset->id;
    }
}
