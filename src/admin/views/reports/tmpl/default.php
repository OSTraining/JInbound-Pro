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

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.calendar');

?>
    <script type="text/javascript">
        Joomla.submitbutton = function(task) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        };
    </script>
    <div class="container-fluid <?php echo $this->viewClass; ?>" id="jinbound_component">
        <?php
        $mainAttributes = array(
            'id' => 'j-main-container'
        );
        if (!empty($this->sidebar)) :
            $mainAttributes['class'] = 'span10';
            ?>
            <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
            </div>
        <?php
        endif;
        ?>
        <div <?php echo ArrayHelper::toString($mainAttributes); ?>
        <?php
        echo JHtml::_('jinbound.startTabSet', 'jinbound_default_tabs', array('active' => 'content_tab'));

        echo JHtml::_(
            'jinbound.addTab',
            'jinbound_default_tabs',
            'content_tab',
            JText::_('COM_JINBOUND_REPORTS', true)
        );
        ?>
        <form action="<?php echo JInboundHelperUrl::view('reports'); ?>"
              method="post"
              id="adminForm"
              name="adminForm"
              class="form-validate"
              enctype="multipart/form-data">
            <div class="row-fluid">
                <div class="span12 text-center">
                    <div class="reports_search">
                        <?php
                        echo $this->campaign_filter;
                        echo $this->page_filter;
                        echo $this->priority_filter;
                        echo $this->status_filter;

                        echo JHtml::_(
                            'calendar',
                            $this->state->get('filter_start'),
                            'filter_start',
                            'filter_start',
                            '%Y-%m-%d',
                            array(
                                'size'        => 10,
                                'placeholder' => JText::_('COM_JINBOUND_FROM'),
                                'onchange'    => $this->filter_change_code
                            )
                        );

                        echo JHtml::_(
                            'calendar',
                            $this->state->get('filter_end'),
                            'filter_end',
                            'filter_end',
                            '%Y-%m-%d',
                            array(
                                'size'        => 10,
                                'placeholder' => JText::_('COM_JINBOUND_TO'),
                                'onchange'    => $this->filter_change_code
                            )
                        );
                        ?>
                    </div>
                </div>
            </div>

            <?php echo $this->loadTemplate('dashboard'); ?>
            <div>
                <input name="task" value="" type="hidden"/>
            </div>
        </form>

        <?php
        echo JHtml::_('jinbound.endTab');

        if ($this->permissions && JFactory::getUser()->authorise('core.admin', 'com_jinbound')) :
            echo JHtml::_(
                'jinbound.addTab',
                'jinbound_default_tabs',
                'permissions_tab',
                JText::_('JCONFIG_PERMISSIONS_LABEL', true)
            );
            ?>
            <div class="row-fluid">
                <form action="<?php echo JRoute::_('index.php?option=com_jinbound&task=reports.permissions'); ?>"
                      method="post">
                    <?php
                    foreach ($this->permissions->getFieldsets() as $fieldset) :
                        $fields = $this->permissions->getFieldset($fieldset->name);
                        ?>
                        <fieldset>
                            <legend><?php echo JText::_($fieldset->label); ?></legend>
                            <?php foreach ($fields as $field) : ?>
                                <?php echo $field->input; ?>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php
                    endforeach;

                    echo JHtml::_('form.token');
                    ?>
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="icon-save"></i>
                        <?php echo JText::_('JTOOLBAR_APPLY'); ?>
                    </button>
                </form>
            </div>
            <?php
            echo JHtml::_('jinbound.endTab');

        endif;

        echo JHtml::_('jinbound.endTabSet');
        ?>
    </div>
<?php
echo $this->loadTemplate('footer');

echo $this->loadTemplate('script');

