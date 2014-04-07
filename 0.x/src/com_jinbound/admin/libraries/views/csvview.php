<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.pane');

JLoader::register('JInboundBaseView', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/views/baseview.php');

class JInboundCsvView extends JInboundBaseView
{
	public function display($tpl = null, $safeparams = null) {
		$data = array();
		if (property_exists($this, 'data')) {
			$data = $this->data;
		}
		$fileName = $this->_name;
		if (property_exists($this, 'filename')) {
			$fileName = $this->filename;
		}
		$date = new DateTime();
		$date = $date->format('Y-m-d');
		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$fileName-$date.csv\";" );
		header("Content-Transfer-Encoding: binary");
		
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		
		if (empty($data)) {
			jexit();
		}
		
		$headers = array_keys(get_object_vars($data[0]));
		echo '"' . implode('","', $headers) . '"';
		
		foreach ($data as $item) {
			$cols = array();
			foreach ($headers as $col) {
				$cols[] = $item->$col;
			}
			echo "\n" . '"' . implode('","', $cols) . '"';
		}
		
		jexit();
	}
}