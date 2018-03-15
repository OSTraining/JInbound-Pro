<?php
/**
 * @package             jInbound
 * @subpackage          mod_jinbound_cta
 **********************************************
 * jInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('JPATH_PLATFORM') or die;

if (!defined('JINB_LOADED')) {
    $path = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
    if (is_file($path)) {
        require_once $path;
    }
}

class JFormFieldModJInboundFormEmbed extends JFormField
{
    public $type = 'ModJInboundFormEmbed';

    protected function getInput()
    {
        $module_id = (int)$this->form->getValue('id');
        $published = (int)$this->form->getValue('published');
        $output    = JText::_('MOD_JINBOUND_FORM_EMBED_SAVE_MODULE_FIRST');

        // change the message if module has an ID
        if (!empty($module_id) && 1 === $published) {
            $attrs = array(
                'src'           => JInboundHelperUrl::toFull(JUri::root(false) . 'media/mod_jinbound_form/js/form.js'),
                'data-j-ref'    => 'jinbound',
                'data-j-module' => $module_id
            );
            $script = '<script async';
            foreach ($attrs as $attr => $value) {
                $script .= ' ' . $attr . '="' . $value . '"';
            }
            $script .= '></script>';
            $output = JText::sprintf('MOD_JINBOUND_FORM_EMBED_SCRIPT', htmlspecialchars($script, ENT_QUOTES, 'UTF-8'));
        }

        return $output;
    }
}
