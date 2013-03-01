<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Base controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
class JinboundController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false) {
		$app        = JFactory::getApplication();
		$view       = $app->input->get('view', 'Page', 'cmd');

		$app->input->set('view', $view);
		parent::display($cachable);
	}
}
