<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JText::script('COM_JINBOUND_NAME');
JText::script('COM_JINBOUND_DATE');
JText::script('COM_JINBOUND_FORM_CONVERTED_ON');
JText::script('COM_JINBOUND_LANDING_PAGE_NAME');
JText::script('COM_JINBOUND_LEADS');
JText::script('COM_JINBOUND_SUBMISSIONS');
JText::script('COM_JINBOUND_VISITS');
JText::script('COM_JINBOUND_CONVERSIONS');
JText::script('COM_JINBOUND_CONVERSION_RATE');

?>
<script type="text/javascript">
(function($){
	window.jinbound_leads_baseurl = '<?php echo JRoute::_('index.php?option=com_jinbound&view=contacts&format=json&filter_order=latest&filter_order_Dir=desc', false); ?>';
	window.jinbound_leads_limit = 10;
	window.jinbound_leads_start = 0;
	window.jinbound_pages_baseurl = '<?php echo JRoute::_('index.php?option=com_jinbound&view=pages&format=json&filter_order=Page.hits&filter_order_Dir=desc', false); ?>';
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
			filter += '&filter_start=' + arguments[2];
		}
		if (arguments.length > 3) {
			filter += '&filter_end=' + arguments[3];
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
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_LANDING_PAGE_NAME')));
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
						tr.append($('<td></td>').append($('<a href="' + data.items[i].url + '"></a>').text(data.items[i].full_name)));
					}
					tr.append($('<td></td>').text(data.items[i].latest));
					if (null === data.items[i].latest_conversion_page_formname) {
						tr.append($('<td></td>').text(' '));
					}
					else {
						tr.append($('<td></td>').append($('<a href="' + data.items[i].page_url + '"></a>').text(data.items[i].latest_conversion_page_formname)));
					}
					if (null === data.items[i].latest_conversion_page_name) {
						tr.append($('<td></td>').text(' '));
					}
					else {
						tr.append($('<td></td>').append($('<a href="' + data.items[i].page_url + '"></a>').text(data.items[i].latest_conversion_page_name)));
					}
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
			filter += '&filter_start=' + arguments[2];
		}
		if (arguments.length > 3) {
			filter += '&filter_end=' + arguments[3];
		}
		$.ajax(window.jinbound_pages_baseurl + '&limit=' + limit + '&limitstart=' + start + filter, {
			dataType: 'json'
		,	success: function(data, textStatus, jqXHR) {
				$('#reports_top_pages').empty();
				var i = 0, n = data.items.length, t = $('<table class="table table-striped"></table>'), h = $('<thead></thead>'), hr = $('<tr></tr>'), b = $('<tbody></tbody>');
				h.append(hr);
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_LANDING_PAGE_NAME')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_VISITS')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_SUBMISSIONS')));
				hr.append($('<td></td>').text(Joomla.JText._('COM_JINBOUND_LEADS')));
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
					tr.append($('<td></td>').text(parseInt(data.items[i].submissions, 10)));
					tr.append($('<td></td>').text(parseInt(data.items[i].contact_submissions, 10)));
					tr.append($('<td></td>').text(parseInt(data.items[i].conversions, 10)));
					tr.append($('<td></td>').text(data.items[i].conversion_rate + ' %'));
					b.append(tr);
				}
				t.append(b);
				t.append(makeButtons(window.fetchPages, data.pagination, 4));
				$('#reports_top_pages').append(t);
			}
		});
	};
	var start = $('#filter_begin'), end = $('#filter_end'), start_date = '', end_date = '';
	if (start.length && end.length) {
		start_date = start.val();
		end_date = end.val();
	}
	window.fetchLeads(0, 10, start_date, end_date);
	window.fetchPages(0, 10, start_date, end_date);
})(jQuery);
</script>
