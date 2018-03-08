<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<fieldset class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="pull-right">
                <?php echo JHtml::_('jinbound.leadnotes', $this->item->id, true); ?>
            </div>
            <div class="well">
                <table id="jinbound_leadnotes_table" class="table table-striped">
                    <tbody>
                    <?php if (!empty($this->notes)) : foreach ($this->notes as $note) : ?>
                        <tr>
                            <td><span class="label"><?php echo JInbound::userDate($note->created); ?></span></td>
                            <td class="note"><?php echo $this->escape($note->author); ?></td>
                            <td class="note"><?php echo $this->escape($note->text); ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td>
                                <div
                                    class="alert alert-error"><?php echo JText::_('COM_JINBOUND_NO_NOTES_FOUND'); ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</fieldset>
