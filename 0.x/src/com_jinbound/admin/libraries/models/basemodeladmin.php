<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

**********************************************
JInbound
Copyright (c) 2012 Anything-Digital.com
**********************************************
JInbound is some kind of marketing thingy

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JInbound at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.modeladmin') or jimport('legacy.model.admin');
jimport('joomla.form.form');
jimport('joomla.form.helper');

JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/path.php');
JLoader::register('JInbound', JInboundHelperPath::helper('jinbound'));

JForm::addFormPath(JInboundHelperPath::admin('models/forms'));
JForm::addFieldPath(JInboundHelperPath::admin('models/fields'));

class JInboundAdminModel extends JModelAdmin
{
	public $option = JInbound::COM;

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm(JInbound::COM.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	public function getTable($type=null, $prefix = 'JInboundTable', $config = array()) {
		if (empty($type)) $type = $this->name;
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($id = null) {
		return parent::getItem($id);
	}

	protected function loadFormData() {
		$data = JFactory::getApplication()->getUserState(JInbound::COM.'.edit.'.strtolower($this->name).'.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache($this->option);
		parent::cleanCache('_system');
		parent::cleanCache($group, $client_id);
	}

	/**
	 * give public read access to the model's context
	 *
	 */
	public function getContext() {
		return (string) $this->_context;
	}
}
