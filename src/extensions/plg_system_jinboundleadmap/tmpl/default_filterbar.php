<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2015 Anything-Digital.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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

defined('JPATH_PLATFORM') or die;

?>
<form action="<?php echo $this->data->url; ?>" method="post" name="adminForm" id="adminForm">
    <?php
    if (!empty($this->filterForm)) :
        if (class_exists('JLayoutHelper')) :
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this), null,
                array('debug' => false));
        else:
            echo $this->loadTemplate('filters');
        endif;
    endif;
    ?>
</form>
