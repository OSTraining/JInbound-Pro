<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * JInbound Component Category Tree
 *
 */
class JinboundCategories extends JCategories
{
    public function __construct($options = array())
    {
        $options['table']      = '#__jinbound_pages';
        $options['extension']  = 'com_jinbound';
        $options['field']      = 'category';
        $options['statefield'] = 'published';
        parent::__construct($options);
    }
}
