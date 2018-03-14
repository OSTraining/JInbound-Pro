<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 **********************************************
 * jInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;


$topFieldset    = $this->form->getFieldset('top');
$hiddenFieldset = $this->form->getFieldset('hidden');


?>

<form
    action="<?php echo JURI::base() . 'index.php?option=com_jinbound&task=location.save&id=' . (int)$this->item->id; ?>"
    method="post" id="location-form" name="adminForm" class="form-validate" enctype="multipart/form-data">

    <div class="">
        <fieldset class="adminform" style="padding:0px; border:0px;">
            <ul class="adminformlist">
                <?php foreach ($topFieldset as $name => $field): ?>
                    <li><?php echo $field->label; ?></li>
                    <li><?php echo $field->input; ?></li>
                <?php endforeach; ?>
            </ul>
        </fieldset>
    </div>

    <div class="clearfix"></div>
    <?php echo $this->loadTemplate('footer'); ?>

    <div>
        <?php foreach ($hiddenFieldset as $name => $field) {
            echo $field->input;
        } ?>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
        <input type="hidden" name="function" value="<?php echo JRequest::getCmd('function'); ?>"/>
        <?php if ('component' == JRequest::getCmd('tmpl')) : ?>
            <input type="hidden" name="tmpl" value="component"/>
            <input type="hidden" name="mlayout" value="modal"/>
        <?php endif; ?>
        <?php echo JHtml::_('form.token'); ?>
    </div>

</form>
