<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundmailchimp
@ant_copyright_header@
 */

defined('_JEXEC') or die;

jimport('fof.include');

class JinboundMailchimp
{
	private $name = 'mailchimp';
	private $mcApi;
	private $delete_member = false;
	private $send_goodbye = true;
	private $send_notify = true;
	private $email_type = 'html';
	private $double_optin = true;
	private $send_welcome = false;
	protected $customFields = array();
	// MC groups
	protected $addMCGroups = array();
	protected $removeMCGroups = array();
	protected $groupingsGroupMap = array();
	protected $groupingsListMap = array();
	protected $groupingsGroupName = array();

	public function __construct($config = array())
	{
		// Load the MailChimp library
		require_once dirname(__FILE__).'/MCAPI.class.php';
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/tables');

		$configParams = @json_decode($config['params']);
		$apiKey = $configParams->mailchimp_key;
		$this->mcApi = new MCAPI($apiKey);
		$this->delete_member = $configParams->delete_member;
		$this->send_goodbye = $configParams->send_goodbye;
		$this->send_notify = $configParams->send_notify;
		$this->email_type = $configParams->email_type;
		$this->double_optin = $configParams->double_optin;
		$this->send_welcome = $configParams->send_welcome;

		$this->addGroups = array();
		$this->removeGroups = array();

		// Load custom fields
		$this->loadCustomFieldsAssignments();

		// Load MC group assignments
		$this->loadMCGroupAssignments();
	}

	protected function loadMCGroupAssignments()
	{
		$this->addMCGroups = array();
		$this->removeMCGroups = array();
		$db = JFactory::getDbo();
		$levels = $db->setQuery('SELECT id, params FROM #__jinbound_campaigns')->loadObjectList();
		$addMCGroupsKey = strtolower($this->name).'_addmcgroups';
		$removeMCGroupsKey = strtolower($this->name).'_removemcgroups';
		if(!empty($levels)) {
			foreach($levels as $level)
			{
				if(is_string($level->params)) {
					$level->params = @json_decode($level->params);
					if(empty($level->params)) {
						$level->params = new stdClass();
					}
				} elseif(empty($level->params)) {
					continue;
				}
				if(property_exists($level->params, $addMCGroupsKey))
				{
					$this->addMCGroups[$level->id] = array_filter($level->params->$addMCGroupsKey);
				}
				if(property_exists($level->params, $removeMCGroupsKey))
				{
					$this->removeMCGroups[$level->id] = array_filter($level->params->$removeMCGroupsKey);
				}
			}
		}
	}

	protected function loadCustomFieldsAssignments()
	{
		$this->customFields = array();
		$db = JFactory::getDbo();
		$levels = $db->setQuery('SELECT id, params FROM #__jinbound_campaigns')->loadObjectList();
		$customFieldsKey = strtolower($this->name).'_customfields';
		if(!empty($levels)) {
			foreach($levels as $level) {
				if(is_string($level->params)) {
					$level->params = @json_decode($level->params);
					if(empty($level->params)) {
						$level->params = new stdClass();
					}
				} elseif(empty($level->params)) {
					continue;
				}
				if(property_exists($level->params, $customFieldsKey)) {
					$this->customFields[$level->id] = array_filter($level->params->$customFieldsKey);
				}
			}
		}
	}

