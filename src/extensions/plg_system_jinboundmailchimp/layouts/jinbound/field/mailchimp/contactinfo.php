<?php
/**
 * @package    jInbound-Pro
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2018 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
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

use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die();

/**
 * @var FileLayout $this
 * @var array      $displayData
 * @var string     $layoutOutput
 * @var string     $path
 */

$email       = empty($displayData['email']) ? null : $displayData['email'];
$memberships = empty($displayData['memberships']) ? array() : $displayData['memberships'];

if (!$email || !$memberships) :
    $message = $email
        ? JText::sprintf('PLG_SYSTEM_JINBOUNDMAILCHIMP_CONTACT_NO_LISTS', $email)
        : JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_CONTACT_NO_EMAIL');
    ?>
    <div class="alert alert-error">
        <?php echo $message; ?>
    </div>
<?php
else :
    $filter = JFilterInput::getInstance();
    ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php echo JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_LIST'); ?></th>
            <th><?php echo JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_GROUPS'); ?></th>
            <th><?php echo JText::_('JSTATUS'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($memberships as $listId => $membership) :
            $groups = array();
            $interests = empty($membership->interests) ? array() : $membership->interests;
            foreach ($interests as $groupId => $group) {
                $groups[] = sprintf('%s/%s', $group->category->title, $group->name);
            }
            ?>
            <tr>
                <td><h3><?php echo $filter->clean($membership->list->name); ?></h3></td>
                <td>
                    <?php
                    echo $groups
                        ? '<ul><li>' . join('</li><li>', $groups) . '</li></ul>'
                        : JText::_('PLG_SYSTEM_JINBOUNDMAILCHIMP_GROUPS_NO_MEMBERSHIPS');
                    ?>
                </td>
                <td><?php echo $membership->status; ?></td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
<?php
endif;
