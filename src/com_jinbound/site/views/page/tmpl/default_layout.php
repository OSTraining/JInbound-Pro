<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
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
