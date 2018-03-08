<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

?>
<div class="jinbound-cta<?php echo $sfx; ?>">
    <?php ModJInboundCTAHelper::getAdapter($params)->render(); ?>
</div>
