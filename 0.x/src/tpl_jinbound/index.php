<?php
/**
 * @package		JInbound
 * @subpackage	tpl_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$app = JFactory::getApplication();

jimport('joomla.filesystem.file');

// allow an override here
$template_override = dirname(__FILE__) . '/override.php';
if (JFile::exists($template_override)) {
	include $template_override;
}

$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (!JFile::exists($helper)) {
	die('JInbound not installed!!!');
}
require_once $helper;

JInbound::registerHelper('url');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . '/media/system/css/system.css');
$document->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap.css');
$document->addStyleSheet(JInboundHelperUrl::media() . '/bootstrap/css/bootstrap-responsive.css');

?><!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<jdoc:include type="head" />
		<style>
.tip,.tooltip {
	border: 1px solid black;
	background: white;
	padding: 5px 12px 5px 12px;
}
.tooltip {
	max-width: 120px;
}
.tip .tip-title {
	font-weight: bold;
}
		</style>
	</head>
	<body>
		<div class="container" id="jinbound_component">
			<div class="row-fluid">
				<div class="span10 offset1">
				
					<div class="row">
						<div class="span12">
							<jdoc:include type="message" />
						</div>
					</div>
<?php if ($this->countModules('jinbound-header')) : ?>
					<div class="row">
						<div class="span12">
							<jdoc:include type="modules" name="jinbound-header" />
						</div>
					</div>
<?php endif; ?>
					<div class="row">
						<div class="span12">
							<jdoc:include type="component" />
						</div>
					</div>
<?php if ($this->countModules('jinbound-footer')) : ?>
					<div class="row">
						<div class="span12">
							<jdoc:include type="modules" name="jinbound-footer" />
						</div>
					</div>
<?php endif; ?>
<?php if ($this->countModules('debug')) : ?>
					<div class="row">
						<div class="span12">
							<jdoc:include type="modules" name="debug" />
						</div>
					</div>
<?php endif; ?>
					
				</div>
			</div>
		</div>
	</body>
</html>