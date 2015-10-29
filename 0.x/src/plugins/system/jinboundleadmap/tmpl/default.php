<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundleadmap
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="container-fluid" id="jinbound_component">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<?php if ($this->data instanceof Exception) : ?>
		<div class="alert alert-error">
			<?php echo $this->data->getMessage(); ?>
			<?php if (JDEBUG) : ?>
			<pre><?php echo $this->data->getTraceAsString(); ?></pre>
			<?php endif; ?>
		</div>
<?php elseif (empty($this->data->locations)) : ?>
		<div class="alert alert-error">
			<?php echo JText::_('PLG_SYSTEM_JINBOUNDLEADMAP_NO_LOCATIONS_FOUND'); ?>
		</div>
<?php else : ?>
		<div id="map" style="min-height:500px"></div>
		<script type="text/javascript">
function mapReady(f){/in/.test(document.readyState)?setTimeout('mapReady('+f+')',9):f()}
mapReady(function() {
  var latLngs = <?php echo json_encode($this->data->locations); ?>;
	var centerLatLng = {lat:30, lng: 0};

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 2,
		center: centerLatLng,
		mapTypeId: google.maps.MapTypeId.SATELLITE
  });
	
	var guestIcon = new StyledIcon(StyledIconTypes.MARKER, {color:"#0000ff"});
	var leadIcon  = new StyledIcon(StyledIconTypes.MARKER, {color:"#ff0000"});
	
	for (var i = 0, n = latLngs.length; i < n; i++) {
		var marker = new StyledMarker({
			position: {lat: latLngs[i].latitude, lng: latLngs[i].longitude},
			map: map,
			title: latLngs[i].city,
			styleIcon: latLngs[i].lead ? leadIcon : guestIcon
		});
	}
});
		</script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="../media/jinboundleadmap/js/StyledMarker.js"></script>
<?php endif; ?>
	</div>
</div>
