<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldJinboundCategorylist extends JFormField {

	protected $type = 'JinboundCategorylist';


	public function getInput() {
		$ret = '';

		$ret .= '<select id="jform_category" name="jform[category]">';

		$default = $this->form->getValue('category');

		$db = JFactory::getDbo();

		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('id, name')
			->from('#__jinbound_categories AS Category')
			;

		$db->setQuery($query);
		$results = $db->loadRowList();

		foreach($results as $result) {
			if($result['0']==$default) {
				$ret .= '<option value="'.$result['0'].'" selected="selected">'.$result['1'].'</option>';
			} else {
				$ret .= '<option value="'.$result['0'].'">'.$result['1'].'</option>';
			}
		}




		$ret .= '</select>';

		return $ret;
	}



}