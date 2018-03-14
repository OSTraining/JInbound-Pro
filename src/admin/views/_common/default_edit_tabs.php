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

$fieldsets = $this->form->getFieldsets();

if (!empty($fieldsets) && !(1 == count($fieldsets) && array_key_exists('default', $fieldsets))) :
    ?>

    <div id="jinbound_default_tabset">
        <?php echo JHtml::_('jinbound.startTabSet', 'jinbound_default_tabs',
            array('active' => (isset($this->default_tab) ? $this->default_tab : 'content_tab'))); ?>
        <?php foreach ($fieldsets as $name => $fieldset) : if ('default' == $name) {
            continue;
        } ?>
            <?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', $name . '_tab',
                JText::_('COM_JINBOUND_' . $this->getName() . '_FIELDSET_' . $name, true)); ?>
            <fieldset class="container-fluid">
                <div class="row-fluid">
                    <div class="span8">
                        <?php
                        $well = false;
                        foreach ($this->form->getFieldset($name) as $field) :
                            $label = trim($field->label . '');
                            if (empty($label)) :
                                echo $field->input;
                            else :
                                $this->_currentField = $field;
                                echo $this->loadTemplate('edit_field');
                            endif;
                            if (empty($well) && method_exists($field, 'getSidebar')) :
                                $well = $field->getSidebar();
                            endif;
                        endforeach;
                        ?>
                    </div>
                    <?php if (!empty($well)) : ?>
                        <div class="span4 well">
                            <?php echo $well; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </fieldset>
            <?php echo JHtml::_('jinbound.endTab'); ?>
        <?php endforeach; ?>
        <?php echo JHtml::_('jinbound.endTabSet'); ?>
    </div>
<?php

endif;