	public function onJinboundSetStatus($status_id, $campaign_id, $contact_id)
	{
		// load campaign, status, contact
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		foreach (array('campaign', 'status', 'contact') as $var)
		{
			$class = 'JInboundTable' . ucwords($var);
			require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/tables/' . $var . '.php';
			$$var = new $class($db);
			$$var->load(${$var . '_id'});
			if (property_exists($$var, 'params') && !is_a($$var->params, 'JRegistry'))
			{
				$reg = new JRegistry();
				$reg->loadString($$var->params);
				$$var->params = $reg;
			}
		}
		
		// Load lists
		$addMCLists = $campaign->params->get('addlists', array());
		$removeMCLists = $campaign->params->get('removelists', array());
		$rem = array();
		foreach ($addMCLists as $k => $v) if (empty($v)) $rem[] = $k;
		foreach ($rem as $k) unset($addMCLists[$k]);
		$rem = array();
		foreach ($removeMCLists as $k => $v) if (empty($v)) $rem[] = $k;
		foreach ($rem as $k) unset($removeMCLists[$k]);
		$this->addGroups = array($campaign->id => $addMCLists);
		$this->removeGroups = array($campaign->id => $removeMCLists);
		//$this->loadUserGroups($user_id, $addMCLists, $removeMCLists);

		// Load groups
		$addMCGroups = $campaign->params->get('addgroups', array());
		$removeMCGroups = $campaign->params->get('removegroups', array());
		$rem = array();
		foreach ($addMCGroups as $k => $v) if (empty($v)) $rem[] = $k;
		foreach ($rem as $k) unset($addMCGroups[$k]);
		$rem = array();
		foreach ($removeMCGroups as $k => $v) if (empty($v)) $rem[] = $k;
		foreach ($rem as $k) unset($removeMCGroups[$k]);
		$this->addMCGroups = array($campaign->id => $addMCGroups);
		$this->removeMCGroups = array($campaign->id => $removeMCGroups);
		//$this->loadUserGroups($user_id, $addMCGroups, $removeMCGroups, 'addMCGroups', 'removeMCGroups');
		$this->initMCGroups();
		
		if (!$status->final)
		{
			$removeMCLists = array();
			$removeMCGroups = array();
			$this->removeGroups = array();
			$this->removeMCGroups = array();
		}
		else
		{
			$addMCLists = array();
			$addMCGroups = array();
			$this->addGroups = array();
			$this->addMCGroups = array();
		}

		// Get the user's name and email
		$firstName = $contact->first_name;
		$lastName = $contact->last_name;
		$email = $contact->email;

		// Get the user's MailChimp lists
		$currentLists = $this->getEmailLists($email);

		// Get the session
		$session = JFactory::getSession();

		// Remove from MailChimp list
		if(!empty($removeMCLists)) {
			foreach($removeMCLists as $mcListToRemove) {
				if(is_array($currentLists) && in_array($mcListToRemove, $currentLists)) {
					$mcSubscribeId = $user_id . ':' . $mcListToRemove;
					$this->mcApi->listUnsubscribe(
							$mcListToRemove,
							$email,
							$this->delete_member,
							$this->send_goodbye,
							$this->send_notify);
				}
				$mcSubscribeId = $contact_id . ':' . $mcListToRemove;
				$session->clear('mailchimp.' . $mcSubscribeId, 'plg_system_jinboundmailchimp');
			}
		}

		// Add to MailChimp list
		if(!empty($addMCLists)) {
			// getting the form data to be sent to mailchimp presents a bit of a problem
			// this method only knows the contact id, the campaign id, and the status id
			// and everything else must be extrapolated from that
			// what we can do is load the fields related to the pages with the same
			// form and campaign, then check the user's conversions for that page
			// this should give us the conversion data, including the fields
			// 
			// get form field params from db first
			$fieldData = $db->setQuery($db->getQuery(true)
				->select('Field.name')
				->select('Field.params')
				->select('Page.id AS page_id')
				->select('Page.campaign')
				->from('#__jinbound_fields AS Field')
				->leftJoin('#__jinbound_form_fields AS FormField ON FormField.field_id = Field.id')
				->leftJoin('#__jinbound_pages AS Page ON Page.formid = FormField.form_id')
				->leftJoin('#__jinbound_contacts_campaigns AS ContactCampaign ON ContactCampaign.campaign_id = Page.campaign')
				->where('Page.id IS NOT NULL')
				->where('Page.campaign = ' . (int) $campaign_id)
				->group('Field.id')
			)->loadObjectList();
			if (JDEBUG)
			{
				$app->enqueueMessage('<h3>[' . __METHOD__ . '] Field Data</h3><pre>' . htmlspecialchars(print_r($fieldData, 1), ENT_QUOTES, 'UTF-8') . '</pre>');
			}
			// Build subscriber data
			$baseMergeVals = array(
				'FNAME' => $firstName
			,	'LNAME' => $lastName
			);
			// get the contact's campaigns
			if (!class_exists('JInboundHelperContact'))
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/contact.php';
			}
			// NOTE: during contact save, this ends up triggering BEFORE the conversion
			// is saved, so having no conversions at all means that it's likely
			// that this is a new contact - in order to make this work correctly,
			// we have to also check the post data with this request
			// so what we'll do is loop through the fields first, set initial data
			// from the conversions, then override using post data
			$contactConversions = JInboundHelperContact::getContactConversions($contact_id);
			$token              = $app->input->get('token', '', 'cmd');
			$rawPostParam       = 'jform';
			if (!empty($token))
			{
				$rawPostParam = preg_replace('/^(.*?)\.(.*?)\.(.*?)$/', '${1}_${3}', $token);
			}
			$rawPostData = $app->input->post->get($rawPostParam, array(), 'array');
			// loop the fields FIRST
			if (!empty($fieldData))
			{
				foreach ($fieldData as $row)
				{
					// decode params for this row
					$params = new JRegistry();
					$params->loadString($row->params);
					$mcParams = $params->get('mailchimp', false);
					// check mc params for mapped fields
					if (!(is_object($mcParams) && property_exists($mcParams, 'mapped_field')
						&& is_array($mcParams->mapped_field) && !empty($mcParams->mapped_field)))
					{
						continue;
					}
					// loop the conversions and add those
					if (!empty($contactConversions))
					{
						foreach ($contactConversions as $contactConversion)
						{
							// skip this conversion as the page is not the same
							if ($row->page_id != $contactConversion->page_id)
							{
								continue;
							}
							// this one matches, so confirm that the conversion data contains field data
							if (array_key_exists($row->name, $contactConversion->formdata['lead']))
							{
								foreach ($mcParams->mapped_field as $fieldName)
								{
									$baseMergeVals[$fieldName] = $contactConversion->formdata['lead'][$row->name];
								}
							}
						}
					}
					// now override using post data
					if (array_key_exists('lead', $rawPostData) && is_array($rawPostData['lead']) && array_key_exists($row->name, $rawPostData['lead']))
					{
						foreach ($mcParams->mapped_field as $fieldName)
						{
							$baseMergeVals[$fieldName] = $rawPostData['lead'][$row->name];
						}
					}
				}
			}
			
			// Add subscriber to lists
			foreach($addMCLists as $mcListToAdd) {
				if(! (is_array($currentLists) && in_array($mcListToAdd, $currentLists))) {
					$mcSubscribeId = $contact_id . ':' . $mcListToAdd;
					if($session->get('mailchimp.' . $mcSubscribeId, '', 'plg_system_jinboundmailchimp') != 'new') {
						// Subscribe if email is not already in the MailChimp list and
						// if the subscription is not already sent for that user (but not confirmed yet)
						$mergeVals = array_merge(array(), $baseMergeVals);
						
						if (JDEBUG)
						{
							$app->enqueueMessage('<h3>[' . __METHOD__ . '] Merge Vals</h3><pre>' . htmlspecialchars(print_r($mergeVals, 1), ENT_QUOTES, 'UTF-8') . '</pre>');
						}

						// Add MC groups to new subscription
						$groupings = array();
						if(!empty($addMCGroups)) {
							foreach($addMCGroups as $mcGroupId) {
								$groupName = str_replace(',', '\,', $this->groupingsGroupName[$mcGroupId]);
								// No group name
								if(empty($groupName)) continue;
								$groupingId = $this->groupingsGroupMap[$mcGroupId];
								// No correspnding grouping
								if(empty($groupingId)) continue;
								$listId = $this->groupingsListMap[$groupingId];
								// No correspnding list
								if(empty($listId)) continue;
								// Group not related to this list
								if($listId != $mcListToAdd) continue;
								// We passed all checks: Add the group to the array
								if(!array_key_exists($groupingId, $groupings)) {
									$groupings[$groupingId] = array();
								}
								$groupings[$groupingId][] = $groupName;
							}
						}
						// Add the new groups to the $mergeVals
						if(!empty($groupings)) {
							foreach($groupings as $groupingId => $newGroups) {
								$newGrouping = array();
								$newGrouping['id'] = $groupingId;
								$newGrouping['groups'] = implode(",", $newGroups);
								$mergeVals['GROUPINGS'][] = $newGrouping;
							}
						}
						// Subscribe to MC list
						if($this->mcApi->listSubscribe(
								$mcListToAdd,
								$email,
								$mergeVals,
								$this->email_type,
								$this->double_optin,
								true,
								false,
								$this->send_welcome)) {
							// Add new MailChimp subscription to session to avoid that MailChimp sends multiple
							// emails for one subscription (before subscription is confirmed by the user)
							$session->set('mailchimp.' . $mcSubscribeId , 'new', 'plg_system_jinboundmailchimp');
						}
					}
				}
			}
		}

