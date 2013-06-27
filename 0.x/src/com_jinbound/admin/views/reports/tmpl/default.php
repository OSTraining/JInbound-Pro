<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.calendar');

?>
<div class="container-fluid" id="jinbound_component">
	<div class="row-fluid">
		<div class="span12 text-center well">
		Random Advice Text
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<fieldset id="filter-bar">
				<?php echo JHtml::_('calendar', $this->state->get('filter_begin'), 'filter_begin', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.fireEvent('submit');this.form.submit()")); ?>
				<?php echo JHtml::_('calendar', $this->state->get('filter_end'), 'filter_end', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.fireEvent('submit');this.form.submit()")); ?>
			</fieldset>
			<div class="clr"> </div>
		</div>
	</div>
	<?php echo $this->loadTemplate('dashboard'); ?>
</div>
