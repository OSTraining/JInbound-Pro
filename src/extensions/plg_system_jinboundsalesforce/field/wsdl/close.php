<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundsalesforce
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

defined('_JEXEC') or die;

?>
<script type="text/javascript">
    if (window.parent) {
        window.parent.jSelectWsdl_<?php echo $this->escape($this->field); ?>('<?php echo $this->escape($this->file); ?>');
    }
</script>
