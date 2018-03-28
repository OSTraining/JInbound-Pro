<?php
/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundleadmap
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


?>
<div class="container-fluid" id="jinbound_component">
    <?php
    $mainAttribs = array(
        'id' => 'j-main-container'
    );
    if (!empty($this->sidebar)) :
        $mainAttribs['class'] = 'span10';
        ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
    <?php
    endif;
    ?>
    <div <?php echo ArrayHelper::toString($mainAttribs); ?>>
        <?php
        if ($this->data instanceof Exception) :
            ?>
            <div class="alert alert-error">
                <?php echo $this->data->getMessage(); ?>
                <?php
                if (JDEBUG) :
                    ?>
                    <pre><?php echo $this->data->getTraceAsString(); ?></pre>
                <?php
                endif;
                ?>
            </div>
        <?php
        elseif (empty($this->data->locations)) :
        ?>
            <div class="alert alert-error">
                <?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_NO_LOCATIONS_FOUND'); ?>
            </div>
        <?php
        echo $this->loadTemplate('filterbar');

        else :
        ?>
            <div id="map"></div>
            <div id="legend">
                <div id="legend-container"><h3><?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_LEGEND'); ?></h3>
                </div>
            </div>

            <div class="alert alert-success">
                <p>
                    <?php
                    echo JText::sprintf('PLG_SYSTEM_JINBOUNDLEADMAP_UPDATE_MAXMIND_DB', $this->download_url);
                    ?>
                </p>
                <p>This product includes GeoLite2 data created by MaxMind, available from
                    <a href="http://www.maxmind.com">http://www.maxmind.com</a>.</p>
            </div>

            <script type="text/javascript">
                function mapReady(f) {
                    /in/.test(document.readyState) ? setTimeout('mapReady(' + f + ')', 9) : f()
                }

                mapReady(function() {
                    var latLngs = <?php echo json_encode($this->data->locations); ?>;
                    var centerLatLng = {lat: 30, lng: 0};
                    var legend = document.getElementById('legend');
                    var legendContainer = document.getElementById('legend-container');
                    var guestLegend = document.createElement('div');
                    var leadLegend = document.createElement('div');

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom  : 2,
                        center: centerLatLng
                    });

                    var guestIcon = new StyledIcon(StyledIconTypes.MARKER, {color: "#0000ff"});
                    var leadIcon = new StyledIcon(StyledIconTypes.MARKER, {color: "#ff0000"});

                    guestLegend.innerHTML = '<img src="' + guestIcon.icon + '" /> <?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_LEGEND_GUEST'); ?>';
                    leadLegend.innerHTML = '<img src="' + leadIcon.icon + '" /> <?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_LEGEND_LEAD'); ?>';

                    legendContainer.appendChild(guestLegend);
                    legendContainer.appendChild(leadLegend);

                    map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);

                    for (var i = 0, n = latLngs.length; i < n; i++) {
                        var marker = new StyledMarker({
                            position : {lat: latLngs[i].latitude, lng: latLngs[i].longitude},
                            map      : map,
                            title    : latLngs[i].city,
                            styleIcon: latLngs[i].lead ? leadIcon : guestIcon
                        });
                        if (latLngs[i].lead) {
                            marker._jib_lead = latLngs[i].lead;
                            marker.addListener('click', function() {
                                window.location.href = 'index.php?option=com_jinbound&task=contact.edit&id=' + this._jib_lead;
                            });
                        }
                    }
                });
            </script>
            <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
            <script type="text/javascript" src="../media/jinboundleadmap/js/StyledMarker.js"></script>
        <?php
        endif;
        ?>
    </div>
</div>
