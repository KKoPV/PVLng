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

var oTable;

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
var ForceFullDraw = false;

var cancelDragging, overlay;

/**
 *
 */
$(function() {

    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            return (ForceFullDraw || !$(oTable.fnGetNodes()[iDataIndex]).hasClass('hidden'));
        }
    );

    $.fn.dataTableExt.oApi.fnProcessingIndicator = function( oSettings, onoff ) {
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };

    lscache.setBucket('Overview');

    oTable = $('#tree').DataTable({
        bSort: false,
        bInfo: false,
        bLengthChange: false,
        bPaginate: false,
        bProcessing: true,
        bJQueryUI: true,
        bAutoWidth: false,
        sDom: ' <"H"r>t<"F">',
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumnDefs: [
            { sWidth: "90%", aTargets: [ 0 ] },
            { sWidth: "1%", aTargets: [ 1, 2, 3 ] },
            { sWidth: "30em", aTargets: [ 4 ] }
        ],
        fnInitComplete: function() {

            /* Init treetable AFTER databale is ready */
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
                    oTable.fnDraw();
                    /* set callbacks here AFTER stripes are initialized */
                    this.settings.onNodeCollapse = function(node) {
                        oTable.fnDraw();
                        /* mark node as collapsed */
                        this.settings.markCollapsed(this.id, true);
                    };
                    this.settings.onNodeExpand = function(node) {
                        ForceFullDraw = true;
                        oTable.fnDraw();
                    };
                    this.settings.onNodeExpanded = function(node) {
                        ForceFullDraw = false;
                        oTable.fnDraw();
                        this.settings.markCollapsed(this.id, false);
                    };
                }
            });
        }
    });

    $('#treetoggle').click(function(event) {
        event.preventDefault();

        oTable.fnProcessingIndicator(true);
        $(document.body).addClass('wait');

        /* Forces show of processing indicator ... */
        setTimeout(function() {
            var toggler = $('#treetoggle');
            if (toggler.data('expanded') == 1) {
                $('#tree').treetable('collapseAll');
                $('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]');
                $('#tiptoggle').html('{{ExpandAll}}');
                toggler.data('expanded', 0);
            } else {
                $('#tree').treetable('expandAll');
                $('#treetoggle').attr('src','/images/ico/toggle.png').attr('alt','[-]');
                $('#tiptoggle').html('{{CollapseAll}}');
                toggler.data('expanded', 1);
            }
            $(document.body).removeClass('wait');
            oTable.fnProcessingIndicator(false);
        }, 1);
    });

    $('.droppable').droppable({
        accept: '.draggable',
        hoverClass: 'ui-state-active',
        drop: function(event, ui) {
            if (cancelDragging) return;
            ui.helper.hide();
            overlay.show();
            /* Create hidden form and submit */
            $('<form/>', { action: '/overview/dragdrop', method: 'post' } )
            .appendTo('body')
            .append($('<input/>', { type: 'hidden', name: 'target', value: $(this).data('tt-id') }))
            .append($('<input/>', { type: 'hidden', name: 'id',     value: ui.helper.data('id') }))
            .append($('<input/>', { type: 'hidden', name: 'entity', value: ui.helper.data('entity') }))
            .append($('<input/>', { type: 'hidden', name: 'copy',   value: +(ui.helper.css('cursor') == 'copy') }))
            .submit();
        }
    });

    $('#drag-new, .draggable').mousedown(function(event) {
        /* Can dragged object be copied? */
        var copy = $(this).parent().parent().hasClass('channel');
        if (!copy && event.ctrlKey) {
            /* Cancel dragging of groups */
            $.pnotify({ type: 'error', delay: 20000, text: "{{CantCopyGroups}}" });
            cancelDragging = true;
            return;
        }
        var cursor = (copy && event.ctrlKey) ? 'copy' : 'alias';
        $(this).draggable('option', { helper : copy ? 'clone' : 'original' }).css({ cursor: cursor });
        $(this).draggable('option', { helper : 'clone' }).css({ cursor: cursor });
    }).draggable({
        distance: 5,
        addClasses: false,
        stack: 'span.draggable',
        cursorAt: { left: 24, top: 13 },
        revert: 'invalid',
        revertDuration: 0,
        start: function(event, ui) {
            cancelDragging = false;
            $('.draggable').css({ cursor: ui.helper.css('cursor') });
            if ($(this).prop('id') != 'drag-new') $(this).parent().parent().droppable('disable');
            ui.helper.addClass('ui-state-hover');
        },
        drag: function(event, ui) {
            if (cancelDragging) return false;
        },
        stop: function() {
            $('.draggable').css({ cursor: 'default' });
            $(this).removeClass('ui-state-hover');
            if ($(this).prop('id') != 'drag-new') $(this).parent().parent().droppable('enable');
        }
    });

    $('#add-child').change(function() {
        var el = $(this).find('option:selected');
        if (el && el.val()) {
            $('#drag-new').data('entity', el.val()).addClass('draggable');
            $('#drag-text').text(el.text().replace(/.*: */, ''));
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
            '{{Add}}': function() {
                $(this).dialog('close');
                overlay.show();
                $(this).find('form').submit()
            },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
        }
    });

    $('#dialog-confirm').dialog({
        autoOpen: false,
        resizable: false,
        width: 480,
        modal: true,
        buttons: {
            '{{Delete}}': function() {
                $(this).dialog('close');
                overlay.show();
                $(this).data('form').submit();
            },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
        }
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
            '{{Ok}}': function() {
                $(this).dialog('close');
                overlay.show();
                $(this).find('form').submit()
            },
            '{{Cancel}}': function() { $(this).dialog('close'); return false }
        }
    });

    $('#addmorechild').click(function() {
        var select = $('#child').clone().removeAttr('id');
        $('#form-addchild').append(select);
    });

    $('.guid').click(function() {
        /* select GUID, make ready for copy */
        $(this).select();
    }).mouseup(function(e) {
        e.preventDefault();
    });

    shortcut.add('Alt+N', function() { window.location = '/channel/add'; });
    shortcut.add('esc', function() { cancelDragging = true; });

    overlay = new Overlay();
});

</script>
