<?php
/**
 * @package   jInbound-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright Nikola Biskup salamander-studios.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
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

defined('_JEXEC') or die;// no direct access

// legacy - determine protocol
// TODO is this necessary? won't // suffice?
// why not use core JUri ??!!!
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $htp = "https";
} else {
    $htp = "http";
}
// TODO clean this up
$url     = $htp . "://" . $_SERVER['HTTP_HOST'] . getenv('REQUEST_URI');
$rss_url = $htp . "://" . $_SERVER['HTTP_HOST'];

// size of the icons
$isize     = (int)$params->get('isize', 32);
$isizename = (24 == $isize || 32 == $isize) ? 32 : 64;
// TODO replace with svg?
$iset         = 'aqu';//$params->get('iset', 'aqu');
$iposition    = $params->get('iposition');
$tweetbtn     = $params->get('tweetbtn');
$tweetbtnsize = $params->get('tweetbtnsize');
$tweetflwsize = $params->get('tweetflwsize');
if ($params->get('s25') == 0) {
    $tweetfollowcount = "false";
} else {
    $tweetfollowcount = "true";
}
if ($params->get('tweetname') != "") {
    $tweetname = $params->get('tweetname');
} else {
    $tweetname = "twitterapi";
}
$linkedcount     = $params->get('linkedcount');
$linkedurl       = $params->get('linkedurl');
$document        = JFactory::getDocument();
$title           = $document->getTitle();
$opac            = $params->get('opac');
$porient         = $params->get('porient');
$piname          = $params->get('piname');
$imagetobepinned = $params->get('imagetobepinned');
if ($opac == "yes") {
    $document->addStyleSheet('modules/mod_jinbound_social_bookmark/css/nsb-opac.css');
} elseif ($opac == "invert") {
    $document->addStyleSheet('modules/mod_jinbound_social_bookmark/css/nsb-opac-inv.css');
} else {
    $document->addStyleSheet('modules/mod_jinbound_social_bookmark/css/nsb.css');
}
$twlink  = $params->get('twlink');
$fblink  = $params->get('fblink');
$mslink  = $params->get('mslink');
$lilink  = $params->get('lilink');
$rsslink = $params->get('rsslink');

if ($params->get("plusoneurl") != "") {
    $plusoneurl = $params->get("plusoneurl");
} else {
    $plusoneurl = $url;
}
$size        = $params->get("size");
$lang        = $params->get("Locale");
$googlecount = $params->get("googlecount");

$customlink1 = $params->get("customlink1");
$customicon1 = $params->get("customicon1");
$customalt1  = $params->get("customalt1");

$customlink2 = $params->get("customlink2");
$customicon2 = $params->get("customicon2");
$customalt2  = $params->get("customalt2");

$customlink3 = $params->get("customlink3");
$customicon3 = $params->get("customicon3");
$customalt3  = $params->get("customalt3");

$customlink4 = $params->get("customlink4");
$customicon4 = $params->get("customicon4");
$customalt4  = $params->get("customalt4");

$padding = $params->get("padding");

$doc   = JFactory::getDocument();
$css   = array();
$css[] = '.nsb_container a{';
$css[] = "\tpadding:" . $params->get('padding') . 'px; float:' . $params->get('iposition') . '; display:inline-block;';
$css[] = '}#plusone{padding:' . $params->get('padding') . 'px !important;}';
$doc->addStyleDeclaration(implode("\n", $css));


echo '<div class="nsb_container">';
$tt = $params->get('s1', '1');
if ($tt == "1") {
    if ($fblink == "") {
        echo '<a id="l1" target="_blank" rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $url . '&amp;title=' . $title . '"><img title="Facebook" src="modules/mod_jinbound_social_bookmark/icons/facebook_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Facebook" /></a>';
    } else {
        echo '<a id="l1" target="_blank" rel="nofollow" href="http://' . $fblink . '"><img title="Facebook" src="modules/mod_jinbound_social_bookmark/icons/facebook_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Facebook" /></a>';
    }
}
$tt = $params->get('s3', '1');
if ($tt == "1") {
    if ($twlink == "") {
        echo '<a id="l3" target="_blank" rel="nofollow" href="http://twitter.com/home?status=' . $url . '&amp;title=' . $title . '"><img title="Twitter" src="modules/mod_jinbound_social_bookmark/icons/twitter_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Twitter" /></a>';
    } else {
        echo '<a id="l3" target="_blank" rel="nofollow" href="http://' . $twlink . '"><img title="Twitter" src="modules/mod_jinbound_social_bookmark/icons/twitter_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Twitter" /></a>';
    }
}

$tt = $params->get('s7', '1');
if ($tt == "1") {
    echo '<a id="l7" target="_blank" rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $url . '&amp;title=' . $title . '"><img title="Google Bookmarks" src="modules/mod_jinbound_social_bookmark/icons/google_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Google Bookmarks" /></a>';
}
$tt = $params->get('s110', '1');
if ($tt == "1") {
    if ($lilink == "") {
        echo '<a id="l11" target="_blank" rel="nofollow" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $url . '&amp;summary=%5B..%5D&amp;source="><img title="linkedin" src="modules/mod_jinbound_social_bookmark/icons/linkedin_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="Linkedin" /></a>';
    } else {
        echo '<a id="ll1" target="_blank" rel="nofollow" href="http://' . $lilink . '"><img title="LinkedIn" src="modules/mod_jinbound_social_bookmark/icons/linkedin_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="LinkedIn" /></a>';
    }
}
$tt = $params->get('s14', '1');
if ($tt == "1") {
    if ($rsslink == "") {
        echo '<a id="l13" target="_blank" rel="nofollow" href="' . $rss_url . '/index.php?format=feed&amp;type=rss&amp;title=' . $title . '"><img title="RSS Feed" src="modules/mod_jinbound_social_bookmark/icons/rss_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="RSS Feed" /></a>';
    } else {
        echo '<a id="l14" target="_blank" rel="nofollow" href="' . $rsslink . '"><img title="RSS Feed" src="modules/mod_jinbound_social_bookmark/icons/rss_' . $iset . '_' . $isizename . '.png" width="' . $isize . '" height="' . $isize . '" alt="RSS Feed" /></a>';
    }
}
$tt = $params->get('s16', '0');
if ($tt == "1") {
    echo '<a id="l16" target="_blank" rel="nofollow" href="' . $customlink1 . '"><img title="" src="' . $customicon1 . '" alt="' . $customalt1 . '" /></a>';
}
$tt = $params->get('s17', '0');
if ($tt == "1") {
    echo '<a id="l17" target="_blank" rel="nofollow" href="' . $customlink2 . '"><img title="" src="' . $customicon2 . '" alt="' . $customalt2 . '" /></a>';
}
$tt = $params->get('s18', '0');
if ($tt == "1") {
    echo '<a id="l18" target="_blank" rel="nofollow" href="' . $customlink3 . '"><img title="" src="' . $customicon3 . '" alt="' . $customalt3 . '" /></a>';
}
$tt = $params->get('s19', '0');
if ($tt == "1") {
    echo '<a id="l19" target="_blank" rel="nofollow" href="' . $customlink4 . '"><img title="" src="' . $customicon4 . '" alt="' . $customalt4 . '" /></a>';
}
$tt = $params->get('s15', '1');
if ($tt == "1") {
    ?>
    <div id="plusone"></div>
    <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
    <script type="text/javascript">
        gapi.plusone.render("plusone",
            {"size": "<?php echo htmlspecialchars($size); ?>",
                "lang": "<?php echo $lang?>",
                "parsetags": "explicit",
                "annotation": "<?php echo $googlecount;?>",
                "href": "<?php echo htmlspecialchars($plusoneurl);?>"
            });
    </script>
    <?php
}
$tt = $params->get('s23', '1');
if ($tt == "1") {
    ?>
    <a href="https://twitter.com/share" class="twitter-share-button" data-count="<?php echo $tweetbtn; ?>"
       data-size="<?php echo $tweetbtnsize; ?>" data-lang="en">Tweet</a>
    <script>!function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, "script", "twitter-wjs");</script>
    <?php
}
$tt = $params->get('s24', '1');
if ($tt == "1") {
    ?>
    <a href="https://twitter.com/<?php echo $tweetname; ?>" class="twitter-follow-button"
       data-show-count="<?php echo $tweetfollowcount; ?>" data-size="<?php echo $tweetflwsize; ?>" data-lang="en">Follow
        @twitterapi</a>
    <script>!function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = "//platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, "script", "twitter-wjs");</script>
    <?php
}
$tt = $params->get('s26', '1');
if ($tt == "1") {
    ?>
    <script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
    <script type="IN/Share" data-url="<?php echo $linkedurl; ?>" data-counter="<?php echo $linkedcount; ?>"></script>
    <?php
}
$tt = $params->get('s27', '1');
if ($tt == "1") {
    ?>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=285414126032";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    <div class="fb-like" data-href="<?php echo $url; ?>" data-width="<?php echo $params->get('facebookwidth'); ?>"
         data-show-faces="<?php echo $params->get('facebookfaces'); ?>"
         data-send="<?php echo $params->get('facebooksend'); ?>"
         data-colorscheme="<?php echo $params->get('facebookcolorscheme'); ?>"></div>
    <?php
}
echo '</div><div style="clear:both;"></div>';
?>
