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
			$state = $model->getState();
			$start = $state->get('filter.start', null);
			$end   = $state->get('filter.end', null);
			$data['hits']        = $model->getLandingPageHits($start, $end);
			$data['leads']       = $model->getLeadsByCreationDate($start, $end);
			$data['conversions'] = $model->getConversionsByDate($start, $end);
			foreach ($data['leads'] as $i => $lead)
			{
				unset($data['leads'][$i]->tracks);
			}
		}
		catch (Exception $e)
		{
			$this->send403($e);
		}
		// TODO the rest
		$this->_json($data);
	}
	
	public function glance()
	{
		$model = $this->getModel('Reports');
		try
		{
			$state = $model->getState();
			$start = $state->get('filter.start', null);
			$end   = $state->get('filter.end', null);
			$hits        = $model->getLandingPageHits($start, $end);
			$leads       = $model->getLeadsByCreationDate($start, $end);
			$conversions = $model->getConversionsByDate($start, $end);
			// initial data
			$data = array(
				'views'            => 0
			,	'leads'            => 0
			,	'views-to-leads'   => 0
			,	'conversion-count' => 0
			,	'conversion-rate'  => 0
			,	'__raw'   => array(
					'hits'  => $hits
				,	'leads' => $leads
				,	'conversions' => $conversions
				,	'start' => $start
				,	'end' => $end
				)
			);
			// add values
			foreach ($hits as $hit)
			{
				$data['views'] += (int) $hit[1];
			}
			foreach ($leads as $lead)
			{
				$data['leads'] += (int) $lead[1];
			}
			foreach ($conversions as $conversion)
			{
				$data['conversion-count'] += (int) $conversion[1];
			}
			// calc percents
			if (0 < $data['views']) {
				$data['views-to-leads']  = ($data['leads'] / $data['views']) * 100;
				$data['conversion-rate'] = ($data['conversion-count'] / $data['views']) * 100;
			}
			$data['views-to-leads']  = number_format($data['views-to-leads'], 2) . '%';
			$data['conversion-rate'] = number_format($data['conversion-rate'], 2) . '%';
		}
		catch (Exception $e)
		{
			$this->send403($e);
		}
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
