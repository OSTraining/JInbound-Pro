<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (JInbound::version()->isCompatible('3.0.0'))
{
	echo $this->loadTemplate('list_default');
}
else
{
	$this->cols = 6;
	echo $this->loadTemplate('list');
}
