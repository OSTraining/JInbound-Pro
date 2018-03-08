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

$tags = $this->input->getTags();

?>
<div id="<?php echo $this->escape($this->input->id); ?>_tags" class="container-fluid">
    <?php

    if (!empty($tags)) :
        ?>
        <ul id="<?php echo $this->escape($this->input->id); ?>_tags_list" class="jinbound_editor">
            <?php
            foreach ($tags as $tag) :
                ?>
                <li class="jinbound_editor_tag">{<?php echo $this->escape($tag->value); ?>}</li>
            <?php
            endforeach;
            ?>
        </ul>
    <?php
    endif;
    ?>
</div>
