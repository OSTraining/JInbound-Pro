<?php

use Joomla\Registry\Registry;

/**
 * @package             jInbound
 * @subpackage          plg_system_jinboundmailchimp
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
class MCAPI
{
    protected $apiVersion = '3.0';

    /**
     * @var string
     */
    protected $apiHost = 'api.mailchimp.com';

    /**
     * @var int
     */
    protected $timeout = 300;

    /**
     * @var int
     */
    protected $chunkSize = 8192;

    /**
     * @var string
     */
    protected $apikey = null;

    /**
     * @var JHttp
     */
    protected $http = null;

    /**
     * @param string $apikey Your MailChimp apikey
     *
     * @return void
     */
    public function __construct($apikey)
    {
        if (strpos($apikey, '-') !== false) {
            $parts = explode('-', $apikey);
            if ($dataCenter = array_pop($parts)) {
                $this->apikey   = $apikey;
                $this->hostName = $dataCenter . '.api.mailchimp.com';
                $this->http     = new JHttp();
            }
        }
    }

    /**
     * Connect to the server and call the requested methods, parsing the result
     * You should never have to call this function manually
     *
     * @param string $task
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     * @throws Exception
     */
    protected function callServer($endpoint, array $params = null, $method = 'get')
    {
        try {
            if (!$this->apikey) {
                throw new Exception('No API key');
            }

            $url     = sprintf('https://%s/%s/%s', $this->hostName, $this->apiVersion, $endpoint);
            $headers = array(
                'Authorization' => 'Basic ' . base64_encode('jInbound.MCAPI:' . $this->apikey)
            );

            switch (strtolower($method)) {
                case 'get':
                    $query    = $params ? '?' . http_build_query($params) : '';
                    $response = $this->http->get($url . $query, $headers, $this->timeout);
                    break;

                case 'post':
                    $response = $this->http->put($url, $params, $headers, $this->timeout);
                    break;

                default:
                    if (!method_exists($this->http, $method)) {
                        throw new Exception('Invalid method - ' . $method);
                    }
                    break;
            }

            if ($response->code < 300) {
                return json_decode($response->body);

            } elseif ($response->code < 400) {
                $error = new Exception('Mailchimp tried to redirect', $response->code);

            } else {
                $message = json_decode($response->body);
                $error   = new Exception(
                    sprintf(
                        '%s<br/>Mailchimp Responded with (%s) - %s',
                        $endpoint,
                        $message->status,
                        $message->detail
                    ),
                    $message->status
                );
            }

        } catch (Exception $error) {
        } catch (Throwable $throwable) {
            $error = new Exception($throwable->getMessage(), $throwable->getCode());
        }

        if (empty($error) || !$error instanceof Exception) {
            $error = new Exception('Unknown error trying to connect to Mailchimp', 500);
        }

        throw $error;
    }

    /**
     * @return object[]
     * @throws Exception
     */
    public function getLists()
    {
        $response = $this->callServer('lists');

        $lists = array();
        if (!empty($response->lists)) {
            foreach ($response->lists as $list) {
                $lists[$list->id] = $list;
            }
        }

        return $lists;
    }

    /**
     * @param string $listId
     *
     * @return object[]
     * @throws Exception
     */
    public function getCategories($listId)
    {
        $response = $this->callServer("lists/{$listId}/interest-categories");

        $categories = array();
        if (!empty($response->categories)) {
            foreach ($response->categories as $category) {
                $categories[$category->id] = $category;
            }
        }

        return $categories;
    }

    /**
     * @param string $listId
     * @param string $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getGroups($listId, $categoryId)
    {
        $groups   = array();
        $response = $this->callServer("lists/{$listId}/interest-categories/{$categoryId}/interests");

        if (!empty($response->interests)) {
            foreach ($response->interests as $group) {
                $groups[$group->id] = $group;
            }
        }

        return $groups;
    }

    /**
     * @param string $listId
     *
     * @return array
     * @throws Exception
     */
    public function getFields($listId)
    {
        $response = $this->callServer("lists/{$listId}/merge-fields");

        return empty($response->merge_fields) ? array() : $response->merge_fields;
    }

    /**
     * @param string $emailAddress
     *
     * @return object[]
     * @throws Exception
     */
    public function getMemberships($emailAddress)
    {
        $query    = array(
            'query' => $emailAddress
        );
        $response = $this->callServer('search-members', $query);
        if (!empty($response->exact_matches->members)) {
            foreach ($response->exact_matches->members as $member) {
                $lists[$member->list_id] = $member;
            }
        }

        return $lists;
    }
}
