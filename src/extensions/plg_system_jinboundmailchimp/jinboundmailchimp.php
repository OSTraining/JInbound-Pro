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

defined('JPATH_PLATFORM') or die;

class plgSystemJInboundmailchimp extends JPlugin
{
    /**
     * @var JApplicationCms
     */
    protected $app = null;

    /**
     * @var bool
     */
    protected static $enabled = null;

    /**
     * Constructor
     *
     * @param JEventDispatcher $subject
     * @param array            $config
     *
     * @return void
     * @throws Exception
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->app = JFactory::getApplication();
        $this->loadLanguage('plg_system_jinboundmailchimp');
        $this->loadLanguage('plg_system_jinboundmailchimp.sys');
    }

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        if (static::$enabled === null) {
            if (!defined('JINB_LOADED')) {
                $includePath = JPATH_ADMINISTRATOR . '/components/com_jinbound/include.php';
                if (is_file($includePath)) {
                    require_once $includePath;
                }
            }
            JLoader::register('JinboundMailchimp', realpath(__DIR__ . '/library/helper.php'));

            static::$enabled = defined('JINB_LOADED') && $this->params->get('mailchimp_key');
        }

        return static::$enabled;
    }

    /**
     * @param JForm $form
     *
     * @return bool
     */
    public function onContentPrepareForm($form)
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }
        if (!$form instanceof JForm) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }

        switch ($form->getName()) {
            case 'com_jinbound.campaign':
                $file = 'jinboundmailchimp';
                break;

            case 'com_jinbound.field':
                $file = 'jinboundmailchimpfield';
                break;

            case 'com_jinbound.contact':
                if ($this->app->isClient('site')) {
                    return true;
                }
                $file = 'jinboundmailchimpcontact';
                break;

            default:
                return true;
        }

        JForm::addFormPath(dirname(__FILE__) . '/form');
        JForm::addFieldPath(dirname(__FILE__) . '/field');
        $result = $form->loadFile($file, false);

        return $result;
    }

    /**
     * @param string $context
     * @param int    $campaignId
     * @param int[]  $contacts
     * @param int    $statusId
     *
     * @return bool
     * @throws Exception
     */
    public function onJInboundChangeState($context, $campaignId, $contacts, $statusId)
    {
        if ($context !== 'com_jinbound.contact.status' || !$this->isEnabled()) {
            return;
        }

        if (JDEBUG) {
            $this->app->enqueueMessage(__METHOD__);
        }

        $helper = new JinboundMailchimp(array('params' => $this->params));

        foreach ($contacts as $contactId) {
            $helper->onJinboundSetStatus($campaignId, $contactId);
        }

        return true;
    }
}
