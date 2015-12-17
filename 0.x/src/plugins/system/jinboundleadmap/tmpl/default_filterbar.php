<?php
/**
 * @package		JInbound
 * @subpackage	plg_system_jinboundleadmap
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
		<form action="<?php echo $this->data->url; ?>" method="post" name="adminForm" id="adminForm">
			<?php
			if (!empty($this->filterForm)) :
				if (class_exists('JLayoutHelper')) :
					echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this), null, array('debug' => false));
				else:
					echo $this->loadTemplate('filters');
				endif;
			endif;
			?>
		</form>