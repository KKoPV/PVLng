/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
</script>

<script src="/js/chosen.jquery.js"></script>
<script src="/js/jquery.treetable.js"></script>

<script>

/**
 *
 */
$(function() {

	$('#entities').DataTable({
		bFilter: false,
		bInfo: false,
		bPaginate: false,
		bLengthChange: false,
		bJQueryUI: true,
		'aoColumns': [
			{ 'asSorting': '' },
			null,
			null,
			null,
			null,
			{ 'asSorting': false },
			{ 'asSorting': false },
			{ 'asSorting': false }
		],
	});

	$("#dialog-confirm").dialog({
		autoOpen: false,
		resizable: false,
		height: 200,
		width: 480,
		modal: true,
		buttons: {
			'{{Delete}}': function() { $(this).data('form').submit(); },
			'{{Cancel}}': function() { $(this).dialog('close'); }
		}
	});

	$('.delete-form').submit(function(){
			currentForm = this;
			$('#dialog-confirm').data('form', this).dialog('open');
			return false;
	});

});
