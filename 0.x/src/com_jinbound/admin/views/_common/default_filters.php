<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$floatButtons = JInbound::version()->isCompatible('3.0');

?>
<fieldset id="filter-bar">
	<div class="filter-search fltlft btn-group pull-left">
		<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" class="input-medium search-query" value="<?php echo JInboundHelperFilter::escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_JINBOUND_FILTER_SEARCH_DESC'); ?>" />
		
<?php if (!$floatButtons) : ?>
		<button type="submit" class="btn btn-primary"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
<?php endif; ?>
		
	</div>
	
<?php if ($floatButtons) : ?>
	<div class="btn-group pull-left hidden-phone">
		<button type="submit" class="btn btn-primary tip hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button type="button" class="btn tip hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
	</div>
<?php endif; ?>

	<?php $this->renderFilters(); ?>
</fieldset>
<div class="clr"> </div>