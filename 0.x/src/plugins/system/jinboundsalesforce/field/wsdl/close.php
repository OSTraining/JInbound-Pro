<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

?>
<script type="text/javascript">
if (window.parent)
{
	window.parent.jSelectWsdl_<?php echo $this->escape($this->field); ?>('<?php echo $this->escape($this->file); ?>');
}
</script>