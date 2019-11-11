<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of jInbound-Pro.
 *
 * jInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * jInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

class JibFormFieldSalesforcewsdl extends JFormField
{
    public $type = 'Jinboundsalesforcewsdl';

    protected function getInput()
    {
        $html = array();

        $formToken = JFactory::getSession()->getFormToken();
        $linkQuery = array(
            'option'   => 'plg_system_jinboundsalesforce',
            'view'     => 'form',
            'field'    => $this->id,
            $formToken => '1'
        );
        $link      = 'index.php?' . http_build_query($linkQuery);

        JHtml::_('behavior.modal', 'a.modal_' . $this->id);

        // Build the script.
        $script   = array();
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
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        $attributes = array_filter(
            array(
                'type'     => 'text',
                'id'       => $this->id,
                'value'    => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
                'class'    => $this->class,
                'size'     => $this->size,
                'required' => $this->required ? 'required' : '',
                'readonly' => 'readonly'
            )
        );

        $html[] = '<div class="input-append">';
        $html[] = sprintf('<input %s/>', ArrayHelper::toString($attributes));

        if (!$this->readonly) {
            $html[] = sprintf(
                '<a class="btn btn-primary modal_%s" title="%s" href="%s" rel="{handler: \'iframe\', size: {x: 400, y: 200}}">',
                $this->id,
                JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_SELECT_WSDL'),
                $link
            );
            $html[] = 'Choose File';
            $html[] = '</a>';
        }

        $html[] = '</div>';

        $html[] = sprintf(
            '<input type="hidden" id="%s_id" name="%s" value="%s"/>',
            $this->id,
            $this->name,
            $this->value
        );

        return implode("\n", $html);
    }
}
