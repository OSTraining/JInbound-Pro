<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('text');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundFieldView', 'views/fieldview');

class JFormFieldJinboundIconText extends JFormFieldText
{
	protected $type = 'Jinboundicontext';
	
	protected function getInput() {
		$view = $this->getView();
		$view->input = parent::getInput();
		$view->icon  = $this->element['icon'] ? JURI::root() . '/' . $this->element['icon'] : JInboundHelperUrl::media() . '/images/icontext-notfound.png';
		return $view->loadTemplate();
	}
	
	/**
	 * gets a new instance of the base field view
	 * 
	 * @return JInboundFieldView
	 */
	protected function getView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/icontext');
		$view = new JInboundFieldView($viewConfig);
		return $view;
	}
}