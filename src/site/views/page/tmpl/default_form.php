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

?>
<div class="row-fluid">
    <div class="span12">
        <?php echo $this->loadTemplate('form_open'); ?>
        <div class="row-fluid">
            <div class="span12">
                <h4><?php echo $this->escape($this->item->formname); ?></h4>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php
                foreach ($this->form->getFieldset('lead') as $key => $field) :
                    $this->_currentField = $field;
                    echo $this->loadTemplate('form_field');
                endforeach;
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="btn-toolbar">
                    <div class="btn-group row-fluid">
                        <?php echo $this->loadTemplate('form_submit'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->loadTemplate('form_close'); ?>
    </div>
</div>
