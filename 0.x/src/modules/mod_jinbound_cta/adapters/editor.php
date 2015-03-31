<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
		if ($this->is_alt)
		{
			$content = $this->params->get('cta_alt_mode_editor');
		}
		else
		{
			$content = $this->params->get('cta_mode_editor');
		}
		// TODO trigger content plugins
		echo $content;
	}
}
