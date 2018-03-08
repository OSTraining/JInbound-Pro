<?php
/**
 * @package             JInbound
 * @subpackage          mod_jinbound_cta
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

// load required classes
JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/jinbound.php');
JInbound::registerHelper('url');

class JFormFieldModJInboundFormEmbed extends JFormField
{
    public $type = 'ModJInboundFormEmbed';

    protected function getInput()
    {
        $module_id = (int)$this->form->getValue('id');
        $published = (int)$this->form->getValue('published');
        $output    = JText::_('MOD_JINBOUND_FORM_EMBED_SAVE_MODULE_FIRST');
        // we should only need the framework on 2.5
        if (method_exists('JInbound', 'loadJsFramework') && !JInbound::version()->isCompatible('3.0.0')) {
            JInbound::loadJsFramework();
            // fix spacing
            $doc = JFactory::getDocument();
            if (method_exists($doc, 'addStyleDeclaration')) {
                $legacyfix = '#jinbound_component{clear:both;margin-top:20px}';
                $doc->addStyleDeclaration($legacyfix);
            }
        }
        // change the message if module has an ID
        if (!empty($module_id) && 1 === $published) {
            $attrs = array(
                'src'           => JInboundHelperUrl::toFull(JUri::root(false) . 'media/mod_jinbound_form/js/form.js')
            ,
                'data-j-ref'    => 'jinbound'
            ,
                'data-j-module' => $module_id
            );
            if (!JInbound::version()->isCompatible('3.0.0')) {
                $attrs['data-j-option'] = 'com_jinbound';
            }
            $script = '<script async';
            foreach ($attrs as $attr => $value) {
                $script .= ' ' . $attr . '="' . $value . '"';
            }
            $script .= '></script>';
            $output = JText::sprintf('MOD_JINBOUND_FORM_EMBED_SCRIPT', htmlspecialchars($script, ENT_QUOTES, 'UTF-8'));
        }
        return $output;
    }
}
