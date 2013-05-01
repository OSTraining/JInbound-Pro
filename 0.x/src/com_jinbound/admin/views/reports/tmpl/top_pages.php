<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$pages = $this->getTopLandingPages();

?>
<!-- Row 6: Landing Pages -->
<div class="row-fluid">
	<h4><?php echo JText::_('COM_JINBOUND_TOP_PERFORMING_LANDING_PAGES'); ?></h4>
	<table class="table table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_JINBOUND_LANDING_PAGE_NAME'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_VISITS'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_CONVERSIONS'); ?></th>
				<th><?php echo JText::_('COM_JINBOUND_CONVERSION_RATE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php if (!empty($pages)) foreach ($pages as $page) : ?>
			<tr>
				<td><a href="<?php echo JInboundHelperUrl::edit('page', $page->id, false); ?>"><?php echo $this->escape($page->name); ?></a></td>
				<td><?php echo $this->escape($page->hits); ?></td>
				<td><?php echo $this->escape($page->conversions); ?></td>
				<td><?php echo $this->escape($page->conversion_rate); ?></td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
</div>