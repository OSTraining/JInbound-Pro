<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.form');

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('path');
JInbound::registerHelper('url');

JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');
JInboundBaseModel::addIncludePath(JInboundHelperPath::admin('models'));

abstract class JInboundHelperForm
{
	static public function getJinboundForm($form_id, $form_options = array())
	{
		// initialise
		if (empty($form_id))
		{
			return false;
		}
		// set up JForm
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models/forms');
		try
		{
			$options = array_merge(array('control' => 'jform'), $form_options);
			$form = JForm::getInstance('jinbound_form_module_' . md5(serialize($options)), '<form><!-- --></form>', $options);
		}
		catch (Exception $e)
		{
			return false;
		}

		// get the model
		JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_jinbound/models', 'JInboundModel');
		$model  = JModelLegacy::getInstance('Page', 'JInboundModel');

		// add fields to form
		$fields = JInboundHelperForm::getFields($form_id);
		$model->addFieldsToForm($fields, $form, JText::_('COM_JINBOUND_FIELDSET_LEAD'));

		// sanity checks
		if (empty($fields) || !($form instanceof JForm))
		{
			return false;
		}
		
		return $form;
	}
	
	public static function getForm($name, $data, $asset = false) {
		// only load once
		static $loaded;
		if (is_null($loaded)) {
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
	
	public static function getField($name, $formid)
	{
		$app = JFactory::getApplication();
		$fields = static::getFields($formid);
		if (empty($fields))
		{
			if (JDEBUG)
			{
				$app->enqueueMessage('[DBG][' . __METHOD__ . '] Fields empty');
			}
			return false;
		}
		$realname = preg_replace('/^jform\[lead\]\[(.*?)\](\[\])?$/', '$1', $name);
		if (JDEBUG)
		{
			$app->enqueueMessage('[DBG][' . __METHOD__ . '] Got real name "' . htmlspecialchars($realname, ENT_QUOTES, 'UTF-8') . '" from field name "' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"');
		}
		foreach ($fields as $field)
		{
			if ($field->name == $realname)
			{
				return $field;
			}
		}
		if (JDEBUG)
		{
			$app->enqueueMessage('[DBG][' . __METHOD__ . '] Could not find field');
		}
		return false;
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
		$query = $db->getQuery(true)
			->select('Field.*')
			->from('#__jinbound_fields AS Field')
			->where('Field.published = 1')
			->group('Field.id')
			// join over Xref
			->leftJoin('#__jinbound_form_fields AS Xref ON Xref.field_id = Field.id')
			->order('Xref.ordering ASC')
			// join over form just to ensure it's published
			->leftJoin('#__jinbound_forms AS Form ON Xref.form_id = Form.id')
			->where('Form.published = 1')
		;
		if ($id)
		{
			$query->where('Xref.form_id = ' . (int) $id);
		}
		$fields = $db->setQuery($query)->loadObjectList();
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
		$new = $db->setQuery($db->getQuery(true)
			->select('id')
			->from('#__jinbound_forms')
			. ' UNION ' . $db->getQuery(true)
			->select('id')
			->from('#__jinbound_fields')
		)->loadColumn();
		// if there's old pages and no new forms/fields, migration can be run
		return !empty($old) && empty($new);
	}
	
	static public function getMigrationWarning()
	{
		return JText::sprintf('COM_JINBOUND_NEEDS_FORM_MIGRATION', JInboundHelperUrl::task('forms.migrate', false));
	}
	
	static public function getDefaultFields()
	{
		// access db
		$db = JFactory::getDbo();
		// must have these 3
		return $db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__jinbound_fields')
			->where('(name = "first_name" OR name = "last_name" OR name = "email")')
			->where('published = 1')
		)->loadObjectList();
	}
	
	static public function getAllFields()
	{
		// access db
		$db = JFactory::getDbo();
		// must have these 3
		return $db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__jinbound_fields')
		)->loadObjectList();
	}
	
	static public function needsDefaultFields()
	{
		$fields = JInboundHelperForm::getDefaultFields();
		// if there's a result, an upgrade is needed
		return count($fields) < 3;
	}
	
	static public function installDefaultForms()
	{
		$db       = JFactory::getDbo();
		$existing = JInboundBaseModel::getInstance('Forms', 'JInboundModel')->getItems();
		if (!empty($existing))
		{
			return;
		}
		$fields = $db->setQuery($db->getQuery(true)
			->select('*')->from('#__jinbound_fields')->where('published = 1')
		)->loadObjectList();
		foreach (array('simple', 'detailed') as $form)
		{
			$data = array(
				'title'      => JText::_('COM_JINBOUND_DEFAULT_FORM_' . strtoupper($form))
			,	'published'  => '1'
			,	'formfields' => array()
			);
			foreach ($fields as $field)
			{
				if ('simple' == $form && !in_array($field->name, array('first_name', 'last_name', 'email')))
				{
					continue;
				}
				$data['formfields'][] = $field->id;
			}
			JInboundBaseModel::getInstance('Form', 'JInboundModel')->save($data);
		}
	}
	
	static public function installDefaultFields()
	{
		$db = JFactory::getDbo();
		// load any existing fields by name
		$existing = JInboundHelperForm::getAllFields();
		$defaults = array(
			'first_name'   => array()
		,	'last_name'    => array()
		,	'email'        => array('type' => 'email')
		,	'website'      => array('type' => 'url')
		,	'company_name' => array()
		,	'phone_number' => array('type' => 'tel')
		,	'address'      => array('type' => 'textarea')
		,	'suburb'       => array()
		,	'state'        => array()
		,	'country'      => array()
		,	'postcode'     => array()
		);
		$required = array('first_name', 'last_name', 'email');
		// create the 3 defaults
		foreach ($defaults as $fieldname => $extra)
		{
			$exists  = false;
			foreach ($existing as $field)
			{
				if ($field->name === $fieldname)
				{
					$exists = true;
					if (!$field->published)
					{
						$db->setQuery($db->getQuery(true)
							->update('#__jinbound_fields')
							->set('published = 1')
							->where('id = ' . intval($field->id))
						)->query();
					}
					break;
				}
			}
			if ($exists)
			{
				continue;
			}
			$data = array_merge(array(
				'title'        => JText::_('COM_JINBOUND_PAGE_FIELD_' . strtoupper($fieldname))
			,	'name'         => $fieldname
			,	'type'         => 'text'
			,	'description'  => ''
			,	'published'    => 1
			,	'params'       => array(
					'attrs'      => array(
						'key'      => array()
					,	'value'    => array()
					)
				,	'opts'       => array(
						'key'      => array()
					,	'value'    => array()
					)
				,	'required'   => (int) in_array($fieldname, $required)
				,	'classname'  => 'input-block-level'
				)
			), $extra);
			if ('email' == $fieldname)
			{
				$data['params']['attrs']['key'][]  = 'validate';
				$data['params']['attrs']['value'][] = 'email';
			}
			JInboundBaseModel::getInstance('Field', 'JInboundModel')->save($data);
		}
	}
}
