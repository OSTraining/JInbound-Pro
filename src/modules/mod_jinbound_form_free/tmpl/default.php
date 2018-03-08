<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_form
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

defined('_JEXEC') or die;

?>
<div class="row-fluid mod_jinbound_form_free<?php if (!empty($sfx)) {
    echo ' mod_jinbound_form_free' . htmlspecialchars($sfx, ENT_QUOTES, 'UTF-8');
} ?>">
    <div class="span12">
        <form action="<?php echo $form_url; ?>" method="post" enctype="multipart/form-data">
            <?php foreach ($form->getFieldsets() as $fieldset) : ?>
                <fieldset class="control-list">
                    <?php foreach ($form->getFieldset($fieldset->name) as $field) : ?>
                        <?php if ($field->hidden) : ?>
                            <?php echo $field->input; ?>
                        <?php else : ?>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $field->label; ?>
                                </div>
                                <div class="controls">
                                    <?php echo $field->input; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars(JText::_($btn), ENT_QUOTES,
                        'UTF-8'); ?></button>
            </div>
        </form>
    </div>
</div>
