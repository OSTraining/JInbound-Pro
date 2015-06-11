<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundsalesforce
@ant_copyright_header@
 */

defined('_JEXEC') or die;

?>
<script type="text/javascript">
if (window.parent)
{
	window.parent.jSelectWsdl_<?php echo $this->escape($this->field); ?>('<?php echo $this->escape($this->file); ?>');
}
</script>