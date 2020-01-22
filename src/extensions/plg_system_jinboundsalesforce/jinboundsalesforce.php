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

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Filesystem\File;

defined('_JEXEC') or die;

class plgSystemJInboundsalesforce extends CMSPlugin
{
    /**
     * @var SforcePartnerClient
     */
    protected $client = null;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @param JEventDispatcher $subject
     * @param array            $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage('plg_system_jinboundsalesforce.sys');

        $this->enabled = is_dir(JPATH_ADMINISTRATOR . '/components/com_jinbound');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function onAfterInitialise()
    {
        if ($this->app->isClient('site') || !$this->enabled) {
            return;
        }

        $option = $this->app->input->getCmd('option');
        if ($option != 'plg_system_jinboundsalesforce') {
            return;
        }

        $view   = $this->app->input->getCmd('view');
        $method = 'execTask' . ucwords($view);
        if (method_exists($this, $method)) {
            $this->$method();
        }

        jexit();
    }

    /**
     * @param Form $form
     *
     * @return bool
     */
    public function onContentPrepareForm($form)
    {
        if (!$this->enabled) {
            return true;
        }

        if (!$form instanceof Form) {
            $this->_subject->setError('JERROR_NOT_A_FORM');

            return false;
        }

        $result = true;
        if ($form->getName() == 'com_jinbound.field') {
            $file = 'jinboundsalesforce';

            Form::addFormPath(__DIR__ . '/form');
            Form::addFieldPath(__DIR__ . '/field');

            $result = $form->loadFile($file, false);
        }

        return $result;
    }

    /**
     * @param string $context
     * @param object $conversion
     * @param bool   $isNew
     *
     * @return void
     */
    public function onContentAfterSave($context, $conversion, $isNew)
    {
        if ($context != 'com_jinbound.conversion') {
            return;
        }

        $fields = $this->getFieldsByPage($conversion->page_id);
        if (empty($fields)) {
            return;
        }

        $objectFields = array();
        $formData     = json_decode($conversion->formdata);
        foreach ($fields as $name => $field) {
            $params = json_decode($field->params);
            if (!(
                is_object($params) && property_exists($params, 'salesforce')
                && is_object($params->salesforce)
                && property_exists($params->salesforce, 'mapped_field')
            )
            ) {
                continue;
            }

            if (!empty($params->salesforce->mapped_field)) {
                $objectFields[$params->salesforce->mapped_field] = $formData->lead->$name;
            }
        }

        if (empty($objectFields)) {
            return;
        }

        $client = $this->getClient();
        if ($client) {
            $object         = new SObject();
            $object->type   = 'Contact';
            $object->fields = $objectFields;
            try {
                // @TODO save response ids?
                $client->create(array($object));

            } catch (Exception $e) {
            }
        }
    }

    /**
     * @param int $pageId
     *
     * @return object[]
     */
    protected function getFieldsByPage($pageId)
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('Field.*')
            ->from('#__jinbound_fields AS Field')
            ->leftJoin('#__jinbound_form_fields AS FormFields ON FormFields.field_id = Field.id')
            ->leftJoin('#__jinbound_pages AS Page ON FormFields.form_id = Page.formid AND Page.id = ' . (int)$pageId)
            ->where('Field.published = 1')
            ->group('Field.id');

        $rows = $db->setQuery($query)->loadObjectList();

        $result = array();
        foreach ($rows as $row) {
            $result[$row->name] = $row;
        }

