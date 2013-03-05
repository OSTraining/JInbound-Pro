<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldJinboundCampaignlist extends JFormField {

	protected $type = 'JinboundCampaignlist';


	public function getInput() {
		$ret = '';

		$ret .= '<select id="jform_campaign" name="jform[campaign_id]">';

		$default = $this->form->getValue('campaign');

		$db = JFactory::getDbo();

		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select('id, name')
			->from('#__jinbound_campaigns AS Category')
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