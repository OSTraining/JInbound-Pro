<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="jinbound-empty">
    <h3><?php echo JText::_('COM_JINBOUND_PAGES_EMPTY'); ?></h3>
    <div class="jinbound-empty-button">
        <div class="icon-wrapper">
            <div class="icon">
                <a href="<?php echo JInboundHelperUrl::task('page.add'); ?>">
                    <?php /*echo JHTML::_('jinbound.image', 'icon-48-page.png');*/ ?><br/>
                    <span><?php echo JText::_('COM_JINBOUND_PAGE_ADD_NEW'); ?></span></a>
            </div>
        </div>
    </div>
</div>
