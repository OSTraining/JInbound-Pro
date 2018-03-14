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
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundPageController', 'controllers/basecontrollerpage');

class JInboundControllerPage extends JInboundPageController
{
    public function edit($key = 'id', $urlVar = 'id')
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.page')) {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }
        $model  = $this->getModel('Pages', 'JInboundModel');
        $canAdd = true;
        foreach (array('categories', 'campaigns') as $var) {
            $single = JInboundInflector::singularize($var);
            $method = 'get' . ucwords($var) . 'Options';
            $$var   = $model->$method();
            // if we don't have any categories yet, warn the user
            // there's always going to be one option in this list
            if (1 >= count($$var)) {
                JFactory::getApplication()
                    ->enqueueMessage(JText::_('COM_JINBOUND_NO_' . strtoupper($var) . '_YET'), 'error');
                $canAdd = false;
            }
        }
        if (!$canAdd) {
            $this->redirect(JInboundHelperUrl::view('pages'));
            jexit();
        }
        return parent::edit($key, $urlVar);
    }

    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'set')
    {
        $set    = JFactory::getApplication()->input->get('set', 'a', 'cmd');
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        $append .= '&set=' . $set;
        return $append;
    }
}
