<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 **********************************************
 * JInbound
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
    <div class="span9">
        <div class="pull-right">
            <?php echo $this->loadTemplate('image'); ?>
        </div>
        <?php if (!empty($this->item->subheading)) : ?>
            <h2><?php echo $this->escape($this->item->subheading); ?></h2>
        <?php endif; ?>
        <?php echo $this->loadTemplate('body'); ?>
    </div>
    <div class="span3 well">
        <?php echo $this->loadTemplate('form'); ?>
    </div>
</div>
<div class="row-fluid">
    <?php echo $this->loadTemplate('social'); ?>
</div>
