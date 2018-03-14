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

if (!property_exists($this->item, 'layout') || !in_array(strtolower($this->item->layout),
        array('a', 'b', 'c', 'd', 'custom', '0'))) :
    $this->item->layout = 'a';
endif;

if ('0' == $this->item->layout || 'custom' == $this->item->layout) :
    echo $this->loadTemplate('layout_custom');
else :

    ?>
    <div class="row-fluid">
        <div class="span12">
            <h1><?php echo $this->escape($this->item->heading); ?></h1>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <?php echo $this->loadTemplate('layout_' . strtolower($this->item->layout)); ?>
        </div>
    </div>
<?php

endif;
