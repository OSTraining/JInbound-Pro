<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
 @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('editor');

class JFormFieldJinboundEditor extends JFormFieldEditor
{
	public $type = 'JinboundEditor';
	
	public function getTags() {
		// Initialize variables.
		$tags = array();
	
		foreach ($this->element->children() as $tag) {
			// Only add <tag /> elements.
			if ($tag->getName() != 'tag') {
				continue;
			}
			
			// Create a new option object based on the <option /> element.
			$tmp = new stdClass;
			$tmp->value = (string) $tag['value'];
			
			// Set some option attributes.
			$tmp->class = (string) $tag['class'];
			
			// Add the option object to the result set.
			$tags[] = $tmp;
		}
		
		reset($tags);
		
		return $tags;
	}
	
	/**
	 * This method is used in the form display to show extra data
	 *
	 */
	public function getSidebar() {
		$view = $this->getView();
		// set data
		$view->input = $this;
		// return template html
		return $view->loadTemplate();
	}
	
	/**
	 * gets a new instance of the base field view
	 *
	 * @return JInboundFieldView
	 */
	protected function getView() {
		$viewConfig = array('template_path' => dirname(__FILE__) . '/editor');
		$view = new JInboundFieldView($viewConfig);
		return $view;
	}
}