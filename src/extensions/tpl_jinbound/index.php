<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Document\HtmlDocument;

defined('_JEXEC') or die();

/**
 * @var HtmlDocument $this
 * @var string       $directory
 * @var string       $filename
 * @var string       $contents
 */

if (!defined('JINB_LOADED')) {
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
$templateOverride = __DIR__ . '/override.php';
if (is_file($templateOverride)) {
    include $templateOverride;
}

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
            <?php endif;

            if ($this->countModules('debug')) : ?>
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
