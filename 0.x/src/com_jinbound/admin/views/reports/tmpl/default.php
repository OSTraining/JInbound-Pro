<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JHtml::_('behavior.calendar');

?>
<div class="container-fluid" id="jinbound_component">
	<div class="row-fluid">
		<div class="span12 text-center well">
		Random Advice Text
		</div>
	</div>
	<form action="<?php echo JInboundHelperUrl::_(); ?>" method="post" id="adminForm" name="adminForm" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span3 offset3">
			<?php echo JHtml::_('calendar', $this->state->get('filter_begin'), 'filter_begin', 'filter_begin', '%Y-%m-%d', array('size' => 10, 'onchange' => "window.fetchLeads(window.jinbound_leads_start, window.jinbound_leads_limit, jQuery('#filter_begin').val(), jQuery('#filter_end').val());window.fetchPages(window.jinbound_pages_start, window.jinbound_pages_limit, jQuery('#filter_begin').val(), jQuery('#filter_end').val())")); ?>
		</div>
		<div class="span3">
			<?php echo JHtml::_('calendar', $this->state->get('filter_end'), 'filter_end', 'filter_end', '%Y-%m-%d', array('size' => 10, 'onchange' => "window.fetchLeads(window.jinbound_leads_start, window.jinbound_leads_limit, jQuery('#filter_begin').val(), jQuery('#filter_end').val());window.fetchPages(window.jinbound_pages_start, window.jinbound_pages_limit, jQuery('#filter_begin').val(), jQuery('#filter_end').val())")); ?>
		</div>
	</div>
	<?php echo $this->loadTemplate('dashboard'); ?>
	<div>
		<input name="task" value="" type="hidden" />
	</div>
	</form>
</div>
<?php

JText::script('COM_JINBOUND_NAME');
JText::script('COM_JINBOUND_DATE');
JText::script('COM_JINBOUND_FORM_CONVERTED_ON');
JText::script('COM_JINBOUND_LANDING_PAGE_NAME');
JText::script('COM_JINBOUND_VISITS');
JText::script('COM_JINBOUND_CONVERSIONS');
JText::script('COM_JINBOUND_CONVERSION_RATE');

