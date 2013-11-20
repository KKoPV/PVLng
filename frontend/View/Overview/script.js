<script>
/**
 *
 *
 * @author       Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright    2012 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      $Id: v1.0.0.2-22-g7bc4608 2013-05-05 22:07:15 +0200 Knut Kohl $
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<script>

var oTable;

/**
 *
 */
$(function() {

	oTable = $('#tree').DataTable({
		bPaginate: false,
		bLengthChange: false,
		bFilter: false,
		bSort: false,
		bInfo: false,
		bJQueryUI: true
	});

	$('#tree').treetable({
		initialState: 'expanded',
		indent: 24,
		expandable: true,
		stringExpand: '{{Expand}}',
		stringCollapse: '{{Collapse}}',
		_cache: 'hide',
		onNodeInitialized: function() {
			/* check if the node is marked as collapsed */
			if (lscache.get(this.settings._cache + this.id)) {
				if (this.children.length > 0) this.collapse();
			}
		},
		onNodeCollapse: function() {
			/* mark node as collapsed */
			lscache.set(this.settings._cache + this.id, 1);
		},
		onNodeExpand: function() {
			lscache.remove(this.settings._cache + this.id);
		}
	});

	$('#dialog-addchild').dialog({
		autoOpen: false,
		resizable: false,
		width: 650,
		modal: true,
		buttons: {
			'{{Add}}'	 : function() { $('#form-addchild').submit(); },
			'{{Cancel}}': function() { $(this).dialog('close'); return false; }
		}
	});

	$('#dialog-confirm').dialog({
		autoOpen: false,
		resizable: false,
		width: 480,
		modal: true,
		buttons: {
			'{{Delete}}': function() { $(this).data('form').submit(); },
			'{{Cancel}}': function() { $(this).dialog('close'); return false; }
		}
	});

	$('.guid').click(function() {
		/* select GUID, make ready for copy */
		$(this).select();
	}).mouseup(function(e) {
		e.preventDefault();
	});

	$('.delete-form').submit(function(){
		$('#dialog-confirm').data('form', this).dialog('open');
		return false;
	});

	$('#dialog-move').dialog({
		autoOpen: false,
		resizable: false,
		width: 480,
		modal: true,
		buttons: {
			'{{Ok}}': function() { $('#form-movechild').submit(); },
			'{{Cancel}}': function() { $(this).dialog('close'); return false; }
		}
	});

});

/**
 *
 */
function addChild( node ) {
	$('#parent').attr('value', node);
	$('#dialog-addchild').dialog('open');
	return false;
}

/**
 *
 */
function moveChild( id, action ) {
	var form = $('#form-movechild');
	form.attr('action', '/overview/' + action);
	form.find('input[name="id"]').val(id);
	$('#dialog-move').dialog('open');
	return false;
}

/**
 *
 */
var TreeExpanded = true;

function ToggleTree() {
	if (TreeExpanded) {
		$('#tree').treetable('collapseAll');
		$('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]');
		$('#tiptoggle').html('{{ExpandAll}}')
	} else {
		$('#tree').treetable('expandAll');
		$('#treetoggle').attr('src','/images/ico/toggle.png').attr('alt','[-]');
		$('#tiptoggle').html('{{CollapseAll}}')
	}
	TreeExpanded = !TreeExpanded;
	return false;
}

</script>
