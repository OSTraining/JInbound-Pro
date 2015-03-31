<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_JEXEC') or die;

class JFormFieldJinboundsalesforcewsdl extends JFormField
{
	public $type = 'Jinboundsalesforcewsdl';
	
	protected function getInput()
	{
		$html = array();
		$link = 'index.php?option=plg_system_jinboundsalesforce&amp;view=form&amp;field=' . $this->id;
		
		// add a token to the frame
		$link .= '&amp;' . JFactory::getSession()->getFormToken() . '=1';
		
		// Initialize some field attributes.
		$attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->required ? ' required' : '';
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);

		// Build the script.
		$script = array();
		$script[] = 'function jSelectWsdl_' . $this->id . '(file) {';
		$script[] = '	var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '	if (old_id != file) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = file;';
		$script[] = '		document.getElementById("' . $this->id . '").value = file;';
		$script[] = '		document.getElementById("' . $this->id . '").className = document.getElementById("' . $this->id . '").className.replace(" invalid" , "");';
		$script[] = '		' . $this->onchange;
		$script[] = '	}';
		$script[] = '	jModalClose();';
		$script[] = '}';
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->required ? ' required' : '';
		
		// Create a dummy text field with the file name.
		$html[] = '<div class="input-append">';
		$html[] = '     <input type="text" id="' . $this->id . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
						. ' readonly' . $attr . ' />';
		
		// Create the file select button.
		if ($this->readonly === false)
		{
			$html[] = '             <a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_SELECT_WSDL') . '" href="' . $link . '"'
							. ' rel="{handler: \'iframe\', size: {x: 400, y: 200}}">';
			$html[] = '<i class="icon-file-check"></i></a>';
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the file name.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

		return implode("\n", $html);
	}
}
