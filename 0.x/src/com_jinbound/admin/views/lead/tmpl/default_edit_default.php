<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span1">TODO: scrolly thing</div>
	<div class="span11">
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
					<div class="span7">
						<?php
							$this->_currentFieldset = $this->form->getFieldset('profile');
							echo $this->loadTemplate('edit_fields');
						?>
					</div>
					<div class="span5">
						<?php
							$this->_currentFieldset = $this->form->getFieldset('social');
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
									TODO: form info
								</div>
								<h4><?php echo JText::_('COM_JINBOUND_CURRENT_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									TODO: campaign info
								</div>
							</div>
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_NOTES'); ?></h4>
								<div class="well">
									TODO: notes info
								</div>
							</div>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
