/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

/**
 *
 */
$(function() {

	$('#dataTable').DataTable({
		bPaginate: false,
		bFilter: false,
		bLengthChange: false,
		bInfo: false,
		bJQueryUI: true,
		aoColumns: [
			{ 'asSorting': false },
			null,
			null,
			null,
			{ 'asSorting': false },
			{ 'asSorting': false }
		],
		aaSorting: [[ 1, "asc" ]]
	});

});
