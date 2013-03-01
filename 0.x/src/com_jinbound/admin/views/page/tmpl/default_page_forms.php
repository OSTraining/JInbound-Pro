<?php
$imageFieldset = $this->form->getFieldset('formtop');
?>
 <div class="row-fluid">
    <div class="span9">
		<fieldset class="adminform" style="padding:0px; border:0px;">
			<ul class="adminformlist">
				<?php foreach ($imageFieldset as $name => $field): ?>
					<li><?php echo $field->label; ?></li>
					<li><?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>

	</fieldset>


			<div class="clearfix"></div>
		<strong>Fields</strong>
		<div style="border:1px solid; width:80%; height:300px;">

		</div>

		<div class="clearfix"></div>

<?php
$imageFieldset = $this->form->getFieldset('formbottom');
?>

		<fieldset class="adminform" style="padding:0px; border:0px;">
			<ul class="adminformlist">
				<?php foreach ($imageFieldset as $name => $field): ?>
					<li><?php echo $field->label; ?></li>
					<li><?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>

	</fieldset>





	</div>
	<div class="span3">

		<div class="m">
			<strong>Tips Area</strong>
			<br/>
			<br/>
			<br/>
		</div>


		<Br />

	<div class="m">
		<strong>Fields</strong><br />
			<?php

echo JHtml::_('tabs.start');
echo JHtml::_('tabs.panel', 'Add A Field', 'page-addfield');
?>

			<div class="jinbound_addfield" id="jifield_fname">First Name</div>
			<div class="jinbound_addfield" id="jifield_lname">Last Name</div>
			<div class="jinbound_addfield" id="jifield_email">Email</div>
			<div class="jinbound_addfield" id="jifield_website">Website</div>
			<div class="jinbound_addfield" id="jifield_company">Company Name</div>
			<div class="jinbound_addfield" id="jifield_phone">Phone Number</div>
			<div class="jinbound_addfield" id="jifield_address">Full Address</div>
			<div class="jinbound_addfield" id="jifield_linetext">Single Line of Text</div>
			<div class="jinbound_addfield" id="jifield_paragraph">Paragraph of Text</div>
			<div class="jinbound_addfield" id="jifield_checkbox">Checkboxes</div>
			<div class="jinbound_addfield" id="jifield_"radio">Radio Buttons</div>
			<div class="jinbound_addfield" id="jifield_select">Drop Down Menu</div>

			<div class="clearfix"></div>

			<?php
echo JHtml::_('tabs.panel', 'Field Settings', 'page-fieldsettings');
?>

			<label>Field Title:</label><Br/><input type="text"/><Br/><Br/>
			<input type="checkbox" checked="checked"/> Required? <br/><br/>
			<label>Choices:</label><br/>
			<input type="text"/><br/><br/>
			<input type="text"/><br/><Br/>
			<div style="float:right;"><a href="#">+ Add Choice</a></div>

			<div class="clearfix"></div>

			<?php
echo JHtml::_('tabs.end');
?>



		</div>

		<Br />



	</div>
</div>


