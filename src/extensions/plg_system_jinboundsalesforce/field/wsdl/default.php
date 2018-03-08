<?php
/**
 * @package             JInbound
 * @subpackage          plg_system_jinboundsalesforce
 **********************************************
 * JInbound
 * Copyright (c) 2013 Anything-Digital.com
 * Copyright (c) 2018 Open Source Training, LLC
 **********************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.n *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 */

defined('_JEXEC') or die;

$root    = JUri::root();
$version = new JVersion();
$legacy  = !$version->isCompatible('3.0.0');

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title><?php echo JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_UPLOAD_WSDL'); ?></title>
    <?php if (!$legacy) : ?>
        <link rel="stylesheet" href="<?php echo $root; ?>/media/jui/css/chosen.css" type="text/css"/>
        <link rel="stylesheet" href="<?php echo $root; ?>/media/system/css/modal.css" type="text/css"/>
        <script src="<?php echo $root; ?>/media/jui/js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/jui/js/jquery-migrate.min.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/system/js/core.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/system/js/punycode.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/system/js/validate.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/jui/js/chosen.jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/jui/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo $root; ?>/media/system/js/html5fallback.js" type="text/javascript"></script>
    <?php endif; ?>
</head>
<body>
<div class="container-fluid">
    <?php if (!empty($this->errors)) : ?>
        <div class="alert alert-error">
            <?php foreach ($this->errors as $error) : ?>
                <div class="row-fluid"><?php echo $this->escape($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="row-fluid">
        <form action="index.php?option=plg_system_jinboundsalesforce&amp;view=upload" class="form-vertical"
              id="wsdlForm" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend><?php echo JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_UPLOAD_WSDL'); ?></legend>
                <div class="control-group">
                    <div class="control-label">
                        <label
                            for="jinboundsalesforcefile"><?php echo JText::_('PLG_SYSTEM_JINBOUNDSALESFORCE_FILE'); ?></label>
                    </div>
                    <div class="controls">
                        <input id="jinboundsalesforcefile" name="wsdl" type="file"/>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="btn-group">
                        <button class="btn btn-primary"><?php echo JText::_('JAPPLY'); ?></button>
                        <button class="btn"><?php echo JText::_('JCANCEL'); ?></button>
                    </div>
                    <input type="hidden" name="field" value="<?php echo $this->escape($this->field); ?>"/>
                    <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
                </div>
            </fieldset>
        </form>
    </div>
</div>
</body>
</html>
