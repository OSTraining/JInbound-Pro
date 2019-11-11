<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
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
                <?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_LOCATIONS_NOT_FOUND'); ?>
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
                    echo JText::sprintf(
                        'PLG_SYSTEM_JINBOUNDLEADMAP_MAXMIND_UPDATE_DB',
                        $this->maxmindDownloadUrl,
                        $this->maxmindDBUrl,
                        str_replace(JPATH_ROOT, '', $this->maxmindDB)
                    );
                    ?>
                </p>
                <p>
                    <?php
                    echo JText::sprintf(
                        'PLG_SYSTEM_JINBOUNDLEADMAP_MAXMIND_CREDITS',
                        'http://www.maxmind.com'
                    );
                    ?>
                </p>
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
