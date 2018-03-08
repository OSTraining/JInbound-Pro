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
    <input class="input-block-level" name="<?php echo $this->_optname; ?>[name][]"
           value="<?php echo $this->escape($this->_optnamevalue); ?>"
           placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_NAME_PLACEHOLDER'); ?>"/>
</div>
<div class="row-fluid">
    <input class="input-block-level" name="<?php echo $this->_optname; ?>[value][]"
           value="<?php echo $this->escape($this->_optvaluevalue); ?>"
           placeholder="<?php echo JText::_('COM_JINBOUND_FORMFIELD_OPTION_VALUE_PLACEHOLDER'); ?>"/>
</div>
<div class="row-fluid btn-group">
	<span class="btn formbuilder-option-add">
		<i class="icon-plus"></i>
	</span>
    <span class="btn formbuilder-option-del">
		<i class="icon-minus"></i>
	</span>
    <?php if ('attributes' != $this->optionsInputName) : ?>
        <span class="btn formbuilder-option-move formbuilder-option-up">
		<i class="icon-arrow-up"></i>
	</span>
        <span class="btn formbuilder-option-move formbuilder-option-down">
		<i class="icon-arrow-down"></i>
	</span>
    <?php endif; ?>
</div>
