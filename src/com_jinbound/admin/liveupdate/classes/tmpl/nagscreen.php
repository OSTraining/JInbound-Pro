<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

$stability = JText::_('LIVEUPDATE_STABILITY_'.$this->updateInfo->stability);
?>

<div class="liveupdate">

	<div id="nagscreen">
		<h2><?php echo JText::_('LIVEUPDATE_NAGSCREEN_HEAD') ?></h2>
		
		<p class="nagtext"><?php echo JText::sprintf('LIVEUPDATE_NAGSCREEN_BODY', $this->updateInfo->version, $stability) ?></p>
	</div>
	<p class="liveupdate-buttons">
		<button class="btn btn-primary" onclick="window.location='<?php echo $this->runUpdateURL ?>'" ><?php echo JText::_('LIVEUPDATE_NAGSCREEN_BUTTON') ?></button>
	</p>

</div>