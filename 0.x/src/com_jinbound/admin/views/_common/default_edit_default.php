<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$fieldset = $this->form->getFieldset('default');
$well = false;
// do we have a well?
foreach ($fieldset as $field) :
	if (empty($well) && method_exists($field, 'getSidebar')) :
		$well = $field->getSidebar();
	endif;
endforeach;

?>
<div class="row-fluid">
	<div class="span<?php echo $well ? 9 : 12; ?>">
<?php
$this->_currentFieldset = $fieldset;
$this->loadTemplate('edit_fields');
?>
	</div>
<?php if (!empty($well)) : ?>
	<div class="span3 well">
		<?php echo $well; ?>
	</div>
<?php endif; ?>
</div>
