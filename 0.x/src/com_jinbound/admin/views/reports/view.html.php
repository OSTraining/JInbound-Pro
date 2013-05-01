<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JInboundViewReports extends JInboundListView
{
	public function getRecentLeads() {
		return $this->_callModelMethod('getRecentLeads');
	}
	
	public function getVisitCount() {
		return $this->_callModelMethod('getVisitCount');
	}
	
	public function getLeadCount() {
		return $this->_callModelMethod('getLeadCount');
	}
	
	public function getTopLandingPages() {
		return $this->_callModelMethod('getTopLandingPages');
	}
	
	public function getConversionCount() {
		return $this->_callModelMethod('getConversionCount');
	}
	
	public function getConversionRate() {
		return $this->_callModelMethod('getConversionRate');
	}
	
	private function _callModelMethod($method) {
		$model = JInboundBaseModel::getInstance('Reports', 'JInboundModel');
		return $model->$method();
	}
}
