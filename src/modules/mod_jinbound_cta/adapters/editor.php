<?php
/**
 * @package		JInbound
 * @subpackage	mod_jinbound_cta
@ant_copyright_header@
 */

defined('_JEXEC') or die;

class ModJInboundCTAEditorAdapter extends ModJInboundCTAAdapter
{
	/**
	 * Renders a module
	 * @return string
	 */
	public function render()
	{
		$content = $this->params->get($this->pfx . 'mode_editor');
		// TODO trigger content plugins
		echo $content;
	}
}
