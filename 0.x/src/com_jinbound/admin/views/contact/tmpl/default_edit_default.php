<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('jinbound.leadupdate');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$context    = JInbound::COM.'.contact.'.$this->item->id;
$canEdit    = $user->authorise('core.edit', $context);
$canCheckin = $user->authorise('core.manage', 'com_checkin') || $this->item->checked_out == $userId || $this->item->checked_out == 0;
$canEditOwn = $user->authorise('core.edit.own', $context) && $this->item->created_by == $userId;
$canChange  = $user->authorise('core.edit.state', $context) && $canCheckin;

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
								<h4><?php echo JText::_('COM_JINBOUND_CURRENT_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									<?php if (empty($this->item->campaigns)) : ?>
									<div class="alert alert-error"><?php echo JText::_('COM_JINBOUND_NO_CAMPAIGNS'); ?></div>
									<?php else : ?>
										<?php echo JHtml::_('jinbound.startSlider', 'campaignSlider', array('active' => 'campaignslider-0')); ?>
										<?php foreach ($this->item->campaigns as $i => $campaign) : ?>
											<?php echo JHtml::_('jinbound.addSlide', 'campaignSlider', $campaign->name, 'campaignslider-' . $i); ?>
									<div class="row-fluid current-priority current-priority-<?php echo $campaign->id; ?>">
										<div class="span6"><?php echo JText::_('COM_JINBOUND_CURRENT_PRIORITY'); ?></div>
										<div class="span5"><?php
											echo JHtml::_('jinbound.priority', $this->item->id, $this->item->priorities[$campaign->id][0]->priority_id, $campaign->id, 'contacts.', $canChange);
										?></div>
									</div>
									<div class="row-fluid current-status current-status-<?php echo $campaign->id; ?>">
										<div class="span6"><?php echo JText::_('COM_JINBOUND_CURRENT_STATUS'); ?></div>
										<div class="span5"><?php
											echo JHtml::_('jinbound.status', $this->item->id, $this->item->statuses[$campaign->id][0]->status_id, $campaign->id, 'contacts.', $canChange);
										?></div>
									</div>
									<div class="row-fluid">
										<div class="span12 current-statuses current-statuses-<?php echo $campaign->id; ?>">
											<?php foreach ($this->item->statuses[$campaign->id] as $status) : ?>
											<div class="row-fluid">
												<div class="span4 status-name"><?php echo $this->escape($status->name); ?></div>
												<div class="span3 status-date"><?php echo $this->escape($status->created); ?></div>
												<div class="span4 status-author"><?php echo $this->escape($status->created_by_name); ?></div>
											</div>
											<?php endforeach; ?>
										</div>
									</div>
											<?php echo JHtml::_('jinbound.endSlide'); ?>
										<?php endforeach; ?>
										<?php echo JHtml::_('jinbound.endSlider'); ?>
									<?php endif; ?>
									
									<div class="row-fluid">
										<div class="alert alert-error"><?php echo JText::_('COM_JINBOUND_WARNING_GET_PERMISSION_BEFORE_ADDING_TO_CAMPAIGN'); ?></div>
									</div>
									<div class="row-fluid">
										<div class="span12">
											<?php
												$this->_currentFieldset = $this->form->getFieldset('campaigns');
												echo $this->loadTemplate('edit_fields');
											?>
										</div>
									</div>
								</div>
								<?php if (!empty($this->item->previous_campaigns)) : ?>
								
								<h4><?php echo JText::_('COM_JINBOUND_PREVIOUS_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									<?php echo JHtml::_('jinbound.startSlider', 'previousCampaignSlider', array('active' => 'previouscampaignslider-0')); ?>
									<?php foreach ($this->item->previous_campaigns as $i => $campaign) : ?>
										<?php echo JHtml::_('jinbound.addSlide', 'previousCampaignSlider', $campaign->name, 'previouscampaignslider-' . $i); ?>
										<?php foreach ($this->item->statuses[$campaign->id] as $status) : ?>
									<div class="row-fluid">
										<div class="span6"><?php echo $this->escape($status->name); ?></div>
										<div class="span3"><?php echo $this->escape($status->created); ?></div>
										<div class="span2"><?php echo $this->escape($status->created_by); ?></div>
									</div>
										<?php endforeach; ?>
										<?php echo JHtml::_('jinbound.endSlide'); ?>
									<?php endforeach; ?>
									<?php echo JHtml::_('jinbound.endSlider'); ?>
								</div>
								<?php endif; ?>
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
										<?php endforeach; else: ?>
											<tr><td><div class="alert alert-error"><?php echo JText::_('COM_JINBOUND_NO_NOTES_FOUND'); ?></div></td></tr>
										<?php endif; ?>
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
										<span class="pull-right"><i class="hasTip hasTooltip icon-<?php echo ($track->current_user_id ? 'user' : 'warning'); ?>" title="<?php echo JText::_('COM_JINBOUND_' . ($track->current_user_id ? 'USER' : 'AUTHOR_GUEST')); ?>"> </i></span>
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
<script type="text/javascript">
(function($,d){$(function(){
	$(d.body).on('jinboundleadupdate', function(e, response) {
		if (!(response && response.success)) {
			return;
		}
		var cid = response.request.campaign_id, container = $('.current-statuses-' + cid);
		if (!container.length) {
			return;
		}
		var html = '<div class="row-fluid"><div class="span4 status-name"></div><div class="span3 status-date"></div><div class="span4 status-author"></div></div>';
		container.empty();
		$(response.list[cid]).each(function(i, el){
			console.log(el);
			var inner = $(html);
			inner.find('.status-name').text(el.name);
			inner.find('.status-date').text(el.created);
			inner.find('.status-author').text(el.created_by_name);
			container.append(inner);
		});
	});
});})(jQuery,document);
</script>