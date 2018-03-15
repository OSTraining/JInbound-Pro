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

if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

require_once JPATH_COMPONENT . '/include.php';

$app = JFactory::getApplication();

if (JInboundHelperForm::needsMigration()) {
    if ($app->input->getCmd('format') !== 'json'
        && $app->input->getCmd('task') !== 'forms.migrate'
    ) {
        JFactory::getApplication()->enqueueMessage(JInboundHelperForm::getMigrationWarning(), 'warning');
    }

} else {
    if (JInboundHelperForm::needsDefaultFields()) {
        JInboundHelperForm::installDefaultFields();
        JInboundHelperForm::installDefaultForms();
    }
}

$controller = JControllerLegacy::getInstance('JInbound');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