		// Get the user's MailChimp lists
		$currentLists = $this->getEmailLists($email);

		// Remove MC group from existing list subscription
		if(!empty($removeMCGroups) && is_array($currentLists)) {
			foreach($removeMCGroups as $mcGroupId) {
				$groupName = str_replace(',', '\,', $this->groupingsGroupName[$mcGroupId]);
				// No group name
				if(empty($groupName)) continue;
				$groupingId = $this->groupingsGroupMap[$mcGroupId];
				// No correspnding grouping
				if(empty($groupingId)) continue;
				$listId = $this->groupingsListMap[$groupingId];
				// No correspnding list
				if(empty($listId)) continue;
				// User is not subscribed to this list
				if(!in_array($listId, $currentLists)) continue;
				// We passed all checks: Remove the group
				$this->removeMCGroup($email, $listId, $groupingId, $groupName);
			}
		}

		// Add MC group to existing list subscription
		if(!empty($addMCGroups) && is_array($currentLists)) {
			foreach($addMCGroups as $mcGroupId) {
				$groupName = str_replace(',', '\,', $this->groupingsGroupName[$mcGroupId]);
				// No group name
				if(empty($groupName)) continue;
				$groupingId = $this->groupingsGroupMap[$mcGroupId];
				// No correspnding grouping
				if(empty($groupingId)) continue;
				$listId = $this->groupingsListMap[$groupingId];
				// No correspnding list
				if(empty($listId)) continue;
				// User is not subscribed to this list
				if(!in_array($listId, $currentLists)) continue;
				// We passed all checks: Add the group
				$this->addMCGroup($email, $listId, $groupingId, $groupName);
			}
		}
	}
	
	public function getEmailLists($email)
	{
		return $this->mcApi->listsForEmail($email);
	}
	
	public function getEmailListDetails($email)
	{
		$lists = $this->mcApi->listsForEmail($email);
		if (empty($lists))
		{
			return array();
		}
		$details = array();
		foreach ($lists as $id)
		{
			$details[$id] = $this->mcApi->listMemberInfo($id, $email);
		}
		return $details;
	}

	/**
	 * =========================================================================
	 * !!! CRUFT WARNING !!!
	 * =========================================================================
	 *
	 * The following methods are leftovers from the Olden Days (before 2.4.5).
	 * At some point (most likely 2.6) they will be removed. For now they will
	 * stay here so that we can do a transparent migration.
	 */

	/**
	 * Moves this plugin's settings from the plugin into each subscription
	 * level's configuration parameters.
	 */
	protected function upgradeSettings($config = array())
	{
		//$model = FOFModel::getTmpInstance('Levels','AkeebasubsModel');
		$levels = array();//$model->getList(true);
		$addgroupsKey = strtolower($this->name).'_addgroups';
		$removegroupsKey = strtolower($this->name).'_removegroups';
		if(!empty($levels)) {
			foreach($levels as $level)
			{
				$save = false;
				if(is_string($level->params)) {
					$level->params = @json_decode($level->params);
					if(empty($level->params)) {
						$level->params = new stdClass();
					}
				} elseif(empty($level->params)) {
					$level->params = new stdClass();
				}
				if(array_key_exists($level->akeebasubs_level_id, $this->addGroups)) {
					if(empty($level->params->$addgroupsKey)) {
						$level->params->$addgroupsKey = $this->addGroups[$level->akeebasubs_level_id];
						$save = true;
					}
				}
				if(array_key_exists($level->akeebasubs_level_id, $this->removeGroups)) {
					if(empty($level->params->$removegroupsKey)) {
						$level->params->$removegroupsKey = $this->removeGroups[$level->akeebasubs_level_id];
						$save = true;
					}
				}
				if($save) {
					$level->params = json_encode($level->params);
					$result = $model->setId($level->akeebasubs_level_id)->save( $level );
				}
			}
		}

		// Remove the plugin parameters
		if(isset($config['params'])) {
			$configParams = @json_decode($config['params']);
			unset($configParams->addlists);
			unset($configParams->removelists);
			$param_string = @json_encode($configParams);

			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update($db->qn('#__extensions'))
				->where($db->qn('type').'='.$db->q('plugin'))
				->where($db->qn('element').'='.$db->q(strtolower($this->name)))
				->where($db->qn('folder').'='.$db->q('akeebasubs'))
				->set($db->qn('params').' = '.$db->q($param_string));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/*
	 * Removes a MailChimp user to a MailChimp group.
	 */
	private function removeMCGroup($userEmail, $listId, $groupingId, $groupName)
	{
		$userMCInfo = $this->mcApi->listMemberInfo($listId, $userEmail);
		$userMCData = $userMCInfo['data'][0];
		$userMergeVars = $userMCData['merges'];
		if(isset($userMergeVars['GROUPINGS']) && is_array($userMergeVars['GROUPINGS'])) {
			$groupings = $userMergeVars['GROUPINGS'];
			foreach($groupings as $key => $grouping) {
				if($groupingId == $grouping['id']) {
					$newGroups = array();
					$groupsChanged = false;
					$existingGroupsString = $grouping['groups'];
					$existingGroupsArray = $this->mcGroupsToArray($existingGroupsString);
					foreach($existingGroupsArray as $existingGroup) {
						$existingGroup = trim($existingGroup);
						if($existingGroup != $groupName) {
							// If this is not the group to be removed, add it again
							$newGroups[] = $existingGroup;
						} else {
							// The group that needs to be removed is there
							$groupsChanged = true;
						}
					}
					if($groupsChanged) {
						// Update MailChimp using the new groups
						if(empty($newGroups)) {
							$newGroupsString = '';
						} else {
							$newGroupsString = implode(",", $newGroups);
						}
						$userMergeVars['GROUPINGS'][$key]['groups'] = $newGroupsString;
						$this->mcApi->listUpdateMember($listId, $userEmail, $userMergeVars);
					}
				}
			}
		}
	}

	/*
	 * Adds a MailChimp user to a MailChimp group.
	 */
	private function addMCGroup($userEmail, $listId, $groupingId, $groupName)
	{
		$userMCInfo = $this->mcApi->listMemberInfo($listId, $userEmail);
		$userMCData = $userMCInfo['data'][0];
		$userMergeVars = $userMCData['merges'];
		if(isset($userMergeVars['GROUPINGS']) && is_array($userMergeVars['GROUPINGS'])) {
			$groupings = $userMergeVars['GROUPINGS'];
			foreach($groupings as $key => $grouping) {
				if($groupingId == $grouping['id']) {
					$newGroups = array();
					$groupsChanged = true;
					$existingGroupsString = $grouping['groups'];
					$existingGroupsArray = $this->mcGroupsToArray($existingGroupsString);
					$newGroups = $groupsArray;
					foreach($existingGroupsArray as $existingGroup) {
						$existingGroup = trim($existingGroup);
						if($existingGroup == $groupName) {
							// The group that needs to be added is already there - nothing to do
							$groupsChanged = false;
							break;
						}
					}
					if($groupsChanged) {
						// Use the existing groups, add the new one, and update MailChimp
						$newGroups = $existingGroupsArray;
						$newGroups[] = $groupName;
						$newGroupsString = implode(",", $newGroups);
						$userMergeVars['GROUPINGS'][$key]['groups'] = $newGroupsString;
						$this->mcApi->listUpdateMember($listId, $userEmail, $userMergeVars);
					}
				}
			}
		}
	}

	private function mcGroupsToArray($groupsString)
	{
		$groupsArray = array();
		$groupStringToBeParsed = $groupsString;
		while(true) {
			$pos = strpos($groupStringToBeParsed, ',');
			if(! $pos) break;
			$charBeforeComma = substr($groupStringToBeParsed, ($pos - 1), 1);
			// Check for '\,'
			if($charBeforeComma != '\\') {
				$groupsArray[] = trim(substr($groupStringToBeParsed, 0, $pos));
				$groupStringToBeParsed = trim(substr($groupStringToBeParsed, ($pos + 1)));
			}
		}
		if(!empty($groupStringToBeParsed)) {
			$groupsArray[] = $groupStringToBeParsed;
		}
		return $groupsArray;
	}

	/*
	 * Returns the MailChimp lists that exist at the MC account.
	 */
	protected function getGroups() {
		static $groups = null;

		if(is_null($groups)) {
			$groups = array();

			$start = 0;
			$limit = 100;
			$mcLists = $this->mcApi->lists(array(), $start, $limit);
			$total = $mcLists['total'];
			while(true) {
				if (is_array($mcLists['data']) && count($mcLists['data']))
				{
					foreach($mcLists['data'] as $list) {
						$listTitle = $list['name'];
						$listId = $list['id'];
						$groups[$listTitle] = $listId;
					}
				}
				if(($start + $limit) < $total) {
					$start += $limit;
					$mcLists = $this->mcApi->lists(array(), $start, $limit);
				} else {
					break;
				}
			}
		}

		return $groups;
	}

	private function initMCGroups()
	{
		$addLevels = array_keys($this->addMCGroups);
		$removeLevels = array_keys($this->removeMCGroups);
		$addAndRemoveLevels = array_merge($addLevels, $removeLevels);
		$allLevels = array_unique($addAndRemoveLevels);
		foreach($allLevels as $levelId) {
			$this->getMCGroups($levelId);
		}
	}

	/*
	 * Returns the MailChimp groups that exist at the MC account.
	 */
	private function getMCGroups($levelId)
	{
		static $mcGroups = array();

		if(! array_key_exists($levelId, $mcGroups)) {
			$mcGroups[$levelId] = array();
		}

		if(empty($mcGroups[$levelId])) {
			$mcLists = $this->getGroups();
			foreach($mcLists as $listTitle => $listId) {
				if(array_key_exists($levelId, $this->addGroups) && in_array($listId, $this->addGroups[$levelId])) {
					$interestGroupings = $this->mcApi->listInterestGroupings($listId);
					if($interestGroupings) {
						foreach($interestGroupings as $groupings) {
							$groupingsId = $groupings['id'];
							$groupingsName = trim($groupings['name']);
							// Add to grouping-list map
							$this->groupingsListMap[$groupingsId] = $listId;
							foreach($groupings['groups'] as $g) {
								$groupName = trim($g['name']);
								$groupName = trim(preg_replace('/<[^>]+>/', "", $groupName));
								$title =  $groupName . ' ( ' . $groupingsName . ' - ' . $listTitle . ' )';
								$id = md5($title);
								$mcGroups[$levelId][$title] = $id;
								// Add to grouping-group map
								$this->groupingsGroupMap[$id] = $groupingsId;
								// Add group name
								$this->groupingsGroupName[$id] = $g['name'];
							}
						}
					}
				}
			}
		}

		return $mcGroups[$levelId];
	}

	/*
	 * Return the custom fields for this subscription level.
	 */
	protected function getCustomFields($levelId)
	{
		static $customFields = array();

		if(empty($customFields[$levelId])) {
			$customFields[$levelId] = array();
			$items = FOFModel::getTmpInstance('Customfields','AkeebasubsModel')
				->enabled(1)
				->getItemList(true);

			// Loop through the items
			foreach($items as $item) {
				if($item->show == 'all' || $item->akeebasubs_level_id == $levelId) {
					$customFields[$levelId][$item->title] = $item->akeebasubs_customfield_id;
				}
			}
		}

		return $customFields[$levelId];
	}

	/*
	 * Return the custom fields as a HTML select field.
	 */
	protected function getMergeTagSelectField($level)
	{
		$customFields = $this->getCustomFields($level->akeebasubs_level_id);
		$options = array();
		$options[] = JHTML::_('select.option','',JText::_('PLG_AKEEBASUBS_' . strtoupper($this->name) . '_NONE'));
		foreach($customFields as $title => $id) {
			$options[] = JHTML::_('select.option',$id,$title);
		}
		// Set pre-selected values
		$selected = array();
		if(! empty($this->customFields[$level->akeebasubs_level_id])) {
			$selected = $this->customFields[$level->akeebasubs_level_id];
		}
		// Create the select field
		return JHtmlSelect::genericlist($options, 'params[' . strtolower($this->name) . '_customfields][]', 'multiple="multiple" size="8" class="input-large"', 'value', 'text', $selected);
	}

	/*
	 * Return the MailChimp groups as an array of options
	 */
	public function getMCGroupSelectOptions($level)
	{
		// Put groups in select field
		$this->addGroups = array($level => $this->getGroups());
		$groups = $this->getMCGroups($level);
		$options = array();
		$options[] = JHTML::_('select.option','',JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_NONE'));
		foreach($groups as $title => $id) {
			$options[] = JHTML::_('select.option',$id,$title);
		}
		return $options;
	}

	/*
	 * Return the MailChimp lists as an array of options
	 */
	public function getMCListSelectOptions($level)
	{
		// Put groups in select field
		$groups = $this->getGroups($level);
		$options = array();
		$options[] = JHTML::_('select.option','',JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_NONE'));
		foreach($groups as $title => $id) {
			$options[] = JHTML::_('select.option',$id,$title);
		}
		return $options;
	}
	
	public function getMCMergeFieldsSelectOptions($level, $shownone = false)
	{
		$lists = $this->mcApi->lists();
		$options = array();
		if ($shownone)
		{
			$options[] = JHTML::_('select.option','',JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_NONE'));
		}
		foreach ($lists['data'] as $list)
		{
			// TODO upgrade to 3.x syntax
			$options[] = JHTML::_('select.optgroup', $list['name']);
			$vars = $this->mcApi->listMergeVars($list['id']);
			foreach ($vars as $var)
			{
				$options[] = JHTML::_('select.option', $var['tag'], $var['name']);
			}
			$options[] = JHTML::_('select.optgroup', $list['name']);
		}
		return $options;
	}
	
}
