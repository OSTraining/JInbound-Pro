<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldJinboundUsers extends JFormFieldList
{
    protected $type = 'JinboundUsers';

    protected function getOptions()
    {
        $db = JFactory::getDbo();

        try {
            $options = $db->setQuery($db->getQuery(true)
                ->select('id AS value, username AS text')
                ->from('#__users')
                ->where('block = 0')
                ->order('username DESC')
            )->loadObjectList();
        } catch (Exception $e) {
            $options = array();
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
