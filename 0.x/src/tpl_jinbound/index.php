<?php
/**
 * @package		JInbound
 * @subpackage	tpl_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$app = JFactory::getApplication();

jimport('joomla.filesystem.file');
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (!JFile::exists($helper)) {
	die('JInbound not installed!!!');
}
require_once $helper;

$option = $app->input->get('option');
if ('com_jinbound' !== $option) {
	$app->redirect(JRoute::_('index.php'));
}

?><!DOCTYPE html>
<html>
	<head>
		<jdoc:include type="head" />
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
					
					<div class="row">
						<div class="span12">
							<jdoc:include type="component" />
						</div>
					</div>
					
					<div class="row">
						<div class="span12">
							<jdoc:include type="modules" name="debug" />
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</body>
</html>