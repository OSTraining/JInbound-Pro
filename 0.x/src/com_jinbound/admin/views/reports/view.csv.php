<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundCsvView', 'views/csvview');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JInboundViewReports extends JInboundCsvView
{
	public function display($tpl = null, $safeparams = null) {
		$model = JInboundBaseModel::getInstance('Reports', 'JInboundModel');
		$state = $this->get('State');
		if (is_array($state) && !empty($state)) {
			foreach ($state as $key => $value) {
				$model->setState($key, $value);
			}
		}
		switch ($this->getLayout()) {
			case 'leads':
				$leads = $model->getRecentLeads();
				$data  = array();
				$extra = array();
				if (!empty($leads)) {
					foreach ($leads as &$lead) {
						$formdata = new JRegistry();
						$formdata->loadString($lead->formdata);
						$lead->formdata = $formdata->toArray();
						if (array_key_exists('lead', $lead->formdata) && is_array($lead->formdata['lead'])) {
							$extra = array_values(array_unique(array_merge($extra, array_keys($lead->formdata['lead']))));
						}
					}
				}
				if (!empty($extra)) {
					foreach ($leads as &$lead) {
						foreach ($extra as $col) {
							$value = '';
							if (array_key_exists('lead', $lead->formdata) && is_array($lead->formdata['lead']) && array_key_exists($col, $lead->formdata['lead'])) {
								$value = $lead->formdata['lead'][$col];
							}
							$lead->$col = $value;
						}
						unset($lead->formdata);
						$data[] = $lead;
					}
				}
				$this->data = $data;
				break;
			case 'pages':
				$this->data = $model->getTopLandingPages();
				break;
			default:
				JError::raiseError(400, JText::_('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND'));
				jexit();
		}
		$this->filename = $this->getLayout() . '-report';
		
		parent::display($tpl, $safeparams);
	}
}