        return $result;
    }

    /**
     * @return SforcePartnerClient
     */
    public function getClient()
    {
        if ($this->client === null) {
            require_once __DIR__ . '/library/force/SforcePartnerClient.php';

            $this->startClient();
        }

        return $this->client;
    }

    /**
     * @return void
     */
    protected function startClient()
    {
        $session         = Factory::getSession();
        $sessionWsdl     = $session->get('jinboundsalesforce.wsdl', '');
        $sessionLocation = $session->get('jinboundsalesforce.location', '');
        $sessionId       = $session->get('jinboundsalesforce.id', '');

        if (!empty($sessionWsdl) && !empty($sessionLocation) && !empty($sessionId)) {
            try {
                $this->client = new SforcePartnerClient();
                $this->client->createConnection($sessionWsdl);
                $this->client->setEndpoint($sessionLocation);
                $this->client->setSessionHeader($sessionId);

                return;

            } catch (Exception $e) {
                // Kill the session data and continue connecting fresh
                $session->set('jinboundsalesforce.wsdl', null);
                $session->set('jinboundsalesforce.location', null);
                $session->set('jinboundsalesforce.id', null);
            }
        }

        $wsdl     = $this->params->get('wsdl', '');
        $username = $this->params->get('username', '');
        $password = $this->params->get('password', '');
        $token    = $this->params->get('security_token', '');
        $useToken = (int)$this->params->get('use_token', 1);
        $wsdlFile = __DIR__ . '/wsdl/' . $wsdl;

        // confirm that required settings are available
        if (empty($wsdl)
            || !file_exists($wsdlFile)
            || !preg_match('/\.xml$/i', $wsdlFile)
            || empty($username)
            || empty($password)
            || (empty($token) && $useToken)
        ) {
            return;
        }

        try {
            // attempt to connect to salesforce
            $this->client = new SforcePartnerClient();
            $this->client->createConnection($wsdlFile);
            $this->client->login($username, $password . ($useToken ? $token : ''));

            // set data into session for reuse later
            $session->set('jinboundsalesforce.wsdl', $wsdlFile);
            $session->set('jinboundsalesforce.location', $this->client->getLocation());
            $session->set('jinboundsalesforce.id', $this->client->getSessionId());

        } catch (Exception $e) {
            $this->client = null;
        }
    }

    /**
     * @return array
     */
    public function onJInboundSalesforceFields()
    {
        if ($this->enabled) {
            return array();
        }

        $options = array();
        $client  = $this->getClient();

        if ($client && method_exists($client, 'describeSObject')) {
            $contact = $client->describeSObject('Contact');

            if (is_object($contact)
                && property_exists($contact, 'fields')
                && is_array($contact->fields)
                && !empty($contact->fields)
            ) {
                foreach ($contact->fields as $field) {
                    // only show fields that can be created
                    if (!$field->createable || $field->deprecatedAndHidden) {
                        continue;
                    }
                    $options[] = JHtml::_('select.option', $field->name, $field->label);
                }
            }
        }

        return $options;
    }

    /**
     * @param string $file
     *
     * @return void
     * @throws Exception
     */
    protected function execTaskClose($file = null)
    {
        if (is_null($file)) {
            $this->execTaskUpload();

        } else {
            $view = $this->getFieldView();
            $view->setLayout('close');
            $view->field = $this->app->input->getString('field');
            $view->file  = $file;

            echo $view->display();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function execTaskUpload()
    {
        Factory::getSession()->checkToken() or die(Text::_('JINVALID_TOKEN'));

        $file = $this->app->input->files->get('wsdl');
        if (empty($file) || !is_array($file)) {
            $this->errors[] = Text::_('PLG_SYSTEM_JINBOUNDSALESFORCE_NO_UPLOAD_FILE');
            $this->execTaskForm(false);

            return;
        }

        $filename = File::makeSafe($file['name']);
        $ext      = File::getExt($filename);

        $src  = $file['tmp_name'];
        $dest = __DIR__ . '/wsdl/' . $filename;
        if (strtolower($ext) !== 'xml') {
            $this->errors[] = Text::_('PLG_SYSTEM_JINBOUNDSALESFORCE_FILE_MUST_BE_XML');
            $this->execTaskForm(false);

            return;
        }

        if (!File::upload($src, $dest)) {
            $this->errors[] = Text::_('PLG_SYSTEM_JINBOUNDSALESFORCE_COULD_NOT_MOVE_FILE');
            $this->execTaskForm(false);

            return;
        }

        $this->execTaskClose($filename);
    }

    /**
     * @param bool $token
     *
     * @return void
     * @throws Exception
     */
    protected function execTaskForm($token = true)
    {
        if ($token) {
            Factory::getSession()->checkToken('get') or die(Text::_('JINVALID_TOKEN'));
        }

        $view         = $this->getFieldView();
        $view->errors = $this->errors;
        $view->field  = $this->app->input->getString('field');

        echo $view->display();
    }

    /**
     * gets a new instance of the base field view
     *
     * @return JInboundFieldView
     * @throws Exception
     */
    protected function getFieldView()
    {
        $viewConfig = array('template_path' => __DIR__ . '/field/wsdl');
        $view       = new JInboundFieldView($viewConfig);

        return $view;
    }
}
