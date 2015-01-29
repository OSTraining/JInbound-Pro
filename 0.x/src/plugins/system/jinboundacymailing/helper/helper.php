<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundacymailing
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');

class JinboundAcymailing
{
	protected $db;
	
	public function __construct($config = array())
	{
		$this->db = JFactory::getDbo();
	}
	
	public function onJinboundSetStatus($status_id, $campaign_id, $contact_id)
	{
		// load campaign, status, contact
		foreach (array('campaign', 'status', 'contact') as $var)
		{
			$class = 'JInboundTable' . ucwords($var);
			require_once JPATH_ADMINISTRATOR . '/components/com_jinbound/tables/' . $var . '.php';
			$$var = new $class($this->db);
			$$var->load(${$var . '_id'});
			if (property_exists($$var, 'params') && !is_a($$var->params, 'JRegistry'))
			{
				$reg = new JRegistry();
				$reg->loadString($$var->params);
				$$var->params = $reg;
			}
		}
		
		if ($status->final)
		{
			$addLists    = array();
			$removeLists = array_filter($campaign->params->get('acymailing_removelists', array()));
		}
		else
		{
			$addLists    = array_filter($campaign->params->get('acymailing_addlists', array()));
			$removeLists = array();
		}
		
		// add users to acymailing subscriber table
		// #__acymailing_subscriber
		/*
		 * mysql> describe jos_acymailing_subscriber;
+----------------+------------------+------+-----+---------+----------------+
| Field          | Type             | Null | Key | Default | Extra          |
+----------------+------------------+------+-----+---------+----------------+
| subid          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| email          | varchar(200)     | NO   | UNI | NULL    |                |
| userid         | int(10) unsigned | NO   | MUL | 0       |                |
| name           | varchar(250)     | NO   |     |         |                |
| created        | int(10) unsigned | YES  |     | NULL    |                |
| confirmed      | tinyint(4)       | NO   |     | 0       |                |
| enabled        | tinyint(4)       | NO   | MUL | 1       |                |
| accept         | tinyint(4)       | NO   |     | 1       |                |
| ip             | varchar(100)     | YES  |     | NULL    |                |
| html           | tinyint(4)       | NO   |     | 1       |                |
| key            | varchar(250)     | YES  |     | NULL    |                |
| confirmed_date | int(10) unsigned | NO   |     | 0       |                |
| confirmed_ip   | varchar(100)     | YES  |     | NULL    |                |
| lastopen_date  | int(10) unsigned | NO   |     | 0       |                |
| lastopen_ip    | varchar(100)     | YES  |     | NULL    |                |
| lastclick_date | int(10) unsigned | NO   |     | 0       |                |
| lastsent_date  | int(10) unsigned | NO   |     | 0       |                |
| source         | varchar(50)      | NO   |     |         |                |
+----------------+------------------+------+-----+---------+----------------+

		 */
		$now = time();
		$subtable = '#__acymailing_subscriber';
		$subid = $this->db->setQuery($this->db->getQuery(true)
			->select('subid')
			->from($subtable)
			->where('email = ' . $this->db->quote($contact->email))
		)->loadResult();
		if (!$subid)
		{
			$values = array(
				$this->db->quote($contact->email)
			,	$this->db->quote($contact->user_id)
			,	$this->db->quote(trim($contact->first_name . ' ' . $contact->last_name))
			,	$this->db->quote($now)
			,	$this->db->quote('jInbound')
			);
			// insert record
			$this->db->setQuery($this->db->getQuery(true)
				->insert($subtable)
				->columns(array('email', 'userid', 'name', 'created', 'source'))
				->values(implode(',', $values))
			)->query();
			$subid = $this->db->insertid();
		}
		// subscribe them to lists
		// #__acymailing_listsub
		/*
		 * mysql> describe jos_acymailing_listsub;
+-----------+----------------------+------+-----+---------+-------+
| Field     | Type                 | Null | Key | Default | Extra |
+-----------+----------------------+------+-----+---------+-------+
| listid    | smallint(5) unsigned | NO   | PRI | NULL    |       |
| subid     | int(10) unsigned     | NO   | PRI | NULL    |       |
| subdate   | int(10) unsigned     | YES  |     | NULL    |       |
| unsubdate | int(10) unsigned     | YES  |     | NULL    |       |
| status    | tinyint(4)           | NO   |     | NULL    |       |
+-----------+----------------------+------+-----+---------+-------+
		 */
		foreach ($addLists as $list)
		{
			$record = $this->getListSub($list, $subid);
			if (empty($record))
			{
				$this->addListSub($list, $subid, $now, false, 1);
			}
			else
			{
				$this->updateListSub($list, $subid, $now, false, 1);
			}
		}
		foreach ($removeLists as $list)
		{
			$record = $this->getListSub($list, $subid);
			if (!empty($record))
			{
				$this->updateListSub($list, $subid, false, $now, -1);
			}
		}
	}
	
