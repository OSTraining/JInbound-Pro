<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

if (!($this instanceof JInboundRSSView)) die;

if (class_exists('JFeedFactory')) :
	echo $this->loadTemplate('new', 'rss');
else :

if ($this->feed) : ?>
<div class="row-fluid">
	<div class="span12">
<?php
	// channel header and link
	$channel['title'] = $this->feed->get_title();
	$channel['link'] = $this->feed->get_link();
	$channel['description'] = $this->feed->get_description();

	// items
	$items = $this->feed->get_items();

	// feed elements
	$items = array_slice($items, 0, $this->feedLimit);

	// feed title
	if (!is_null( $channel['title'] ) && $this->showTitle) {
	?>
		<div class="row-fluid">
			<h4><a href="<?php echo $this->escape($channel['link']); ?>" target="_blank"><?php
				echo $this->escape($channel['title']);
			?></a></h4>
		</div><?php
	}

	// feed description
	if ($this->showDescription) {
	?>
		<div class="row-fluid">
			<p><?php echo $this->escape($channel['description']); ?></p>
		</div>
	<?php
	}

	$actualItems = count( $items );
	$setItems = $this->feedLimit;

	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}
	?>
		<div class="row-fluid">
			<ul>
	<?php
	for ($j = 0; $j < $totalItems; $j ++)
	{
		$currItem = & $items[$j];
		// item title
		?>
				<li>
		<?php
		if ( !is_null( $currItem->get_link() ) ) {
		?>
					<h5><a href="<?php echo $this->escape($currItem->get_link()); ?>" target="_child"><?php
						echo $this->escape($currItem->get_title());
					?></a></h5>
		<?php
		}

		// item description
		if ($this->showDetails)
		{
			// item description
			$text = html_entity_decode($currItem->get_description());
			$text = str_replace('&apos;', "'", $text);
			$text = strip_tags($text);

			// word limit check
			if ($this->wordLimit) {
				$texts = explode(' ', $text);
				$count = count($texts);
				if ($count > $this->wordLimit) {
					$text = '';
					for ($i = 0; $i < $this->wordLimit; $i ++)
					{
						$text .= ' '.$texts[$i];
					}
					$text .= '...';
				}
			}
			?>
					<p><?php echo $this->escape($text); ?></p>
			<?php
		}
		?>
				</li>
<?php
	}
	?>
			</ul>
		</div>
	</div>
</div>
<?php endif;

endif;
