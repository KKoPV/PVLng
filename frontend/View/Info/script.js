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
			xhr.setRequestHeader('X-PVLng-Key', '{APIKEY}');
		}
	});

	$('.last-reading').each(function(id, el){
		$.getJSON(
			PVLngAPI + 'data/' + $(el).data('guid') + '.json',
			{
				start:      0,
				attributes: true,
				period:     'last',
				_ts:        (new Date).getTime()
			},
			function(data) {
				var attr = data.shift(), value;
				try {
					/* Don't make checks, just try :-) */
					value = data[0].data.toFixed(attr.decimals);
				} catch (e) {
					value = data[0].data;
				}
				$(el).html(value);
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
