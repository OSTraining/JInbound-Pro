<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$fieldsets = $this->form->getFieldsets();

if (1 < count($fieldsets)) : ?>

		<script type="text/javascript">
		(function($){$(function(){
			$('#jinbound_default_tabs').tabs();
		});})(jQuery);
		</script>
		<div id="jinbound_default_tabs">
			<ul class="nav nav-tabs">
<?php $active = true; foreach ($fieldsets as $name => $fieldset) : if ('default' == $name) continue; ?>
				<li><a <?php echo ($active ? ' class="active"' : ''); $active = false; ?>href="<?php echo $this->escape('#jinbound_tab_' . $name); ?>"><?php echo JText::_($fieldset->label); ?></a></li>
<?php endforeach; ?>
			</ul>
<?php endif; ?>
<?php if (1 < count($fieldsets)) : ?>
			<div class="tab-content">
<?php $active = true; foreach ($fieldsets as $name => $fieldset) : if ('default' == $name) continue; ?>
				<div id="<?php echo $this->escape('jinbound_tab_' . $name); ?>" class="tab-pane<?php echo ($active ? ' active' : ''); $active = false; ?>">
					<fieldset class="container-fluid">
						<div class="row-fluid">
							<div class="span9">
							<?php
								$well = false;
								foreach ($this->form->getFieldset($name) as $field) :
									$label = trim($field->label . '');
									if (empty($label)) :
										echo $field->input;
									else :
										$this->_currentField = $field;
										echo $this->loadTemplate('edit_field');
									endif;
									if (empty($well) && method_exists($field, 'getSidebar')) :
										$well = $field->getSidebar();
									endif;
								endforeach;
							?>
							</div>
							<?php if (!empty($well)) : ?>
							<div class="span3 well">
								<?php echo $well; ?>
							</div>
							<?php endif; ?>
						</div>
					</fieldset>
				</div>
<?php endforeach; ?>
			</div>
		</div>
<?php endif;
