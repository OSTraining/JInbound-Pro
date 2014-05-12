<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			<div class="span12">
			
				<div class="row-fluid">
					<div class="span12 well">
						<div class="row-fluid">
							<?php
								$this->_currentFieldset = $this->form->getFieldset('default');
								foreach ($this->_currentFieldset as $field) :
							?>
							<div class="span6">
								<?php
									$this->_currentField = $field;
									echo $this->loadTemplate('edit_field');
								?>
							</div>
							<?php
								endforeach;
							?>
						</div>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span12">
						<?php
							$this->_currentFieldset = $this->form->getFieldset('profile');
							echo $this->loadTemplate('edit_fields');
						?>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span12 well">
					
						<div class="row-fluid">
							<h3><?php echo JText::_('COM_JINBOUND_LEAD_DETAILS'); ?></h3>
						</div>
						
						<div class="row-fluid">
							<div class="span12">
								<?php
									$this->_currentFieldset = $this->form->getFieldset('details');
									echo $this->loadTemplate('edit_fields');
								?>
							</div>
						</div>
						
						<div class="row-fluid">
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_FORM_INFORMATION'); ?></h4>
								<div class="well">
									<?php echo JHtml::_('jinbound.startSlider', 'leadSlider'); ?>
									<?php if (!empty($this->item->conversions)) : ?>
									<?php foreach ($this->item->conversions as $i => $data) : ?>
									<?php echo JHtml::_('jinbound.addSlide', 'leadSlider', $data->created . ' | ' . $data->page_name, 'leadslider-' . $i); ?>
									<table class="table table-striped">
										<?php if (array_key_exists('lead', $data->formdata)) foreach ($data->formdata['lead'] as $key => $value) : ?>
										<tr>
											<td><?php echo $this->escape($key); ?></td>
											<td><?php echo $this->escape(print_r($value, 1)); ?></td>
										</tr>
										<?php endforeach; ?>
									</table>
									<?php echo JHtml::_('jinbound.endSlide'); ?>
									<?php endforeach; ?>
									<?php endif; ?>
									<?php echo JHtml::_('jinbound.endSlider'); ?>
								</div>
								<h4><?php echo JText::_('COM_JINBOUND_CURRENT_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									<?php if (empty($this->item->campaigns)) : ?>
									<div class="alert alert-error"><?php echo JText::_('COM_JINBOUND_NO_CAMPAIGNS'); ?></div>
									<?php else : ?>
										<?php echo JHtml::_('jinbound.startSlider', 'campaignSlider'); ?>
										<?php foreach ($this->item->campaigns as $i => $campaign) : ?>
											<?php echo JHtml::_('jinbound.addSlide', 'campaignSlider', $campaign->name, 'campaignslider-' . $i); ?>
											<?php foreach ($this->item->statuses[$campaign->id] as $status) : ?>
									<div class="row-fluid">
										<div class="span6"><?php echo $this->escape($status->name); ?></div>
										<div class="span4"><?php echo $this->escape($status->created); ?></div>
										<div class="span2"><?php echo $this->escape($status->created_by); ?></div>
									</div>
											<?php endforeach; ?>
											<?php echo JHtml::_('jinbound.endSlide'); ?>
										<?php endforeach; ?>
										<?php echo JHtml::_('jinbound.endSlider'); ?>
									<?php endif; ?>
									
									<div class="row-fluid">
										<div class="span12">
											<?php
												$this->_currentFieldset = $this->form->getFieldset('campaigns');
												echo $this->loadTemplate('edit_fields');
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_NOTES'); ?></h4>
								<div class="pull-right">
									<?php echo JHtml::_('jinbound.leadnotes', $this->item->id, true); ?>
								</div>
								<div class="well">
									<table id="jinbound_leadnotes_table" class="table table-striped">
										<tbody>
										<?php if (!empty($this->notes)) : foreach ($this->notes as $note) : ?>
											<tr>
												<td><span class="label"><?php echo $note->created; ?></span></td>
												<td class="note"><?php echo $this->escape($note->author); ?></td>
												<td class="note"><?php echo $this->escape($note->text); ?></td>
											</tr>
										<?php endforeach; endif; ?>
										</tbody>
									</table>
								</div>
								<h4><?php echo JText::_('COM_JINBOUND_TRACKS'); ?></h4>
								<div class="well">
								<?php if (empty($this->item->tracks)) : ?>
									<div class="alert alert-warning"><?php echo JText::_('COM_JINBOUND_NO_TRACKS_FOUND'); ?></div>
								<?php else: ?>
									<?php foreach ($this->item->tracks as $i => $track) : if (20 < $i) break; ?>
									<div class="well">
										<span class="pull-right"><i class="icon-<?php echo ($track->current_user_id ? 'ok' : 'remove')?>"> </i></span>
										<span class="label"><?php echo $this->escape($track->created); ?></span>
										<div class="track-url"><?php echo $this->escape($track->url); ?></div>
									</div>
									<?php endforeach; ?>
								<?php endif; ?>
								</div>
							</div>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
