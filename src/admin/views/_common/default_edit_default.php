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

$fieldset = $this->form->getFieldset('default');
$well     = false;
// do we have a well?
foreach ($fieldset as $field) :
    if (empty($well) && method_exists($field, 'getSidebar')) :
        $well = $field->getSidebar();
    endif;
endforeach;

?>
<div class="row-fluid">
    <div class="span<?php echo $well ? 9 : 12; ?>">
        <?php
        $this->_currentFieldset = $fieldset;
        echo $this->loadTemplate('edit_fields');
        ?>
    </div>
    <?php if (!empty($well)) : ?>
        <div class="span3 well">
            <?php echo $well; ?>
        </div>
    <?php endif; ?>
</div>
