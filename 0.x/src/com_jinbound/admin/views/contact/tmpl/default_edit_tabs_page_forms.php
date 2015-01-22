<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<fieldset class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div class="well">
				<?php echo JHtml::_('jinbound.startSlider', 'leadSlider', array('active' => 'leadslider-0')); ?>
				<?php if (!empty($this->item->conversions)) : ?>
				<?php foreach (array_reverse($this->item->conversions) as $i => $data) : ?>
				<?php echo JHtml::_('jinbound.addSlide', 'leadSlider', $data->created . ' | ' . $data->page_name, 'leadslider-' . $i); ?>
				<table class="table table-striped">
					<?php if (array_key_exists('lead', $data->formdata)) foreach ($data->formdata['lead'] as $key => $value) : ?>
					<tr>
						<td><?php echo $this->escape($key); ?></td>
						<td><?php echo $this->renderFormField($data->page_id, $key, $value); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php echo JHtml::_('jinbound.endSlide'); ?>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php echo JHtml::_('jinbound.endSlider'); ?>
			</div>
		</div>
	</div>
</fieldset>