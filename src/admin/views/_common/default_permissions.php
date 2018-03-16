<?php
/**
 * @package    JInbound-Pro
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2018 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of JInbound-Pro.
 *
 * JInbound-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * JInbound-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JInbound-Pro.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

$action    = sprintf('index.php?%s=1', JSession::getFormToken());
$assetName = $this->getName();

if (!empty($this->permissions)) :
    ?>
    <div id="<?php echo $assetName . '-permissions-form'; ?>">
        <form action="<?php echo $action; ?>" method="post">
            <?php echo JHtml::_('jinbound.permissions', $this->permissions->getField('rules')); ?>
            <input type="hidden" name="asset" value="<?php echo $assetName; ?>"/>
        </form>
    </div>
<?php
endif;
