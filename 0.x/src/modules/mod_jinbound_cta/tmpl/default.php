<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

?>
<div class="jinbound-cta<?php echo $sfx; ?>">
	<?php ModJInboundCTAHelper::getAdapter($params)->render(); ?>
</div>