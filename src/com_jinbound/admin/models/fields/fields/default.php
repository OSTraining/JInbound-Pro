<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

?>
<table class="jinboundfields table table-striped">
    <thead>
    <tr>
        <th width="1%">&nbsp;</th>
        <th><?php echo JText::_('COM_JINBOUND_FIELD_TITLE'); ?></th>
        <th width="15%" class="nowrap"><?php echo JText::_('JGRID_HEADING_ORDERING'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->fields as $field) : ?>
        <tr>
            <td>
                <?php if ($field->core) : ?>
                    <i class="icon-lock"></i><input name="<?php echo $this->escape($this->input_name); ?>[]"
                                                    type="hidden" value="<?php echo $this->escape($field->id); ?>"/>
                <?php else : ?>
                    <input id="<?php echo $this->escape($this->input_id . $field->id); ?>"
                           name="<?php echo $this->escape($this->input_name); ?>[]" type="checkbox"
                           value="<?php echo $this->escape($field->id); ?>"<?php echo $field->extra; ?>/>
                <?php endif; ?>
            </td>
            <td><?php echo $this->escape($field->title); ?></td>
            <td class="nowrap">
                <div class="jinboundfields_ordering btn-group">
                    <input type="button" class="jinboundfields_ordering_up btn" value="↑"/>
                    <input type="button" class="jinboundfields_ordering_down btn" value="↓"/>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
