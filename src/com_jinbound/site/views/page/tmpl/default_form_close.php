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
<div>
    <input type="hidden" name="option" value="com_jinbound"/>
    <input type="hidden" name="task" value="lead.save"/>
    <input type="hidden" name="page_id" value="<?php echo (int)$this->item->id; ?>"/>
    <input type="hidden" name="Itemid"
           value="<?php echo JFactory::getApplication()->input->get('Itemid', 0, 'int'); ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>
