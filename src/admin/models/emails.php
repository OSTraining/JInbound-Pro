<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
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

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundListModel', 'models/basemodellist');

/**
 * This models supports retrieving lists of emails.
 *
 * @package        jInbound
 * @subpackage     com_jinbound
 */
class JInboundModelEmails extends JInboundListModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    public    $_context = 'com_jinbound.emails';
    protected $context  = 'com_jinbound.emails';

    /**
     * Constructor.
     *
     * @param       array   An optional associative array of configuration settings.
     *
     * @see         JController
     */
    function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'Campaign.name'
            ,
                'Email.name'
            ,
                'Email.type'
            ,
                'Email.published'
            ,
                'Email.sendafter'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to send all the emails that need to be sent
     *
     */
    public function send()
    {
        $dispatcher = JDispatcher::getInstance();
        $this->sendCampaignEmails();
        $dispatcher->trigger('onJInboundSend');
    }

    public function sendCampaignEmails()
    {
        JInbound::registerHelper('url');
        JPluginHelper::importPlugin('content');

        $db         = $this->getDbo();
        $out        = JInbound::config("debug", 0);
        $interval   = $out ? 'MINUTE' : 'DAY';
        $now        = JFactory::getDate();
        $params     = new JRegistry;
        $dispatcher = JDispatcher::getInstance();
        $limit      = (int)JInbound::config("cron_max_campaign_mails", 0);
        $query      = $db->getQuery(true)
            ->select('Contact.first_name AS first_name')
            ->select('Contact.last_name AS last_name')
            ->select('Contact.created AS created')
            ->select('Conversion.formdata AS form')
            ->select('Contact.email AS email')
            ->select('Contact.id AS contact_id')
            ->select('Conversion.id AS conversion_id')
            ->select('Page.id AS page_id')
            ->select('Page.formname AS form_name')
            ->select('Campaign.id AS campaign_id')
            ->select('Campaign.name AS campaign_name')
            ->select('Email.id AS email_id')
            ->select('Email.sendafter AS sendafter')
            ->select('Email.fromname AS fromname')
            ->select('Email.fromemail AS fromemail')
            ->select('Email.subject AS subject')
            ->select('Email.htmlbody AS htmlbody')
            ->select('Email.plainbody AS plainbody')
            ->select('Record.id AS record_id')
            ->select('MAX(Version.id) AS version_id')
            ->from('#__jinbound_contacts AS Contact')
            ->leftJoin('#__jinbound_conversions AS Conversion ON Conversion.contact_id = Contact.id')
            ->leftJoin('#__jinbound_pages AS Page ON Conversion.page_id = Page.id')
            ->leftJoin('#__jinbound_campaigns AS Campaign ON Page.campaign = Campaign.id')
            ->leftJoin('#__jinbound_emails AS Email ON Email.campaign_id = Campaign.id')
            ->leftJoin('#__jinbound_emails_records AS Record ON Record.lead_id = Contact.id AND Record.email_id = Email.id')
            ->leftJoin('#__jinbound_subscriptions AS Sub ON Contact.id = Sub.contact_id')
            ->leftJoin('#__jinbound_emails_versions AS Version ON Version.email_id = Email.id')
            ->where('Record.id IS NULL')
            // TODO add date column to contacts_campaigns to prevent contacts from slipping their email dates
            ->where('DATE_ADD(Conversion.created, INTERVAL Email.sendafter ' . $interval . ') < UTC_TIMESTAMP()')
            ->where('Email.type = ' . $db->quote('campaign'))
            ->where('Email.published = 1')
            ->where('Page.published = 1')
            ->where('Campaign.published = 1')
            ->where('Sub.enabled <> 0')
            // NOTE: Grouping order is VERY important here!!!!!
            // the query has to be grouped FIRST by emails, THEN by contacts
            // otherwise we don't get the correct data!!!!!!
            ->group('Email.id')
            ->group('Contact.id');

        if ($limit) {
            $query->setLimit($limit);
        }

        if ($out) {
            echo '<h3>Query</h3><pre>' . print_r((string)$query, 1) . '</pre>';
        }

        try {
            $results = $db->setQuery($query)->loadObjectList();
            if (empty($results)) {
                throw new Exception('No records found');
            }
        } catch (Exception $e) {
            if ($out) {
                echo $e->getMessage() . "\n<pre>" . $e->getTraceAsString() . "</pre>";
            }
            return;
        }

        foreach ($results as $result) {
            // parse form data
            $reg = new JRegistry;
            $reg->loadString($result->form);
            $arr  = $reg->toArray();
            $tags = array();
            foreach (array_keys($arr['lead']) as $tag) {
                $tags[] = 'email.lead.' . $tag;
            }
            array_unique($tags);
            $reg = $reg->toObject();
            // trigger an event before parsing
            $status = $dispatcher->trigger('onContentBeforeDisplay',
                array('com_jinbound.email', &$result, &$params, 0));
            //if (in_array(false, $status, true)) {
            //continue;
            //}
            // replace email tags
            $result->htmlbody  = $this->_replaceTags($result->htmlbody, $reg, $tags);
            $result->plainbody = $this->_replaceTags($result->plainbody, $reg, $tags);
            // replace other tags
            $tags              = array('email.campaign_name', 'email.form_name');
            $result->htmlbody  = $this->_replaceTags($result->htmlbody, $result, $tags);
            $result->plainbody = $this->_replaceTags($result->plainbody, $result, $tags);
            // add unsubscribe link to email contents
            if (JInbound::config('unsubscribe', 1)) {
                $unsubscribe       = JInboundHelperUrl::toFull(JInboundHelperUrl::task('unsubscribe', false,
                    array('email' => $result->email)));
                $result->htmlbody  = $result->htmlbody . JText::sprintf('COM_JINBOUND_UNSUBSCRIBE_HTML', $unsubscribe);
                $result->plainbody = $result->plainbody . JText::sprintf('COM_JINBOUND_UNSUBSCRIBE_PLAIN',
                        $unsubscribe);
            }
            // trigger an event after parsing
            $dispatcher->trigger('onContentAfterDisplay', array('com_jinbound.email', &$result, &$params, 0));

            if ($out) {
                echo '<h3>Result</h3><pre>' . htmlspecialchars(print_r($result, 1)) . '</pre>';
            }

            $mailer = JFactory::getMailer();
            $mailer->ClearAllRecipients();
            $mailer->SetFrom($result->fromemail, $result->fromname);
            $mailer->addRecipient($result->email, $result->first_name . ' ' . $result->last_name);
            $mailer->setSubject($result->subject);
            $mailer->setBody($result->htmlbody);
            $mailer->IsHTML(true);
            $mailer->AltBody = $result->plainbody;

            if ($out) {
                echo('<h3>Mailer</h3><pre>' . print_r($mailer, 1) . '</pre>');
            }

            $sent = $mailer->Send();

            if (!$sent) {
                if ($out) {
                    echo('<h3>COULD NOT SEND MAIL!!!!</h3>');
                }
                continue;
            }
            $object             = new stdClass;
            $object->email_id   = $result->email_id;
            $object->lead_id    = $result->contact_id;
            $object->sent       = $now->toSql();
            $object->version_id = $result->version_id;
            try {
                $db->insertObject('#__jinbound_emails_records', $object);
            } catch (Exception $e) {
                if ($out) {
                    echo $e->getMessage() . "\n" . $e->getTraceAsString();
                }
                continue;
            }
        }

        echo "\n";
    }

    public static function _replaceTags($string, $object, $extra = false)
    {
        $out = false;//JInbound::config("debug", 0);
        if ($out) {
            echo('<h3>Email Tags</h3>');
        }
        $tags = array(
            'email.lead.first_name'
        ,
            'email.lead.last_name'
        ,
            'email.lead.email'
        );

        if ($extra && is_array($extra)) {
            $tags = array_merge($tags, $extra);
        }
        array_unique($tags);

        if ($out) {
            echo('<h4>Tags</h4><pre>' . print_r($tags, 1) . '</pre>');
            echo('<h4>Object</h4><pre>' . print_r($object, 1) . '</pre>');
        }

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (false === stripos($string, $tag)) {
                    continue;
                }
                $parts   = explode('.', $tag);
                $context = array_shift($parts);
                $params  = false;
                $value   = false;
                if ($out) {
                    echo('<h4>Context</h4><pre>' . print_r($context, 1) . '</pre>');
                    echo('<h4>Parts</h4><pre>' . print_r($parts, 1) . '</pre>');
                }
                while (!empty($parts)) {
                    $part = array_shift($parts);
                    if ($out) {
                        echo('<h4>Part</h4><pre>' . print_r($part, 1) . '</pre>');
                    }
                    // handle the value differently based on it's type
                    if ($value) {
                        // arrays should have the key available
                        if (is_array($value) && array_key_exists($part, $value)) {
                            $value = $value[$part];
                        } // JRegistry uses get() for values
                        else {
                            if (is_object($value) && $value instanceof JRegistry) {
                                $value = $value->get($part);
                            } // normal object
                            else {
                                if (is_object($value) && property_exists($value, $part)) {
                                    $value = $value->{$part};
                                } // object with this method
                                else {
                                    if (is_object($value) && method_exists($value, $part)) {
                                        $value = call_user_func(array($value, $part));
                                    } // don't know what to do here...
                                    else {
                                        $value = '';
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $value = $object->{$part};
                    }
                    if ($out) {
                        echo('<h4>Value</h4><pre>' . print_r($value, 1) . '</pre>');
                    }
                }
                // last checks on value
                if (is_array($value) || is_object($value)) {
                    $value = print_r($value, 1);
                }
                // replace tag
                $string = str_ireplace("{%$tag%}", $value, $string);
            }
        }
        if ($out) {
            echo('<h4>String</h4><pre>' . htmlspecialchars(print_r($string, 1)) . '</pre>');
        }
        return $string;
    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();

        // main query
        $query = $db->getQuery(true)
            // Select the required fields from the table.
            ->select('Email.*')
            ->select('Campaign.name AS campaign_name')
            ->from('#__jinbound_emails AS Email')
            ->leftJoin('#__jinbound_campaigns AS Campaign ON Email.campaign_id = Campaign.id')
            ->group('Email.id')
            ->order('Campaign.name ASC');

        $this->appendAuthorToQuery($query, 'Email');
        $this->filterSearchQuery($query, $this->state->get('filter.search'), 'Email', 'id',
            array('name', 'subject', 'Campaign.name'));
        $this->filterPublished($query, $this->getState('filter.published'), 'Email');

        // Add the list ordering clause.
        $listOrdering = $this->getState('list.ordering', 'Email.sendafter');
        $listDirn     = $db->escape($this->getState('list.direction', 'ASC'));
        $query->order($db->escape($listOrdering) . ' ' . $listDirn);

        return $query;
    }
}
