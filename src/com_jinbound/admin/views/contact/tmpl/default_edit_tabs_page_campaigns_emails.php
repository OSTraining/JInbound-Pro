<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

if (!empty($this->item->emails)) :
    ?>
    <div class="row-fluid">
        <div class="span12 well">
            <h4><?php echo JText::_('COM_JINBOUND_EMAIL_HISTORY'); ?></h4>
            <?php
            foreach ($this->item->emails as $email) :
                if ($email->campaign_id && $this->_currentCampaignId != $email->campaign_id) :
                    continue;
                endif;
                ?>
                <div class="row-fluid">
                    <div class="span12">
                        <h5><?php echo $this->escape($email->subject); ?></h5>
                        <h6><?php echo JInbound::userDate($email->sent); ?></h6>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php
endif;
