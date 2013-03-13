<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<?php echo $this->escape($this->_currentField['title']); ?>
</div>
<div class="row-fluid">
	<input name="<?php echo $this->escape($this->_currentFieldName); ?>" type="text" value="" />
</div>