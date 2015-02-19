<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$this->cols = 11;

echo $this->loadTemplate('list');

?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if ('reports.exportleads' === task)
	{
		setTimeout(function(){
			jQuery('#adminForm').find('input[name=\'task\']').val('');
		}, 3000);
	}
	Joomla.submitform(task, document.getElementById('adminForm'));
};
</script>
