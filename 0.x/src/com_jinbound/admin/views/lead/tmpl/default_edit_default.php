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
							<div class="pull-right"><?php echo JText::sprintf('COM_JINBOUND_USER_ID', $this->item->contact_id); ?></div>
						</div>
						<div class="row-fluid">
							<?php
								$this->_currentFieldset = $this->form->getFieldset('leadname');
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
									<h5><?php echo $this->escape($this->page->name); ?></h5>
									<table class="table table-striped">
										<?php $data = $this->item->formdata->toArray(); if (array_key_exists('lead', $data)) foreach ($data['lead'] as $key => $value) : ?>
										<tr>
											<td><?php echo $this->escape($key); ?></td>
											<td><?php echo $this->escape(print_r($value, 1)); ?></td>
										</tr>
										<?php endforeach; ?>
									</table>
								</div>
								<h4><?php echo JText::_('COM_JINBOUND_CURRENT_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									<?php if (!empty($this->records)) : ?>
									<table class="table table-striped">
										<?php foreach ($this->records as $record) : ?>
										<tr>
											<td><a href="<?php echo JInboundHelperUrl::edit('email', $record->email_id); ?>"><?php echo $this->escape($record->email_name); ?></a></td>
											<td><?php echo $this->escape($record->sent); ?></td>
										</tr>
										<?php endforeach; ?>
									</table>
									<?php endif; ?>
								</div>
							</div>
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_NOTES'); ?></h4>
								<div class="pull-right">
									<?php echo JHtml::_('jinbound.leadnotes', $this->item->id, true); ?>
								</div>
								<div class="well">
									<table class="table table-striped">
									<?php if (!empty($this->notes)) : foreach ($this->notes as $note) : ?>
										<tr>
											<td><span class="label"><?php echo $note->created; ?></span></td>
											<td class="note"><?php echo $this->escape($note->text); ?></td>
										</tr>
									<?php endforeach; endif; ?>
									</table>
								</div>
							</div>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
