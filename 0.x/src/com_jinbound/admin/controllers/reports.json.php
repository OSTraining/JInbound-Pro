<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundBaseController', 'controllers/basecontroller');

class JInboundControllerReports extends JInboundBaseController
{
	public function plot()
	{
		$model = $this->getModel('Reports');
		try
		{
			$data['hits']        = $model->getLandingPageHits();
			$data['leads']       = $model->getLeadsByCreationDate();
			$data['conversions'] = $model->getConversionsByDate();
		}
		catch (Exception $e)
		{
			$this->send403($e);
		}
		// TODO the rest
		$this->_json($data);
	}
	
	private function _getDateTimeFromInput($string)
	{
		try
		{
			$date = new DateTime(JFactory::getApplication()->input->get($string, ''));
		}
		catch (Exception $e)
		{
			return false;
		}
		return $date;
	}
	
	private function send403(Exception $exception)
	{
		if (!headers_sent())
		{
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' 403 Forbidden');
		}
		$this->_json(array('error' => $exception->getMessage()));
	}
	
	private function _json($data, $headers = true)
	{
		if ($headers)
		{
			header('Content-Type: application/json');
		}
		echo json_encode($data);
		die;
	}
}
