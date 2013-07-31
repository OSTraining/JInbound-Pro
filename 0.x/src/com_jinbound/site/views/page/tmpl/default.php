<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

echo $this->loadTemplate('layout');

?>
<?php if (defined('JDEBUG') && JDEBUG) : ?>
<pre><?php echo htmlspecialchars(print_r($this->item, 1)); ?></pre>
<pre><?php echo htmlspecialchars(print_r($this->form, 1)); ?></pre>
<?php endif; ?>
