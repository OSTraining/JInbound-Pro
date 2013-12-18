<?php
defined('_JEXEC') or die;
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_JINBOUND_INSTALL_EXTENSION'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_JINBOUND_INSTALL_EXTENSION_TYPE'); ?></th>
			<th><?php echo JText::_('COM_JINBOUND_INSTALL_EXTENSION_INSTALLED'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_JINBOUND_INSTALL_EXTENSION_VERSION'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php

foreach ($this->extensions as $extension) :
	$link = false;
	if ($extension->installed) {
		if ('plugin' == $extension->type) {
			$link = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $extension->extension_id;
		}
		else if ('component' == $extension->type) {
			$link = 'index.php?option=' . $extension->id;
		}
		else if ('template' == $extension->type) {
			$link = 'index.php?option=com_templates&view=template&id=' . $extension->extension_id;
		}
	}

?>
		<tr>
			<td><?php echo $link ? '<a href="' . $link . '">' . $extension->name . '</a>' : $this->escape($extension->name); ?></td>
			<td class="hidden-phone"><?php echo $this->escape($extension->type); ?></td>
			<td><img src="../media/system/images/notice-<?php echo $this->escape($extension->installed ? 'info' : 'alert'); ?>.png" /></td>
			<td class="hidden-phone"><?php echo $this->escape($extension->version); ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
