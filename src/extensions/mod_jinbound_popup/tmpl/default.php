<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal');

?>
<!-- <?php echo $form->getName(); ?> -->
<div
    data-moduleid="<?php echo $module->id; ?>"
    id="mod_jinbound_popup_<?php echo $module->id; ?>"
    class="mod_jinbound_popup_container<?php if (!empty($sfx)) {
        echo ' mod_jinbound_popup_container' . htmlspecialchars($sfx, ENT_QUOTES, 'UTF-8');
    } ?>">
    <div class="mod_jinbound_popup_form hide">
        <div class="mod_jinbound_popup">
            <?php if ($showintro) : ?>
                <div class="row-fluid">
                    <div class="span12">
                        <?php echo $introtext; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row-fluid">
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
                            <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars(JText::_($btn),
                                    ENT_QUOTES, 'UTF-8'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
