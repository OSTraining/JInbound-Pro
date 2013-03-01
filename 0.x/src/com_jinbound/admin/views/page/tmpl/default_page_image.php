<?php
$imageFieldset = $this->form->getFieldset('imagetab');
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



	</div>
	<div class="span3">

		<div class="m">
			<strong>Tips Area</strong>
			<br/>
			<br/>
			<br/>
		</div>

	</div>
</div>




