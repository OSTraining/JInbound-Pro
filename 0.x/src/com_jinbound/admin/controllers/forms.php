<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('path');
JInbound::registerHelper('access');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

jimport('joomla.application.component.controlleradmin');

class JInboundControllerForms extends JControllerAdmin
{
	public function getModel($name='Form', $prefix = 'JInboundModel') {
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	public function saverules() {
		JInboundHelperAccess::saveRulesWithRedirect('forms');
	}
	
	public function migrate()
	{
		if (!JInboundHelperForm::needsMigration())
		{
			throw new Exception(JText::_('COM_JINBOUND_NO_FORM_MIGRATION_NEEDED'));
		}
		$db = JFactory::getDbo();
		$forms = $db->setQuery($db->getQuery(true)
			->select('id, formid, formname, formbuilder')
			->from('#__jinbound_pages')
		)->loadObjectList();
		if (empty($forms))
		{
			throw new Exception(JText::_('COM_JINBOUND_NO_FORMS_FOUND'));
		}
		
		// migrate the forms
		// TODO break this up?
		foreach ($forms as $oldform)
		{
			$fieldids = array();
			// decode the form
			$structure = json_decode($oldform->formbuilder);
			// we can't always bank on the __ordering being there (for older installs)
			// so only use if available
			if (property_exists($structure, '__ordering'))
			{
				// determine the correct field ordering
				$ordering = explode('|', $structure->__ordering);
			}
			// no __ordering? just use the keys in the order they appear
			else
			{
				$ordering = array_keys((array) $structure);
			}
			// create the fields
			foreach ($ordering as $order => $oldfield)
			{
				// skip empties
				if (!(property_exists($structure, $oldfield)
					&& is_object($structure->$oldfield)
					&& property_exists($structure->$oldfield, 'title')))
				{
					continue;
				}
				if (empty($structure->$oldfield->title))
				{
					continue;
				}
				// build the field data to be saved
				$data = array(
					'title'        => $structure->$oldfield->title
				,	'name'         => property_exists($structure->$oldfield, 'name') ? $structure->$oldfield->name : $oldfield
				,	'type'         => $structure->$oldfield->type
				,	'description'  => ''
				,	'published'    => $structure->$oldfield->enabled
				,	'params'       => array()
				);
				// check old
				$oldfieldid = $db->setQuery($db->getQuery(true)
					->select('id')
					->from('#__jinbound_fields')
					->where('name = ' . $db->quote($data['name']))
					->where('title = ' . $db->quote($data['title']))
					->where('type = ' . $db->quote($data['type']))
				)->loadResult();
				
				if ($oldfieldid)
				{
					$fieldids[] = $oldfieldid;
					continue;
				}
				
				// set attributes
				$attr = property_exists($structure->$oldfield, 'attributes') ? $structure->$oldfield->attributes : new stdClass;
				$opts = property_exists($structure->$oldfield, 'options') ? $structure->$oldfield->options : new stdClass;					
				// fix attributes
				if (!property_exists($attr, 'name'))
				{
					$attr->name = array();
				}
				if (!property_exists($attr, 'value'))
				{
					$attr->value = array();
				}
				if (property_exists($structure->$oldfield, 'required') && $structure->$oldfield->required)
				{
					$attr->name[] = 'required';
					$attr->value[] = 'true';
				}
				$data['params']['attributes'] = (array) $attr;
				$data['params']['options']    = (array) $opts;
				// save the field
				if (!JInboundBaseModel::getInstance('Field', 'JInboundModel')->save($data))
				{
					// TODO something else
					continue;
				}
				
				// fetch the newly saved field's id
				$newfieldid = $db->setQuery($db->getQuery(true)
					->select('id')
					->from('#__jinbound_fields')
					->where('name = ' . $db->quote($data['name']))
					->where('title = ' . $db->quote($data['title']))
					->where('type = ' . $db->quote($data['type']))
				)->loadResult();
				
				if ($newfieldid)
				{
					$fieldids[] = $newfieldid;
				}
			}
			// new form data
			$newform = array(
				'title'      => $oldform->formname
			,	'published'  => 1
			,	'formfields' => implode('|', $fieldids)
			);
			// save the form
			if (!JInboundBaseModel::getInstance('Form', 'JInboundModel')->save($newform))
			{
				// TODO something else
				continue;
			}
			// find the form just saved
			$newformid = $db->setQuery($db->getQuery(true)
				->select('id')
				->from('#__jinbound_forms')
				->where('title = ' . $db->quote($newform['title']))
			)->loadResult();
			
			// update the page
			$db->setQuery($db->getQuery(true)
				->update('#__jinbound_pages')
				->set('formid = ' . (int) $formid)
				->set('formname = ' . $db->quote(''))
				->set('formbuilder = ' . $db->quote(''))
			)->query();
			
		}
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_JINBOUND_FORM_MIGRATION_COMPLETE'));
		$app->redirect(JInboundHelperUrl::_(array(), false));
	}
}
