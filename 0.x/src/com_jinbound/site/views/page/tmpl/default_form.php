<?php
/**
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
		<form action="<?php echo JInboundHelperUrl::task('lead.save', true, array('page_id' => (int) $this->item->id)); ?>" method="post">
			<div class="row-fluid">
				<div class="span12">
					<h4><?php echo $this->escape($this->item->formname); ?></h4>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<?php
						foreach ($this->form->getFieldset('lead') as $key => $field) :
							$this->_currentField = $field;
							echo $this->loadTemplate('form_field');
						endforeach;
					?>
					<input type="hidden" name="option" value="com_jinbound" />
					<input type="hidden" name="task" value="lead.save" />
					<input type="hidden" name="page_id" value="<?php echo (int) $this->item->id; ?>" />
					<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get('Itemid', 0, 'int'); ?>" />
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
