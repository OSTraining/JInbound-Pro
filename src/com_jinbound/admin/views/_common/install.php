<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

?>
<?php
if (!empty($this->messages)) :
    $messageclass = JInbound::version()->isCompatible('3.0.0') ? 'alert alert-message' : 'm pre_message';
    foreach ($this->messages as $message) : ?>
        <div class="<?php echo $messageclass; ?>">
            <?php echo $message; ?>
        </div>
    <?php
    endforeach;
endif;
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
            } else {
                if ('component' == $extension->type) {
                    $link = 'index.php?option=' . $extension->id;
                } else {
                    if ('template' == $extension->type) {
                        $link = 'index.php?option=com_templates&view=template&id=' . $extension->extension_id;
                    }
                }
            }
        }

        ?>
        <tr>
            <td><?php echo $link ? '<a href="' . $link . '">' . $extension->name . '</a>' : $this->escape($extension->name); ?></td>
            <td class="hidden-phone"><?php echo $this->escape($extension->type); ?></td>
            <td><img
                    src="../media/jinbound/images/install-<?php echo $this->escape($extension->installed ? 'success' : 'failed'); ?>.png"/>
            </td>
            <td class="hidden-phone"><?php echo $this->escape($extension->version); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
