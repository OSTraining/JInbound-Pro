<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

echo $this->loadTemplate('edit');


/*


$topFieldset = $this->form->getFieldset('top');
$hiddenFieldset   = $this->form->getFieldset('hidden');


?>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
  var j = jQuery.noConflict();
</script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="<?=JURI::root(true)?>/media/jinbound/js/formbuilder.js"></script>

<style>
  #sortable1, #sortable2 { list-style-type: none; margin: 0; padding: 0 0 2.5em; float: left; margin-right: 10px; }
#sortable1 li, #sortable2 li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 120px; }
</style>
  <script>
$(function() {
	$( "#sortable1, #sortable2" ).sortable({
		connectWith: ".connectedSortable"
	}).disableSelection();
});
  </script>


<form action="<?php echo JURI::base() . 'index.php?option=com_jinbound&task=location.save&id=' . (int) $this->item->id; ?>" method="post" id="location-form" name="adminForm" class="form-validate" enctype="multipart/form-data">

<div class="">
		<fieldset class="adminform" style="padding:0px; border:0px;">
		<ul class="adminformlist">
		<?php foreach ($topFieldset as $name => $field): ?>
			<li><?php echo $field->label; ?></li>
			<li><?php echo $field->input; ?></li>
		<?php endforeach; ?>
		</ul>
		</fieldset>
</div>

<div class="clearfix"></div>

<div id="panes">
	<?php
		// start pane
		echo JHtml::_('tabs.start');
		echo JHtml::_('tabs.panel', 'Content', 'page-content');
		echo $this->loadTemplate('page_content');

		echo JHtml::_('tabs.panel', 'Image', 'page-image');
		echo $this->loadTemplate('page_image');

		echo JHtml::_('tabs.panel', 'Forms', 'page-forms');
		echo $this->loadTemplate('page_forms');

		echo JHtml::_('tabs.panel', 'SEO', 'page-seo');
		echo $this->loadTemplate('page_seo');

		echo JHtml::_('tabs.panel', 'Template Editor', 'page-editor');
		echo $this->loadTemplate('page_editor');

		echo JHtml::_('tabs.end');
	?>
</div>

<div>
		<?php foreach ($hiddenFieldset as $name => $field) echo $field->input; ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="function" value="<?php echo JRequest::getCmd('function'); ?>" />
		<?php if ('component' == JRequest::getCmd('tmpl')) : ?>
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="mlayout" value="modal" />
		<?php endif; ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>

*/