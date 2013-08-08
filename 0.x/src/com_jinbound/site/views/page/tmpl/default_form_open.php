<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
		<form action="<?php echo JInboundHelperUrl::task('lead.save', true, array('page_id' => (int) $this->item->id)); ?>" method="post">