<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');

abstract class JInboundHelperForm
{
	public static function getForm($name, $data, $asset = false) {
		// only load once
		static $loaded;
		if (is_null($loaded)) {
			jimport('joomla.form.form');
			JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models/forms');
			$loaded = true;
		}
		// get our form
		// TODO pass more options?
		$form = JForm::getInstance($name, $data);
		// check the form
		if (!($form instanceof JForm)) {
			throw new Exception(JText::_('JERROR_NOT_A_FORM'));
		}
		if ($asset) {
			// get the asset data & bind it to the form
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select('id, rules')
				->from('#__assets')
				->where('name = ' . $db->Quote($asset))
			);
			$rules = $db->loadObject();
			if (!empty($rules)) {
				$form->bind(array('asset_id' => $rules->id, 'rules' => $rules->rules));
			}
		}
		// all done - return form
		return $form;
	}
	
	
	public static function getFields($id) {
		static $collection;
		if (!is_array($collection))
		{
			$collection = array();
		}
		if (!$id || -1 == $id)
		{
			return false;
		}
		if (array_key_exists("key_$id", $collection))
		{
			return $collection["key_$id"];
		}
		// go ahead and just load from the db
		$db = JFactory::getDbo();
		$fields = $db->setQuery($db->getQuery(true)
			->select('Field.*')
			->from('#__jinbound_fields AS Field')
			->where('Field.published = 1')
			->group('Field.id')
			// join over Xref
			->leftJoin('#__jinbound_form_fields AS Xref ON Xref.field_id = Field.id')
			->where('Xref.form_id = ' . (int) $id)
			->order('Xref.ordering ASC')
			// join over form just to ensure it's published
			->leftJoin('#__jinbound_forms AS Form ON Xref.form_id = Form.id')
			->where('Form.published = 1')
		)->loadObjectList();
		// adjust values
		if (!empty($fields))
		{
			foreach ($fields as &$field)
			{
				$reg = new JRegistry;
				$reg->loadString($field->params);
				$params = $reg->toArray();
				$field->params = $params;
				$field->reg = $reg;
			}
		}
		$collection["key_$id"] = $fields;
		
		return $collection["key_$id"];
	}
	
	/**
	 * Determines if old forms need to be migrated
	 * 
	 * @return boolean
	 */
	static public function needsMigration()
	{
		// access db
		$db = JFactory::getDbo();
		// old installs that need migrated will have forms in pages but not on site
		$old = $db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_pages')
			->where('formid = 0')
			->where('formbuilder <> ' . $db->quote(''))
		)->loadColumn();
		// if there's a result, an upgrade is needed
		return !empty($old);
	}
	
	static public function getMigrationWarning()
	{
		JInbound::registerHelper('url');
		return JText::sprintf('COM_JINBOUND_NEEDS_FORM_MIGRATION', JInboundHelperUrl::task('forms.migrate', false));
	}
}
