<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2020 Joomlashack.com. All rights reserved
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

class plgSystemJInboundacymailing extends JPlugin
{
    /**
     * @var bool
     */
    protected static $enabled = null;

    /**
     * Constructor
     *
     * @param JEventDispatcher $subject
     * @param array            $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage('plg_system_jinboundacymailing.sys');

        JLoader::register('JinboundAcymailing', __DIR__ . '/helper/helper.php');
    }

    /**
     * @param JForm $form
     *
     * @return bool
     * @throws Exception
     */
    public function onContentPrepareForm($form)
    {
        if ($this->isEnabled() && $form instanceof JForm) {
            switch ($form->getName()) {
                case 'com_jinbound.campaign':
                    $file = 'jinboundacymailing';
                    break;

                case 'com_jinbound.contact':
                    if (JFactory::getApplication()->isClient('administrator')) {
                        $file = 'jinboundacymailingcontact';
                    }
                    break;

            }

            if (!empty($file)) {
                JForm::addFormPath(__DIR__ . '/form');
                JForm::addFieldPath(__DIR__ . '/field');
                $result = $form->loadFile($file, false);

                return $result;
            }
        }

        return true;
    }

    /**
     * @param string $context
     * @param int    $campaign_id
     * @param int[]  $contacts
     * @param int    $status_id
     *
     * @return void
     */
    public function onJInboundChangeState($context, $campaign_id, $contacts, $status_id)
    {
        if ($context === 'com_jinbound.contact.status' && $this->isEnabled()) {
            $helper = new JinboundAcymailing(array('params' => $this->params));

            foreach ($contacts as $contact_id) {
                $helper->onJinboundSetStatus($status_id, $campaign_id, $contact_id);
            }
        }
    }

    /**
     * @param string $how
     * @param int    $contact_id
     * @param int    $campaign_id
     * @param int    $value
     * @param bool   $result
     *
     * @return array
     */
    public function onJInboundAfterJsonChangeState($how, $contact_id, $campaign_id, $value, $result)
    {
        if ($result && $how === 'status' && $this->isEnabled()) {
            $db    = JFactory::getDbo();
            $email = $db->setQuery(
                $db->getQuery(true)
                    ->select('email')
                    ->from('#__jinbound_contacts')
                    ->where('id = ' . intval($contact_id))
            )->loadResult();

            $helper = new JinboundAcymailing(array('params' => $this->params));

            return array(
                'acymailing' => $helper->getListTable($email, 'jform_acymailing_table')
            );
        }

        return null;
    }

    /**
     * This plugin requires both jInbound and acymailing to be installed and enabled
     *
     * @return bool
     */
    protected function isEnabled()
    {
        if (static::$enabled === null) {
            $db         = JFactory::getDbo();
            $components = $db->setQuery(
                $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from('#__extensions')
                    ->where(
                        array(
                            sprintf(
                                '%s IN (%s, %s)',
                                $db->quoteName('element'),
                                $db->quote('com_acymailing'),
                                $db->quote('com_jinbound')
                            ),
                            $db->quoteName('enabled') . ' = 1'
                        )
                    )
            )->loadResult();

            static::$enabled = ($components == 2);
        }

        return static::$enabled;
    }
}
