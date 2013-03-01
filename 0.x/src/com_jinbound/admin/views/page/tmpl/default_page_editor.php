 <div class="row-fluid">
    <div class="span9">


    <?php
    $imageFieldset = $this->form->getFieldset('template');
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

		<br/>

	<div class="m">
		<strong>Template Key</strong>
	<table width="100%" border="1">
		<tr><td nowrap="nowrap" style="background-color:#C2DFFF;">{heading}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{subheading}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{maintext}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{sidebartext}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{image}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form}</td><td style="background-color:#C2DFFF;">entire form</td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: firstname}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: lastname}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: email}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: website}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: companyname}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:phonenumber}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form: fulladdress}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:singleline:fieldtitle}</td><td style="background-color:#C2DFFF;">the field title must be unique</td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:paragraph: fieldtitle}</td><td style="background-color:#C2DFFF;">the field title must be unique</td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:checkbox: fieldtitle}</td><td style="background-color:#C2DFFF;">the field title must be unique</td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:radio: fieldtitle}</td><td style="background-color:#C2DFFF;">the field title must be unique</td></tr>
		<tr><td style="background-color:#C2DFFF;">{form:dropdown: fieldtitle}</td><td style="background-color:#C2DFFF;">the field title must be unique</td></tr>
		<tr><td style="background-color:#C2DFFF;">{captcha}</td><td style="background-color:#C2DFFF;"></td></tr>
		<tr><td style="background-color:#C2DFFF;">{submit}</td><td style="background-color:#C2DFFF;"></td></tr>
		</table>
	</div>


	</div>
</div>