<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$tags = $this->input->getTags();

?>
<div id="<?php echo $this->escape($this->input->id); ?>_tags" class="container-fluid">
<?php

if (!empty($tags)) :
	?>
	<ul id="<?php echo $this->escape($this->input->id); ?>_tags_list" class="jinbound_editor">
	<?php
	foreach ($tags as $tag) :
		?>
		<li class="jinbound_editor_tag">{<?php echo $this->escape($tag->value); ?>}</li>
		<?php
	endforeach;
	?>
	</ul>
	<?php
endif;
?>
</div>
