<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_form
 **********************************************
 * JInbound
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

defined('_JEXEC') or die;

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('form');
JInbound::registerHelper('module');
JInbound::registerHelper('url');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.form');
jimport('joomla.application.module.helper');

abstract class modJinboundFormHelper
{
    public static function getFormAjax()
    {
        // reset the document
        $doc               = JFactory::getDocument();
        $doc->_scripts     = array();
        $doc->_script      = array();
        $doc->_styleSheets = array();
        $doc->_style       = array();
        // get the module id to load
        $input = JFactory::getApplication()->input;
        $id    = $input->getInt('id', 0);
        // get ORIGIN
        // TODO: whitelist? param?
        if (filter_has_var(INPUT_SERVER, 'HTTP_ORIGIN')) {
            $origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
        } else {
            $origin = (isset($_SERVER['HTTP_ORIGIN']) ? filter_var($_SERVER['HTTP_ORIGIN'],
                FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE) : null);
        }
        if (empty($origin)) {
            $origin = '*';
        }
        // set CORS headers
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description, Authorization, X-Requested-With');
        // get the module and render the form
        $module = JInboundHelperModule::getModuleObject();
        $returl = base64_encode(JUri::root(true));
        $return = $input->getBase64('return_url', $returl);
        if ($returl !== $return) {
            $module->params->set('after_submit_sendto', 'url');
            $module->params->set('send_to_url', base64_decode($return));
        }
        if (0 === (int)$module->params->get('allow_embed', 0)) {
            throw new Exception(JText::_('MOD_JINBOUND_FORM_EMBED_NOT_ALLOWED'), 401);
        }
        $attribs = array('style' => 'none');
        $form    = JModuleHelper::renderModule($module, $attribs);
        // get module-specific scripts
        $corescripts = $doc->_scripts;
        $corescript  = $doc->_script;
        $corestyles  = $doc->_styleSheets;
        $corestyle   = $doc->_style;
        // adjust the script structures
        $finalscripts = array();
        if (!empty($corescripts)) {
            $root = JUri::root(true);
            foreach ($corescripts as $script => $attributes) {
                // remove root if possible
                if (substr($script, 0, strlen($root)) === $root) {
                    $script = substr($script, strlen($root));
                }
                $finalscripts[] = array_merge($attributes, array('src' => $script));
            }
        }
        $finalscript = '';
        if (is_array($corescript) && array_key_exists('text/javascript', $corescript)) {
            $finalscript = $corescript['text/javascript'];
        }
        // send data structure
        return array(
            'form'    => $form,
            'styles'  => empty($corestyles) ? false : $corestyles,
            'style'   => empty($corestyle) ? false : $corestyle,
            'scripts' => empty($finalscripts) ? false : $finalscripts,
            'script'  => empty($finalscript) ? false : $finalscript,
            'session' => JFactory::getSession()->get('mod_jinbound_form.form.' . $id)
        );
    }

    /**
     * Fetches the module object needed to operate
     *
     * This method is deprecated, use JInboundHelperModule::getModuleObject instead
     *
     * @return stdClass
     * @throws UnexpectedValueException
     * @deprecated
     */
    public static function getModuleObject($module_id = null)
    {
        return JInboundHelperModule::getModuleObject($module_id);
    }

    public static function getFormData(&$module, &$params)
    {
        // initialise
        $campaign_id = (int)$params->get('campaignid', 0);
        $form_id     = (int)$params->get('formid', 0);
        if (empty($form_id) || empty($campaign_id)) {
            return false;
        }
        return (object)array(
            'campaign_id'         => $campaign_id,
            'form_id'             => $form_id,
            'page_name'           => preg_replace('/^mod_/', '', $module->module),
            'notification_email'  => $params->get('notification_email', ''),
            'after_submit_sendto' => $params->get('after_submit_sendto', 'message'),
            'menu_item'           => $params->get('menu_item', ''),
            'send_to_url'         => $params->get('send_to_url', ''),
            'sendto_message'      => $params->get('sendto_message', ''),
            'return_url'          => $params->get('return_url', JUri::root(false))
        );
    }

    public static function getForm(&$module, &$params)
    {
        return JInboundHelperForm::getJinboundForm((int)$params->get('formid', 0),
            array('control' => 'mod_jinbound_form_' . $module->id));
    }
}
