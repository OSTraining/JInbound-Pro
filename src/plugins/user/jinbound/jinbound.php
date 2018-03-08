<?php
/**
 * @package             JInbound
 * @subpackage          plg_user_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php';
if (JFile::exists($helper)) {
    require_once $helper;
}

class plgUserJInbound extends JPlugin
{
    /**
     * Constructor
     *
     * @param unknown_type $subject
     * @param unknown_type $config
     */
    public function __construct(&$subject, $config)
    {
        // if something happens & the helper class can't be found, we don't want a fatal error here
        if (class_exists('JInbound')) {
            JInbound::language('plg_user_jinbound.sys', JPATH_ADMINISTRATOR);
        } else {
            JFactory::getLanguage()->load('plg_user_jinbound.sys', JPATH_ADMINISTRATOR);
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_USER_JINBOUND_COMPONENT_NOT_INSTALLED'));
        }
        parent::__construct($subject, $config);
    }

    /**
     * @param    string $context The context for the data
     * @param    int    $data    The user id
     * @param    object
     *
     * @return    boolean
     */
    function onContentPrepareData($context, $data)
    {
        // Check we are manipulating a valid form.
        if (!in_array($context,
            array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile'))) {
            return true;
        }

        if (is_object($data)) {
            $userId = isset($data->id) ? $data->id : 0;

            if (!isset($data->jinbound) and $userId > 0) {
                // Load the profile data from the database.
                $db = JFactory::getDbo();
                $db->setQuery(
                    'SELECT profile_key, profile_value FROM #__user_profiles' .
                    ' WHERE user_id = ' . (int)$userId . " AND profile_key LIKE 'jinbound.%'" .
                    ' ORDER BY ordering'
                );
                $results = $db->loadRowList();

                // Check for a database error.
                if ($db->getErrorNum()) {
                    $this->_subject->setError($db->getErrorMsg());
                    return false;
                }

                // Merge the profile data.
                $data->jinbound = array();

                foreach ($results as $v) {
                    $k                  = str_replace('jinbound.', '', $v[0]);
                    $data->jinbound[$k] = $v[1];
                }
            }
        }

        return true;
    }

    /**
     * Adds the necessary fields to the user profile
     *
     * @param JForm $form
     *
     * @deprecated form code will be removed/changed in the future
     */
    public function onContentPrepareForm($form)
    {

        return true;

        // make sure form is a JForm
        if (!($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }
        // Check we are manipulating a valid form.
        if (!in_array($form->getName(),
            array('com_admin.profile', 'com_users.user', 'com_users.registration', 'com_users.profile'))) {
            return true;
        }
        // Add the email fields to the form.
        JForm::addFormPath(dirname(__FILE__) . '/forms');
        $form->loadFile('jinbound_lead', false);

        return true;
    }

    /**
     * Saves the user data to the profiles table
     *
     * @param $data
     * @param $isNew
     * @param $result
     * @param $error
     */
    function onUserAfterSave($data, $isNew, $result, $error)
    {
        $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

        if ($userId && $result && isset($data['jinbound']) && (count($data['jinbound']))) {
            try {
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = ' . $userId .
                    " AND profile_key LIKE 'jinbound.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

                $tuples = array();
                $order  = 1;

                foreach ($data['jinbound'] as $k => $v) {
                    $tuples[] = '(' . $userId . ', ' . $db->quote('jinbound.' . $k) . ', ' . $db->quote($v) . ', ' . $order++ . ')';
                }

                $db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

            } catch (JException $e) {
                $this->_subject->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all user profile information for the given user ID
     *
     * Method is called after user data is deleted from the database
     *
     * @param    array   $user    Holds the user data
     * @param    boolean $success True if user was succesfully stored in the database
     * @param    string  $msg     Message
     */
    function onUserAfterDelete($user, $success, $msg)
    {
        if (!$success) {
            return false;
        }

        $userId = JArrayHelper::getValue($user, 'id', 0, 'int');

        if ($userId) {
            try {
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = ' . $userId .
                    " AND profile_key LIKE 'jinbound.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }
            } catch (JException $e) {
                $this->_subject->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }
}