	private function updateListSub($list, $sub, $subdate = false, $unsubdate = false, $status = 1)
	{
		$query = $this->db->getQuery(true)
			->update('#__acymailing_listsub')
			->set('status = ' . intval($status))
			->where('listid = ' . intval($list))
			->where('subid = ' . intval($sub))
		;
		if ($subdate)
		{
			$query->set('subdate = ' . $this->db->quote($subdate));
		}
		if ($unsubdate)
		{
			$query->set('unsubdate = ' . $this->db->quote($unsubdate));
		}
		$this->db->setQuery($query)->query();
	}
	
	private function addListSub($list, $sub, $subdate = false, $unsubdate = false, $status = 1)
	{
		$columns = array('listid', 'subid', 'status');
		$values  = array(intval($list), intval($sub), intval($status));
		if ($subdate)
		{
			$columns[] = 'subdate';
			$values[]  = $this->db->quote($subdate);
		}
		if ($unsubdate)
		{
			$columns[] = 'unsubdate';
			$values[]  = $this->db->quote($unsubdate);
		}
		$this->db->setQuery($this->db->getQuery(true)
			->insert('#__acymailing_listsub')
			->columns($columns)
			->values(implode(',', $values))
		)->query();
	}
	
	private function getListSub($list, $sub)
	{
		return $this->db->setQuery($this->db->getQuery(true)
			->select('*')
			->from('#__acymailing_listsub')
			->where('subid = ' . $sub)
			->where('listid = ' . (int) $list)
		)->loadObject();
	}
	
	public function getEmailListDetails($email)
	{
		if (empty($email))
		{
			return array();
		}
		return $this->db->setQuery($this->db->getQuery(true)
			->select('List.name')
			->select('ListSub.status')
			->from('#__acymailing_list AS List')
			->leftJoin('#__acymailing_listsub AS ListSub ON ListSub.listid = List.listid')
			->leftJoin('#__acymailing_subscriber AS Sub ON Sub.subid = ListSub.subid')
			->where('Sub.email = ' . $this->db->quote($email))
			->group('List.listid')
		)->loadObjectList();
	}
	
	public function getListSelectOptions($level)
	{
		return $this->db->setQuery($this->db->getQuery(true)
			->select('listid AS value, name AS text')
			->from('#__acymailing_list')
			->where($this->db->quoteName('type') . '=' . $this->db->quote('list'))
			->where($this->db->quoteName('published') . '=' . $this->db->quote('1'))
		)->loadObjectList();
	}
	
	public function getListTable($email, $id = null)
	{
		JFactory::getLanguage()->load('com_acymailing', JPATH_ROOT);
		if (empty($email))
		{
			return JText::_('PLG_SYSTEM_JINBOUNDACYMAILING_CONTACT_NO_EMAIL');
		}
		$lists = $this->getEmailListDetails($email);
		if (empty($lists))
		{
			return JText::_('PLG_SYSTEM_JINBOUNDACYMAILING_CONTACT_NO_LISTS');
		}
		$filter = JFilterInput::getInstance();
		if (!empty($id))
		{
			$id = ' id="' . $filter->clean($id) . '"';
		}
		$html = array();
		$html[] = '<table class="table table-striped"' . $id . '>';
		$html[] = '<thead><tr>';
		$html[] = '<th>' . JText::_('COM_JINBOUND_NAME') . '</th>';
		$html[] = '<th>' . JText::_('JSTATUS') . '</th>';
		$html[] = '</tr></thead>';
		$html[] = '<tbody>';
		foreach ($lists as $list)
		{
			$status = ($list->status == 1) ? JText::_('SUBSCRIBED') : (($list->status == -1) ? JText::_('UNSUBSCRIBED') : JText::_('PENDING_SUBSCRIPTION'));
			$html[] = '<tr>';
			$html[] = '<td><h3>' . $filter->clean($list->name) . '</h3></td>';
			$html[] = '<td>' . $filter->clean($status) . '</td>';
			$html[] = '</tr>';
		}
		$html[] = '</tbody>';
		$html[] = '</table>';
		return implode("\n", $html);
	}
}
