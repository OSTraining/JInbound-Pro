<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$fieldset = $this->form->getFieldset($this->_currentFieldsetName);

?>
<fieldset class="container-fluid">
    <div class="row-fluid">
        <div class="span8">
            <?php
            $well = false;
            foreach ($fieldset as $field) :
                $label = trim($field->label . '');
                if (empty($label)) :
                    echo $field->input;
                else :
                    $this->_currentField = $field;
                    echo $this->loadTemplate('edit_field');
                endif;
                if (empty($well) && method_exists($field, 'getSidebar')) :
                    $well = $field->getSidebar();
                endif;
            endforeach;
            ?>
        </div>
        <?php if (!empty($well)) : ?>
            <div class="span4 well">
                <?php echo $well; ?>
            </div>
        <?php endif; ?>
    </div>
</fieldset>
