<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!empty($this->listbuttons)) : ?>
<div class="jinbound_listbuttons">
	<?php foreach ($this->listbuttons as $button) : ?>
	<div class="jinbound_listbutton">
		new button
	</div>
	<?php endforeach; ?>
</div>
<?php
endif;