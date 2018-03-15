<?php
/**
 * @package             jInbound
 * @subpackage          tpl_jinbound
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

$app = JFactory::getApplication();

jimport('joomla.filesystem.file');

// allow an override here
$template_override = dirname(__FILE__) . '/override.php';
if (JFile::exists($template_override)) {
    include $template_override;
}

if (!defined('JINP_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    } else {
        throw new Exception('jInbound not installed');
    }
}

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'templates/jinbound/css/jinbound.css');
$document->addStyleSheet(JUri::root() . 'media/system/css/system.css');
$document->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap.css');
$document->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap-responsive.css');

?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="head"/>
</head>
<body>
<div class="container" id="jinbound_component">
    <div class="row-fluid">
        <div class="span10 offset1">

            <div class="row">
                <div class="span12">
                    <jdoc:include type="message"/>
                </div>
            </div>
            <?php if ($this->countModules('jinbound-header')) : ?>
                <div class="row">
                    <div class="span12">
                        <jdoc:include type="modules" name="jinbound-header"/>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="span12">
                    <jdoc:include type="component"/>
                </div>
            </div>
            <?php if ($this->countModules('jinbound-footer')) : ?>
                <div class="row">
                    <div class="span12">
                        <jdoc:include type="modules" name="jinbound-footer"/>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->countModules('debug')) : ?>
                <div class="row">
                    <div class="span12">
                        <jdoc:include type="modules" name="debug"/>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
