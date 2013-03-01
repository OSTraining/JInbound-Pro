<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

 **********************************************
 JInbound
 Copyright (c) 2012 Anything-Digital.com
 **********************************************
 JInbound is some kind of marketing thingy

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This header must not be removed. Additional contributions/changes
 may be added to this header as long as no information is deleted.
 **********************************************
 Get the latest version of JInbound at:
 http://anything-digital.com/
 **********************************************

 */

defined('JPATH_PLATFORM') or die;

?>

<div class="container-fluid" id="jinbound_component">
  <div class="row-fluid">
    <div class="span12">
      <!--Body content-->


      <!-- Monthly Report -->

      <br />
      <div class="row-fluid">
      	<div class="span12" style="border:1px solid;">
      		<br />
      		<div style="width:100%; font-weight:bold; text-align:center; font-size:200%;">
      			Monthly Reporting Snapshot
			</div>
			<br />
			<div style="float:left; width:30%; text-align:center;">
				<span style="font-size:300%;">1000</span>
				<br/><br />
				Website Visits
			</div>
			<div style="float:left; width:5%; text-align:center;">
				<span style="font-size:300%;">&rarr;</span>
			</div>
			<div style="float:left; width:30%; text-align:center;">
				<span style="font-size:300%;">100</span>
				<br/><br />
				Leads
			</div>
			<div style="float:left; width:5%; text-align:center;">
				<span style="font-size:300%;">&rarr;</span>
			</div>
			<div style="float:left; width:30%; text-align:center; clear:right;">
				<span style="font-size:300%;">10%</span>
				<br/><br />
				Conversion Rates
			</div>
			<br style="clear:both;" /><br />
      	</div>
      	<div style="width:100%; text-align:right;"><?=JHtml::link('index.php?option=com_jinbound&view=reports', 'See More')?></div>
      </div>


	<h3>Recent Leads</h3>
	<table border="1" width="100%">
		<tr>
			<th>Name</th>
			<th>Date</th>
			<th>Form Converted On</th>
			<th>Website</th>
		</tr>
		<?php for($i=0;$i<6;$i++) { ?>
		<tr>
			<td>Full Name</td>
			<td>October...</td>
			<td>Form Name</td>
			<td>http://www...</td>
		</tr>
		<?php } ?>
	</table>
	<div style="width:100%; text-align:right;"><?=JHtml::link('index.php?option=com_jinbound&view=leads', 'See More')?></div>


    <h3>Top Performing Landing Pages</h3>
	<table border="1" width="100%">
		<tr>
			<th>Landing Page</th>
			<th>Visits</th>
			<th>Conversions</th>
			<th>Conversion Rate</th>
		</tr>
		<?php for($i=0;$i<6;$i++) { ?>
		<tr>
			<td>Full Name</td>
			<td>100</td>
			<td>10</td>
			<td>10%</td>
		</tr>
		<?php } ?>
	</table>
	<div style="width:100%; text-align:right;"><?=JHtml::link('index.php?option=com_jinbound&view=pages', 'See More')?></div>


      <!-- end Body content-->
    </div>


  </div>
</div>