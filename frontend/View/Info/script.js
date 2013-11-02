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
