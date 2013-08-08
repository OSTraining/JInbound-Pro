<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
	<div>
		<input type="hidden" name="option" value="com_jinbound" />
		<input type="hidden" name="task" value="lead.save" />
		<input type="hidden" name="page_id" value="<?php echo (int) $this->item->id; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get('Itemid', 0, 'int'); ?>" />
	</div>
</form>