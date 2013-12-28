<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<script>

/**
 *
 */
$(function() {

	$.ajaxSetup({
		beforeSend: function setHeader(xhr) {
			xhr.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
		}
	});

	$('.last-reading').each(function(id, el){
		var guid = $(el).data('guid');

		if (guid) {
			$.getJSON(
				PVLngAPI + 'data/' + guid + '.json',
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
		}
	});

	$('#entities').DataTable({
		bSort: true,
		bLengthChange: false,
		bFilter: false,
		bInfo: false,
		bPaginate: false,
		bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
		aoColumns: [
			{ 'asSorting': false },
			null,
			null,
			null,
			null,
			{ 'asSorting': false },
			null,
			null,
			{ 'asSorting': false }
		],
		aaSorting: [[ 1, 'asc' ]]
	});

	$("#dialog-confirm").dialog({
		autoOpen: false,
		resizable: false,
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

	shortcut.add('Alt+N', function() { window.location = '/channel/add'; });

});

</script>
