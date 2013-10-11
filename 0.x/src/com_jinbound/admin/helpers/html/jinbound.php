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
	
	public function formdata($id, $formname, $formdata, $script = true) {
		if (!is_a($formdata, 'JRegistry')) {
			$registry = new JRegistry();
			if (is_object($formdata)) {
				$registry->loadObject($formdata);
			}
			else if (is_array($formdata)) {
				$registry->loadArray($formdata);
			}
			else if (is_string($formdata)) {
				$registry->loadString($formdata);
			}
			else {
				return;
			}
			$data = $registry->toArray();
		}
		else {
			$data = $formdata->toArray();
		}
		
		$filter = JFilterInput::getInstance();
		
		?>
			<div class="formdata">
<?php if ($script) : ?>
				<a href="#" class="formdata-modal"><?php echo $filter->clean($formname); ?></a>
<?php else : ?>
				<h3><?php echo $filter->clean($formname); ?></h3>
<?php endif; ?>
				<div class="formdata-container<?php if ($script) : ?> hide<?php endif; ?>">
					<div class="formdata-data">
						<h4><?php echo JText::_('COM_JINBOUND_FORM_INFORMATION'); ?></h4>
						<div class="well">
							<table class="table table-striped">
								<?php if (array_key_exists('lead', $data)) foreach ($data['lead'] as $key => $value) : ?>
								<tr>
									<td><?php echo $filter->clean($key); ?></td>
									<td><?php echo $filter->clean(print_r($value, 1)); ?></td>
								</tr>
								<?php endforeach; ?>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php
		
		// add script once
		static $scripted;
		
		if (!is_null($scripted)) {
			return;
		}
		
		$scripted = true;
		
		$doc = JFactory::getDocument();
		// if no scripts can be added, bail
		if (!$script || !method_exists($doc, 'addScriptDeclaration')) {
			return;
		}
		JHtml::_('behavior.modal');
		// build script
		$source = <<<EOF
(function($){
	$(document).ready(function(){
		$('.formdata-modal').click(function(e){
			try {
				console.log('opening modal');
			}
			catch (err) {
			}
			var data = $(e.target).parent().find('.formdata-data');
			if (data.length) {
				SqueezeBox.setContent('adopt', data[0]);
			}
			e.preventDefault();
			e.stopPropagation();
		});
	});
})(jQuery);
EOF
;
		$doc->addScriptDeclaration($source);
	}
	
	public function leadnotes($id) {
		static $notes;
		
		if (is_null($notes)) {
			$notes = array();
			$document = JFactory::getDocument();
			$document->addScript(JInboundHelperUrl::media() . '/js/leadnotes.js');
		}
		
		$id  = (int) $id;
		$key = "lead_$id";
		if (!array_key_exists($key, $notes)) {
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->select('*')
				->from('#__jinbound_notes')
				->where($db->quoteName('lead_id') . ' = ' . $id)
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
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="leadnotes-count"><?php echo count($notes[$key]); ?></span> <i class="icon-pencil"> </i> <span class="carat"></span></a>
			<div class="dropdown-menu pull-right" data-stopPropagation="true">
				<div class="leadnotes-block" data-stopPropagation="true">
					<div class="leadnotes-notes" data-stopPropagation="true">
<?php if (!empty($notes[$key])) : foreach ($notes[$key] as $note) : ?>
						<div class="leadnote alert" data-stopPropagation="true">
							<a class="close" data-dismiss="alert" href="#" onclick="(function(){return confirm(Joomla.JText._('COM_JINBOUND_CONFIRM_DELETE'));})();">&times;</a>
							<span class="label" data-stopPropagation="true"><?php echo $note->created; ?></span>
							<div class="leadnote-text" data-stopPropagation="true"><?php echo JFilterInput::getInstance()->clean($note->text, 'string'); ?></div>
						</div>
<?php endforeach; endif; ?>
					</div>
					<div class="leadnotes-form-container" data-stopPropagation="true">
						<fieldset class="well" data-stopPropagation="true">
							<textarea class="leadnotes-new-text input-block-level" data-stopPropagation="true"></textarea>
							<input type="hidden" name="lead_id" value="<?php echo $id; ?>" />
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
