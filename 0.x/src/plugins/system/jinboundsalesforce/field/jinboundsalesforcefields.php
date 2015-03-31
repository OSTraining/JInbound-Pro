<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundsalesforcefields extends JFormFieldList
{
	public $type = 'Jinboundsalesforcefields';
	
	protected function getOptions()
	{
		$options = array();
		JFactory::getLanguage()->load('plg_system_jinboundsalesforce.sys', JPATH_ADMINISTRATOR);
		JDispatcher::getInstance()->trigger('onJInboundSalesforceFields', array(&$options));
		return array_merge(parent::getOptions(), $options);
	}
}
