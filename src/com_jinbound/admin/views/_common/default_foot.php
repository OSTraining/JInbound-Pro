<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!isset($this->cols)) {
    $this->cols = 8;
}
?>
<tr>
    <td colspan="<?php echo (int)$this->cols; ?>"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
