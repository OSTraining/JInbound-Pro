<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundContacts extends JFormFieldList
{
    protected $type = 'JinboundContacts';

    protected function getOptions()
    {
        $db = JFactory::getDbo();

        try {
            $options = $db->setQuery($db->getQuery(true)
                ->select('id AS value, name AS text')
                ->from('#__contact_details')
                ->where('published = 1')
                ->order('name DESC')
            )->loadObjectList();
        } catch (Exception $e) {
            $options = array();
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
