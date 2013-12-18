<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldJinboundCronUrl extends JFormField
{
	protected $type = 'JinboundCronUrl';
	
	/**
	 * output a link
	 * 
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput() {
		$url = JURI::root() . 'index.php?option=com_jinbound&task=cron';
		return '<a href="' . $url . '" target="_blank" style="float:left;">' . $url . '</a>';
	}
}