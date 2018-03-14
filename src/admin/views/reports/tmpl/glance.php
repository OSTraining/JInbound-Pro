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

$show_link = ('reports' == JFactory::getApplication()->input->get('view'));

?>
<div id="jinbound-reports-glance" class="row-fluid">
    <!-- visits -->
    <div class="span2 text-center">
        <h3 id="jinbound-reports-glance-views"><?php echo $this->getVisitCount(); ?></h3>
        <?php if ($show_link) : ?>
            <a href="<?php echo JInboundHelperUrl::view('reports', false, array(
                'layout'       => 'chart',
                'filter_chart' => 'views'
            )) ?>"><?php echo JText::_('COM_JINBOUND_LANDING_PAGE_VIEWS'); ?></a>
        <?php else : ?>
            <span><?php echo JText::_('COM_JINBOUND_LANDING_PAGE_VIEWS'); ?></span>
        <?php endif; ?>
    </div>
    <!-- arrow -->
    <div class="span1 text-center">
        <h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>"/></h3>
    </div>
    <!-- leads -->
    <div class="span1 text-center">
        <h3 id="jinbound-reports-glance-leads"><?php echo $this->getLeadCount(); ?></h3>
        <?php if ($show_link) : ?>
            <a href="<?php echo JInboundHelperUrl::view('reports', false, array(
                'layout'       => 'chart',
                'filter_chart' => 'leads'
            )) ?>"><?php echo JText::_('COM_JINBOUND_LEADS'); ?></a>
        <?php else : ?>
            <span><?php echo JText::_('COM_JINBOUND_LEADS'); ?></span>
        <?php endif; ?>
    </div>
    <!-- arrow -->
    <div class="span1 text-center">
        <h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>"/></h3>
    </div>
    <!-- views to leads -->
    <div class="span2 text-center">
        <h3 id="jinbound-reports-glance-views-to-leads"><?php echo $this->getViewsToLeads(); ?> %</h3>
        <?php if ($show_link) : ?>
            <a href="<?php echo JInboundHelperUrl::view('reports', false, array(
                'layout'       => 'chart',
                'filter_chart' => 'viewstoleads'
            )) ?>"><?php echo JText::_('COM_JINBOUND_VIEWS_TO_LEADS'); ?></a>
        <?php else : ?>
            <span><?php echo JText::_('COM_JINBOUND_VIEWS_TO_LEADS'); ?></span>
        <?php endif; ?>
    </div>
    <!-- arrow -->
    <div class="span1 text-center">
        <h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>"/></h3>
    </div>
    <!-- customers -->
    <div class="span1 text-center">
        <h3 id="jinbound-reports-glance-conversion-count"><?php echo $this->getConversionCount(); ?></h3>
        <?php if ($show_link) : ?>
            <a href="<?php echo JInboundHelperUrl::view('reports', false, array(
                'layout'       => 'chart',
                'filter_chart' => 'conversioncount'
            )) ?>"><?php echo JText::_('COM_JINBOUND_GOAL_COMPLETIONS'); ?></a>
        <?php else : ?>
            <span><?php echo JText::_('COM_JINBOUND_GOAL_COMPLETIONS'); ?></span>
        <?php endif; ?>
    </div>
    <!-- arrow -->
    <div class="span1 text-center">
        <h3><img src="<?php echo JInboundHelperUrl::media() . '/images/summary_arrows.png'; ?>"/></h3>
    </div>
    <!-- conversions -->
    <div class="span2 text-center">
        <h3 id="jinbound-reports-glance-conversion-rate"><?php echo $this->getConversionRate(); ?> %</h3>
        <?php if ($show_link) : ?>
            <a href="<?php echo JInboundHelperUrl::view('reports', false, array(
                'layout'       => 'chart',
                'filter_chart' => 'conversionrate'
            )) ?>"><?php echo JText::_('COM_JINBOUND_VIEWS_TO_GOAL_COMPLETIONS'); ?></a>
        <?php else : ?>
            <span><?php echo JText::_('COM_JINBOUND_VIEWS_TO_GOAL_COMPLETIONS'); ?></span>
        <?php endif; ?>
    </div>
</div>
