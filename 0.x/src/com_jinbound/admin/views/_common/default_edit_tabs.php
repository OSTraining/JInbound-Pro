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
		<ul class="nav nav-tabs">
<?php $active = true; foreach ($fieldsets as $name => $fieldset) : if ('default' == $name) continue; ?>
			<li><a <?php echo ($active ? ' class="active"' : ''); $active = false; ?>href="<?php echo $this->escape('#jinbound_tab_' . $name); ?>" data-toggle="tab"><?php echo JText::_($fieldset->label); ?></a></li>
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
								foreach ($this->form->getFieldset($name) as $field) :
									$label = trim($field->label . '');
									if (empty($label)) :
										echo $field->input;
									else :
									?>
							<div class="row-fluid">
								<div class="span1"><?php echo $label; ?></div>
								<div class="span10 offset1"><?php echo $field->input; ?></div>
							</div>
									<?php
									endif;
								endforeach;
							?>
						</div>
						<div class="span3 well">
						</div>
					</div>
				</fieldset>
			</div>
<?php endforeach; ?>
		</div>
<?php endif;