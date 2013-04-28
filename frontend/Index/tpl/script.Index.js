/**
 *
 *
 * @author		 Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright	2012 Knut Kohl
 * @license		GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version		$Id$
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<script>

/**
 *
 */
$(function() {

	$('#tree').DataTable({
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
			'{{Cancel}}': function() { $(this).dialog('close'); }
		}
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

	$( "#dialog-guid" ).dialog({
		autoOpen: false,
		resizable: false,
		width: 480,
		modal: true,
		buttons: {
			Ok: function() { $(this).dialog('close'); }
		}
	});

	$('.delete-form').submit(function(){
		currentForm = this;
		$('#dialog-confirm').data('form', this).dialog('open');
		return false;
	});

});

/**
 *
 */
function addChild( node ) {
	$('#parent').attr('value', node);
	$('#dialog-addchild').dialog('open');
}

/**
 *
 */
function showGUID( guid ) {
	$('#show-guid').html(guid);
	$('#dialog-guid').dialog('open');
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
