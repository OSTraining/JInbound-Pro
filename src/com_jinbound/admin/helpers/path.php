<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');

abstract class JInboundHelperPath
{
    /**
     * static method to get the media path
     *
     * @return string
     */
    static public function media($file = '')
    {
        return self::_buildPath(JPATH_ROOT . '/media/jinbound', $file);
    }

    static private function _buildPath($root, $file = '')
    {
        return $root . (empty($file) ? '' : "/$file");
    }

    /**
     * static method to get the site path
     *
     * @return string
     */
    static public function site($file = '')
    {
        return self::_buildPath(JPATH_ROOT . '/components/' . JInbound::COM, $file);
    }

    /**
     * static method to get the helper path
     *
     * @return string
     */
    static public function helper($helper = null)
    {
        static $base;

        if (empty($base)) {
            $base = self::admin('helpers');
        }

        $file = '';
        if (!empty($helper)) {
            jimport('joomla.filesystem.file');
            $file = preg_replace('/[^a-z]/', '', $helper) . '.php';
        }

        return self::_buildPath($base, $file);
    }

    /**
     * static method to get the admin path
     *
     * @return string
     */
    static public function admin($file = '')
    {
        return self::_buildPath(JPATH_ADMINISTRATOR . '/components/' . JInbound::COM, $file);
    }

    /**
     * static method to get the library path
     *
     * @return string
     */
    static public function library($file = '')
    {
        return self::_buildPath(self::admin('libraries'), $file);
    }
}
