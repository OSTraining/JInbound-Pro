<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');

/**
 * Utility class for JInbound
 *
 * @static
 * @package		JInbound
 * @subpackage	com_jinbound
 */
abstract class JHtmlJInbound
{
	public function priority($id, $priority_id, $prefix, $canChange) {
		static $options;
		
		if (is_null($options)) {
			// get the priorities
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select('Priority.id')
				->select('Priority.name')
				->from('#__jinbound_priorities AS Priority')
				->where('Priority.published = 1')
			);
			
			try {
				$options = $db->loadObjectList();
				if (!is_array($options) || empty($options)) {
					throw new Exception('Empty');
				}
			}
			catch (Exception $e) {
				$options = array();
			}
		}
		
		$attr = 'class="change_priority input-small"';
		if (!$canChange) {
			$attr .= ' disabled="disabled"';
		}
		
		echo JHtml::_('select.genericlist', $options, 'change_priority[' . $id . ']', $attr, 'id', 'name', $priority_id);
	}
	
	public function status($id, $status_id, $prefix, $canChange) {
		static $options;
		
		if (is_null($options)) {
			// get the priorities
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select('Status.id')
				->select('Status.name')
				->from('#__jinbound_lead_statuses AS Status')
				->where('Status.published = 1')
			);
			
			try {
				$options = $db->loadObjectList();
				if (!is_array($options) || empty($options)) {
					throw new Exception('Empty');
				}
			}
			catch (Exception $e) {
				$options = array();
			}
		}
		
		$attr = 'class="change_status input-small"';
		if (!$canChange) {
			$attr .= ' disabled="disabled"';
		}
		
		echo JHtml::_('select.genericlist', $options, 'change_status[' . $id . ']', $attr, 'id', 'name', $status_id);
	}
	
	public function leadupdate() {
		static $loaded;
		
		if (is_null($loaded)) {
			$document = JFactory::getDocument();
			$document->addScript(JInboundHelperUrl::media() . '/js/leadupdate.js');
			$loaded = true;
		}
	}
	
	public function leadnotes($id) {
		static $notes;
		
		if (is_null($notes)) {
			$notes = array();
			$document = JFactory::getDocument();
			$document->addScript(JInboundHelperUrl::media() . '/js/leadnotes.js');
		}
		
		$key = "lead_$id";
		if (!array_key_exists($key, $notes)) {
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select('*')
				->from('#__jinbound_notes')
				->where($db->quoteName('lead_id') . ' = ' . (int) $id)
			);
			
			try {
				$notes[$key] = $db->loadObjectList();
				if (!is_array($notes[$key])) {
					throw new Exception('Empty');
				}
			}
			catch (Exception $e) {
				$notes[$key] = array();
			}
		}
		
		?>
		<div class="leadnotes btn-group">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-pencil"> </i> <span class="carat"></span></a>
			<div class="dropdown-menu pull-right" data-stopPropagation="true">
				<div class="leadnotes-block" data-stopPropagation="true">
					<div class="leadnotes-notes" data-stopPropagation="true">
<?php if (!empty($notes[$key])) : foreach ($notes[$key] as $note) : ?>
						<div class="leadnote" data-stopPropagation="true">
							<span class="label" data-stopPropagation="true"><?php echo $note->created; ?></span>
							<div class="leadnote-text" data-stopPropagation="true"><?php echo JFilterInput::getInstance()->clean($note->text, 'string'); ?></div>
						</div>
<?php endforeach; endif; ?>
					</div>
					<div class="leadnotes-form-container" data-stopPropagation="true">
						<fieldset class="well" data-stopPropagation="true">
							<textarea class="leadnotes-new-text input-block-level" data-stopPropagation="true"></textarea>
							<input type="hidden" name="lead_id" value="<?php echo (int) $id; ?>" />
							<button type="button" class="leadnotes-submit btn btn-primary pull-right" data-stopPropagation="true"><i class="icon-ok"> </i> <?php echo JText::_('JAPPLY'); ?> </button>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		<?php
		
	}
	
	public static function startTabSet($tabSetName, $options = array()) {
		if (JInbound::version()->isCompatible('3.1.0')) {
			JHtml::_('bootstrap.framework');
			return JHtml::_('bootstrap.startTabSet', $tabSetName, $options);
		}
		else {
			return JHtml::_('tabs.start', $tabSetName, $options);
		}
	}
	
	public static function addTab($tabSetName, $tabName, $tabLabel) {
		if (JInbound::version()->isCompatible('3.1.0')) {
			return JHtml::_('bootstrap.addTab', $tabSetName, $tabName, $tabLabel);
		}
		else {
			return JHtml::_('tabs.panel', $tabLabel, $tabName);
		}
	}
	
	public static function endTab() {
		if (JInbound::version()->isCompatible('3.1.0')) {
			return JHtml::_('bootstrap.endTab');
		}
		else {
			return '';
		}
	}
	
	public static function endTabSet() {
		if (JInbound::version()->isCompatible('3.1.0')) {
			return JHtml::_('bootstrap.endTabSet');
		}
		else {
			return JHtml::_('tabs.end');
		}
	}
}
