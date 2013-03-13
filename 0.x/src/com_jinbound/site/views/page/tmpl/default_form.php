<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$go = trim((string) $this->item->submit_text);
if (empty($go)) {
	$go = JText::_('JSUBMIT');
}

?>
<div class="row-fluid">
	<div class="span12">
		<form action="<?php echo JInboundHelperUrl::task('lead.save'); ?>" method="post">
			<div class="row-fluid">
				<div class="span12">
					<h4><?php echo $this->escape($this->item->formname); ?></h4>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<?php
						foreach ($this->item->formbuilder->toArray() as $key => $element) :
							if (!$element['enabled']) continue;
							$this->_currentFieldName = $key;
							$this->_currentField     = $element;
							echo $this->loadTemplate('form_field_' . $key);
						endforeach;
					?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="btn-toolbar">
						<div class="btn-group row-fluid">
							<button type="submit" class="btn btn-primary"><?php echo $this->escape($go); ?></button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
