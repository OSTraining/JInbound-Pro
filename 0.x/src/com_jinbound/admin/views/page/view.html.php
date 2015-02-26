<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');
JInbound::registerHelper('filter');
JInbound::registerHelper('form');

class JInboundViewPage extends JInboundItemView
{
	function display($tpl = null, $safeparams = false)
	{
		$model    = JInboundBaseModel::getInstance('Forms', 'JInboundModel');
		$forms    = $model->getItems();
		$tags     = array();
		$defaults = '';
		foreach (array('heading', 'subheading', 'maintext', 'sidebartext', 'image', 'form', 'form:open', 'form:close') as $default)
		{
			$defaults .= "<li>{{$default}}</li>";
		}
		
		if (!empty($forms))
		{
			foreach ($forms as $form)
			{
				// start the list
				$out = '<ul>';
				// add the defaults
				$out .= $defaults;
				$fields = JInboundHelperForm::getFields($form->id);
				if (!empty($fields))
				{
					foreach ($fields as $field)
					{
						$out .= '<li>{form:' . JInboundHelperFilter::escape($field->name) . '}</li>';
					}
				}
				$out .= '<li>{submit}</li></ul>';
				$tags[$form->id] = $out;
			}
		}
		
		$this->layouttags = $tags;
		
		return parent::display($tpl, $safeparams);
	}
}