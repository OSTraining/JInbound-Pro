<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundBaseView', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/views/baseview.php');

class JInboundRSSView extends JInboundBaseView
{
	public $url;
	public $feed;
	
	public $showTitle = true;
	public $showDescription = true;
	public $showDetails = true;
	public $feedLimit = 5;
	public $wordLimit = 140;
	
	function display($tpl = null, $echo = true) {
		$this->feed = $this->getFeed($this->url);
		return parent::display($tpl);
	}
	
	/**
	 * Method to load the feed html
	 * 
	 */
	public function getFeed($url, $cacheTime = 900) {
		if (empty($url)) {
			return false;
		}
		//  get RSS parsed object
		$options = array('rssUrl' => $url);
		$cacheDir = JPATH_BASE . '/cache';
		if (is_writable($cacheDir)) {
			$options['cache_time'] = $cacheTime;
		}
		
		// getXMLParser is deprecated
		if (method_exists('JFactory', 'getXMLParser')) {
			$rssDoc =& JFactory::getXMLParser('RSS', $options);
		}
		else {
			// JFactory::getFeedParser()
			$rssDoc =& JFactory::getFeedParser($options['rssUrl'], array_key_exists('cache_time', $options) ? $options['cache_time'] : $cacheTime);
		}
		
		$this->feed = $rssDoc;
		
		return $this->feed;
	}
}
