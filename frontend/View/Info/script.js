<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

$(function() {

	$.ajaxSetup({
		beforeSend: function setHeader(xhr) {
			xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
		}
	});

	$('.last-reading').each(function(id, el){
		$.getJSON(
			PVLngAPI + 'data/' + $(el).data('guid') + '.json',
			{
				attributes: true, /* need decimals for formating */
				period:     'readlast'
			},
			function(data) {
				var attr = data.shift();
				/* Test for numeric data */
				if (data[0].data == +data[0].data) {
					$(el).number(data[0].data, attr.decimals, DecimalSeparator, ThousandSeparator);
				} else {
					$(el).html(data[0].data);
				}
			}
		);
	});

	$('#table-info').DataTable({
		bJQueryUI: true,
		bPaginate: false,
		bLengthChange: false,
		bFilter: false,
		bSort: false,
		bInfo: false
	});

	$('#regenerate').click(function() {
		/* Replace text, make bold red and unbind this click handler */
	    $(this).val("{{Sure}}?").css('fontWeight','bold').css('color','red').unbind();
	    return false;
	});

	$('#table-stats').DataTable({
		bJQueryUI: true,
		bPaginate: false,
		bLengthChange: false,
		bFilter: false,
		bInfo: false,
		aaSorting: [[0, 'asc']],
		aoColumnDefs: [
			{ sType: 'numeric-' + (('{LANGUAGE}' == 'de') ? 'comma' : 'dot'), aTargets: [4] }
		]
	});

});
</script>
