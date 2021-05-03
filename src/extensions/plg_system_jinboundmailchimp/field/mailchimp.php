<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
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

use Joomla\CMS\Form\FormField;

defined('_JEXEC') or die;

class JinboundFormFieldMailchimp extends FormField
{
    /**
     * @var string
     */
    protected $layout = 'jinbound.field.mailchimp';

    /**
     * @var FormField
     */
    protected $emailField = null;

    /**
     * @inheritDoc
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        if (parent::setup($element, $value, $group)) {
            if ($emailField = (string)$element['emailfield']) {
                $this->emailField = $this->form->getField($emailField, $group);
            }

            return $this->emailField instanceof JFormField;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getRenderer($layoutId = 'default')
    {
        $renderer = parent::getRenderer($layoutId);

        if ($layoutId == $this->layout) {
            $renderer->addIncludePath(JPATH_PLUGINS . '/system/jinboundmailchimp/layouts');
        }

        return $renderer;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function getLayoutData()
    {
        $email = $this->emailField ? $this->emailField->value : '';
        if ($email) {
            $plugin = JPluginHelper::getPlugin('system', 'jinboundmailchimp');
            require_once realpath(__DIR__ . '/../library/helper.php');
            $helper = new JinboundMailchimp(array('params' => $plugin->params));

            $memberships = $helper->getMemberships($email);
        }

        $fieldData = array(
            'email'   => $email,
            'memberships' => empty($memberships) ? array() : $memberships
        );

        return array_merge(parent::getLayoutData(), $fieldData);
    }

    /**
     * @inheritDoc
     */
    protected function getLabel()
    {
        return '';
    }
}
