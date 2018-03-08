<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundleadmap
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
<form action="<?php echo $this->data->url; ?>" method="post" name="adminForm" id="adminForm">
    <?php
    if (!empty($this->filterForm)) :
        if (class_exists('JLayoutHelper')) :
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this), null,
                array('debug' => false));
        else:
            echo $this->loadTemplate('filters');
        endif;
    endif;
    ?>
</form>
