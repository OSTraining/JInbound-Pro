<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundmailchimp
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');

class plgSystemJInboundmailchimp extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_system_jinboundmailchimp', JPATH_ADMINISTRATOR);
		$this->loadLanguage('plg_system_jinboundmailchimp.sys', JPATH_ADMINISTRATOR);
	}
	
	public function onAfterInitialise()
	{
		if (JFactory::getApplication()->isSite())
		{
			return;
		}
		$option = array_key_exists('option', $_REQUEST) ? $_REQUEST['option'] : '';
		$view = array_key_exists('view', $_REQUEST) ? $_REQUEST['view'] : '';
		if ('plg_system_jinboundmailchimp' === $option && 'liveupdate' === $view)
		{
			require_once JPATH_ROOT . '/plugins/system/jinboundmailchimp/liveupdate/liveupdate.php';
			$updateInfo = LiveUpdate::getUpdateInformation();
			if ($updateInfo->hasUpdates) {
				echo JText::sprintf('PLG_SYSTEM_JINBOUNDMAILCHIMP_UPDATE_HASUPDATES', $updateInfo->version);
			}
			jexit();
		}
	}
	
	public function onJinboundDashboardUpdate()
	{
		return "index.php?option=plg_system_jinboundmailchimp&view=liveupdate";
	}
	
	public function onContentPrepareForm($form)
	{
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		switch ($form->getName())
		{
			case 'com_jinbound.campaign':
				$file = 'jinboundmailchimp';
				break;
			case 'com_jinbound.contact':
				if (JFactory::getApplication()->isSite())
				{
					return true;
				}
				$file = 'jinboundmailchimpcontact';
				break;
			default: return true;
		}
		JForm::addFormPath(dirname(__FILE__) . '/form');
		JForm::addFieldPath(dirname(__FILE__) . '/field');
		$result = $form->loadFile($file, false);
		return $result;
	}
	
	public function onJInboundChangeState($context, $campaign_id, $contacts, $status_id)
	{
		if ('com_jinbound.contact.status' !== $context)
		{
			return;
		}
		require_once realpath(dirname(__FILE__).'/library/helper.php');
		$helper = new JinboundMailchimp(array('params' => $this->params));
		foreach ($contacts as $contact_id)
		{
			$helper->onJinboundSetStatus($status_id, $campaign_id, $contact_id);
		}
	}
}
