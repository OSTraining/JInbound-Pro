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
    <div class="span9">
      <!--Body content-->


      <div class="row-fluid" id="welcome_message">
      	<div class="span12">
      		Welcome Lorem ipsum dolor sit amet, consectetur adipiscing elit.
      	</div>
      </div>

      <div class="row-fluid">
      	<div class="span2" style="border:1px solid;">
      		Langing Page ico
      		<br /><br /><br />
      	</div>
      	<div class="span2" style="border:1px solid;">
      		Lead Nurturing ico
      		<br /><br /><br />
      	</div>
      	<div class="span2" style="border:1px solid;">
      		Lead Management ico
      		<br /><br /><br />
      	</div>
      	<div class="span2" style="border:1px solid;">
      		Reporting ico
      		<br /><br /><br />
      	</div>
      </div>

      <!-- Monthly Report -->

      <br />
      <div class="row-fluid">
      	<div class="span12" style="border:1px solid;">
      		<div style="width:100%; font-weight:bold; text-align:center;">
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
			<div style="float:left; width:30%; text-align:center;">
				<span style="font-size:300%;">10%</span>
				<br/><br />
				Copnversion Rates
			</div>
      	</div>
      	<div style="width:100%; text-align:right;"><?=JHtml::link('index.php?option=com_jinbound&view=reports', 'See More')?></div>
      </div>


	<strong>Recent Leads</strong>
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


    <strong>Top Performing Landing Pages</strong>
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
    <div class="span3">
      <!--Sidebar content-->


       <!-- actions-->
      <div class="m">

	  	Create a New:<br />
	  	<ul>
	  		<li>Landing Page</li>
			<li>Lead Nurturing Campaign</li>
			<li>Lead</li>
		</ul>

	  	View Reports:<br />
	  	<ul>
	  		<li>Conversions</li>
			<li>Landing Page Report</li>
			<li>Lead Nurturing Campaign</li>
		</ul>



	  </div>
	  <!-- end actions-->

      <br />

	  <div class="m">
	  	<!-- alerts -->
		  <strong>Alerts</strong>
	      <ul>
	      	<li>Cras nec augue augue, sit amet lobortis urna.</li>
	      	<li>Vivamus ac risus nulla, et eleifend purus.</li>
	      	<li>Quisque quis lorem ac massa tempor elementum.</li>
		  </ul>
	      <div style="width:100%; text-align:right;"><?=JHtml::link('index.php?option=com_jinbound', 'See More')?></div>


	      <br />

	      <div class="m" style="font-size:150%; text-align:center;">
	      	<?=JHtml::link('http://extensions.joomla.org', 'Rate us on JED')?>
	      </div>

	  		<br />
	  		Connect for Great Inbound Content: <br />
			<div id="social-icons" style="text-align:center;">

				<span style="padding-right:5px;"><?=JHtml::link('http://www.twitter.com', JHtml::image('/media/jinbound/images/twitter.jpg', 'Twitter', array('width'=>'36')), array('border'=>'0', 'target'=>'_blank'))?></span>
				<span style="padding-right:5px;"><?=JHtml::link('/', JHtml::image('/media/jinbound/images/rss.jpg', 'RSS', array('width'=>'36')), array('border'=>'0', 'target'=>'_blank'))?></span>
				<span style="padding-right:5px;"><?=JHtml::link('http://www.facebook.com', JHtml::image('/media/jinbound/images/facebook.jpg', 'Facebook', array('width'=>'36')), array('border'=>'0', 'target'=>'_blank'))?></span>
				<span style=""><?=JHtml::link('http://www.youtube.com', JHtml::image('/media/jinbound/images/youtube.jpg', 'YouTube', array('width'=>'36')), array('border'=>'0', 'target'=>'_blank'))?></span>

			</div>


	  	<!-- end alerts -->
	  </div>

	  <br />
	  <!-- lms ad -->
	  <div class="m">

      	<div style="width:100%; text-align:center; font-size:200%;">inbound LMS Ad Banner</div>

      </div>
      <!-- end lms ad -->


      <!--end Sidebar content-->
    </div>
  </div>
</div>