?>
<script type="text/javascript">
(function($){
	window.jinbound_leads_baseurl = '<?php echo JRoute::_('index.php?option=com_jinbound&view=leads&format=json', false); ?>';
	window.jinbound_leads_limit = 10;
	window.jinbound_leads_start = 0;
	window.jinbound_pages_baseurl = '<?php echo JRoute::_('index.php?option=com_jinbound&view=pages&format=json', false); ?>';
	window.jinbound_pages_limit = 10;
	window.jinbound_pages_start = 0;
	var makeButtons = function(callback, pagination, cols) {
		var f = $('<tfoot></tfoot>'), fr = $('<tr colspan="' + cols + '"></tr>'), fc = $('<td class="btn-group"></td>');
		if (pagination.pagesTotal > 1) {
			var prev = $('<button class="btn"><i class="icon-arrow-left"> </i></button>'), next = $('<button class="btn"><i class="icon-arrow-right"> </i></button>'), page;
			if (0 != pagination.limitstart) {
				prev.click(function(e){
					callback(Math.max(0, pagination.limitstart - pagination.limit), pagination.limit);
					e.preventDefault();
					return false;
				});
			}
			else {
				prev.attr('disabled', 'disabled');
			}
			if (pagination.total > pagination.limit + pagination.limitstart) {
				next.click(function(e){
					callback(Math.min(pagination.total, pagination.limitstart + pagination.limit), pagination.limit);
					e.preventDefault();
					return false;
				});
			}
			else {
				next.attr('disabled', 'disabled');
			}
			fc.append(prev);
			for (i = 0, n = pagination.pagesTotal; n > i; i++) {
				page = $('<button class="btn"></button>').text(1 + i);
				if (1 + i == pagination.pagesCurrent) {
					page.addClass('btn-primary');
				}
				page.click(function(e){
					var num = parseInt($(this).text(), 10);
					callback(Math.max(0, (num * pagination.limit) - pagination.limit), pagination.limit);
					e.preventDefault();
					return false;
				});
				fc.append(page);
			}
			fc.append(next);
			fr.append(fc);
			f.append(fr);

			return f;
		}
	};
	window.fetchLeads = function(start, limit) {
		window.jinbound_leads_limit = limit;
		window.jinbound_leads_start = start;
		var filter = '';
		if (arguments.length > 2) {
			if (arguments[2]) {
				filter += '&filter_start=' + arguments[2];
			}
			if (arguments[3]) {
				filter += '&filter_end=' + arguments[3];
			}
		}
		$.ajax(window.jinbound_leads_baseurl + '&limit=' + limit + '&limitstart=' + start + filter, {
			dataType: 'json'
		,	success: function(data, textStatus, jqXHR) {
				$('#reports_recent_leads').empty();
				var i = 0, n = data.items.length, t = $('<table class="table table-striped"></table>'), h = $('<thead></thead>'), hr = $('<tr></tr>'), b = $('<tbody></tbody>');
				h.append(hr);
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_NAME')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_DATE')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_FORM_CONVERTED_ON')));
				t.append(h);
				if (!n) {
					return;
				}
				for (; n > i; i++) {
					var tr = $('<tr></tr>');
					if (null === data.items[i].name) {
						tr.append($('<td></td>').text(' '));
					}
					else {
						tr.append($('<td></td>').append($('<a href="' + data.items[i].url + '"></a>').text(data.items[i].name)));
					}
					tr.append($('<td></td>').text(data.items[i].created));
					tr.append($('<td></td>').append($('<a href="' + data.items[i].page_url + '"></a>').text(data.items[i].formname)));
					b.append(tr);
				}
				t.append(b);
				t.append(makeButtons(window.fetchLeads, data.pagination, 3));
				$('#reports_recent_leads').append(t);
			}
		});
	};
	window.fetchPages = function(start, limit) {
		window.jinbound_pages_limit = limit;
		window.jinbound_pages_start = start;
		var filter = '';
		if (arguments.length > 2) {
			if (arguments[2]) {
				filter += '&filter_start=' + arguments[2];
			}
			if (arguments[3]) {
				filter += '&filter_end=' + arguments[3];
			}
		}
		$.ajax(window.jinbound_pages_baseurl + '&limit=' + limit + '&limitstart=' + start + filter, {
			dataType: 'json'
		,	success: function(data, textStatus, jqXHR) {
				$('#reports_top_pages').empty();
				var i = 0, n = data.items.length, t = $('<table class="table table-striped"></table>'), h = $('<thead></thead>'), hr = $('<tr></tr>'), b = $('<tbody></tbody>');
				h.append(hr);
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_LANDING_PAGE_NAME')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_VISITS')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_CONVERSIONS')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_CONVERSION_RATE')));
				t.append(h);
				if (!n) {
					return;
				}
				for (; n > i; i++) {
					var tr = $('<tr></tr>');
					if (null === data.items[i].name) {
						tr.append($('<td></td>').text(' '));
					}
					else {
						tr.append($('<td></td>').append($('<a href="' + data.items[i].url + '"></a>').text(data.items[i].name)));
					}
					tr.append($('<td></td>').text(parseInt(data.items[i].hits, 10)));
					tr.append($('<td></td>').text(parseInt(data.items[i].conversions, 10)));
					tr.append($('<td></td>').text(parseInt(data.items[i].conversion_rate, 10)));
					b.append(tr);
				}
				t.append(b);
				t.append(makeButtons(window.fetchPages, data.pagination, 4));
				$('#reports_top_pages').append(t);
			}
		});
	};
	window.fetchLeads(0, 10);
	window.fetchPages(0, 10);
})(jQuery);
</script>
