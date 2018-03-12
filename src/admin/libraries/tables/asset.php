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

/**
 * This is a base class for backwards compat
 *
 * @deprecated v2.1.8
 */
class JInboundAssetTable extends JInboundTable
{
    public $asset_id;

    /**
     * Our compat method
     *
     * @param JTable $table
     * @param string $id
     *
     * @return int
     * @deprecated v2.1.8
     */
    protected function _compat_getAssetParentId(JTable $table = null, $id = null)
    {
        /** @var JTableAsset $asset */
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jinbound');
        return $asset->id;
    }

    /**
     * @param JTable $table
     * @param string $id
     *
     * @return int
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        return $this->_compat_getAssetParentId($table, $id);
    }
}
