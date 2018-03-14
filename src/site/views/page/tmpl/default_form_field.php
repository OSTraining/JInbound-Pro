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

$field = JInboundHelperForm::getField($this->_currentField->name, $this->item->formid);

if ($field) :
    ?>
    <?php if ($field->reg->get('show_label', 1)) : ?>
    <div class="row-fluid">
        <?php echo $this->_currentField->label; ?>
    </div>
<?php endif; ?>
    <div class="row-fluid">
        <?php echo $this->_currentField->input; ?>
    </div>
<?php endif;
