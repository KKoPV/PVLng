<!--
/**
 *
 *
 * @author       Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright    2012 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */
-->

<script src="/js/jquery.treetable.js"></script>

<script>

var oTable, TreeExpanded = true;

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
    form.attr('action', '/overview/'+action);
    form.find('input[name="id"]').val(id);
    $('#dialog-move').dialog('open');
    return false;
}

/**
 *
 */
function ToggleTree() {
    if (TreeExpanded) {
        $('#tree').treetable('collapseAll');
        $('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]');
        $('#tiptoggle').html('{{ExpandAll}}');
    } else {
        $('#tree').treetable('expandAll');
        $('#treetoggle').attr('src','/images/ico/toggle.png').attr('alt','[-]');
        $('#tiptoggle').html('{{CollapseAll}}');
    }
    TreeExpanded = !TreeExpanded;
    return false;
}

/**
 *
 */
function rearrangeStripes() {
    $('#tree tbody tr:visible').each(function(id, el) {
        el = $(el);
        if (id & 1) {
            /* Set to odd if needed */
            if (el.hasClass('even')) el.removeClass('even').addClass('odd');
        } else {
            /* Set to even if needed */
            if (el.hasClass('odd'))  el.removeClass('odd').addClass('even');
        }
    });
}

/**
 *
 */
$(function() {

    oTable = $('#tree').DataTable({
        bSort: false,
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' }
    }).disableSelection();

    lscache.setBucket('Overview');

    $('#tree').treetable({
        initialState: 'expanded',
        indent: 24,
        expandable: true,
        stringExpand: '{{Expand}}',
        stringCollapse: '{{Collapse}}',
        hiddenNodes: (lscache.get('HiddenNodes') || []),

        /* Helper functions for local storage handling */
        isCollapsed: function(id) {
            return (this.hiddenNodes.indexOf(id) != -1);
        },
        markCollapsed: function(id, collapsed) {
            if (collapsed) {
                this.hiddenNodes.push(id);
            } else {
                idx = this.hiddenNodes.indexOf(id);
                if (idx != -1) this.hiddenNodes.splice(idx);
            }
            lscache.set('HiddenNodes', this.hiddenNodes);
        },

        onNodeInitialized: function() {
            /* check if the node is marked as collapsed */
            if (this.settings.isCollapsed(this.id)) this.collapse();
        },
        onInitialized: function() {
            rearrangeStripes();
            /* set callbacks here AFTER stripes are initialized */
            this.settings.onNodeCollapse = function() {
                /* mark node as collapsed */
                this.settings.markCollapsed(this.id, true);
                rearrangeStripes();
            };
            this.settings.onNodeExpand = function() {
                this.settings.markCollapsed(this.id, false);
                setTimeout(function() { rearrangeStripes() }, 1);
            };
        }
    });

    $('.droppable').droppable({
        accept: '.draggable',
        hoverClass: 'ui-state-active',
        drop: function( event, ui ) {
            /* Create hidden form and submit */
            ui.draggable.hide();
            var f = $('<form/>', { action: '/overview/dragdrop', method: 'post' } );
            f.append($('<input/>', { type: 'hidden', name: 'target', value: $(this).data('tt-id') }));
            f.append($('<input/>', { type: 'hidden', name: 'id',     value: ui.draggable.data('id') }));
            f.append($('<input/>', { type: 'hidden', name: 'entity', value: ui.draggable.data('entity') }));
            f.appendTo('body').submit();
        }
    });

    $('.draggable').draggable({
        distance: 5,
        opacity: .9,
        revert: true,
        revertDuration: 0,
        stack: 'span.draggable',
        addClasses: false,
        cursorAt: { left: 24, top: 13 },
        refreshPositions: true,
        scroll: true,
        start: function() {
            var p = $(this).parent().parent();
            if (p.hasClass('droppable')) p.droppable('disable');
            $(this).addClass('ui-state-hover');
        },
        stop: function() {
            $(this).removeClass('ui-state-hover');
            var p = $(this).parent().parent();
            if (p.hasClass('droppable')) p.droppable('enable');
        }
    });

    $('#add-child').change(function() {
        var el = $(this).find('option:selected');
        if (el && el.val()) {
            $('#drag-new').data('entity', el.val());
            $('#drag-text').text(el.text());
            $('#drag-new-wrapper').show();
        } else {
            $('#drag-new-wrapper').hide();
        }
    });

    $('#dialog-addchild').dialog({
        autoOpen: false,
        resizable: false,
        width: 650,
        modal: true,
        buttons: {
            '{{Add}}': function() { $('#form-addchild').submit() },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
        }
    });

    $('#dialog-confirm').dialog({
        autoOpen: false,
        resizable: false,
        width: 480,
        modal: true,
        buttons: {
            '{{Delete}}': function() { $(this).data('form').submit() },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
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
            '{{Ok}}': function() { $('#form-movechild').submit() },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
        }
    });

});

</script>
