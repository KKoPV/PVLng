<script>
/**
 *
 *
 * @author      Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0

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
    var form = $('#form-move');
    form.attr('action', '/overview/'+action);
    form.find('input[name="id"]').val(id);
    $('#dialog-move').dialog('open');
    return false;
}

/**
 *
 */
var oTable, cancelDragging;

/**
 *
 */
$(function() {

    $.ajaxSetup({
        beforeSend: function setHeader(XHR) {
            XHR.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            return !$(oTable.fnGetNodes()[iDataIndex]).hasClass('hidden');
        }
    );

    lscache.setBucket('Overview');

    var hiddenNodes = lscache.get('HiddenNodes') || [];
    var pauseRedraw = false;

    if (lscache.get('HiddenNodesCollapsed')) {
        $('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]').data('expanded', 0);
        $('#treetoggletip').html('{{ExpandAll}}');
    }

    oTable = $('#tree').dataTable({
        bSort: false,
        bInfo: false,
        bLengthChange: false,
        bPaginate: false,
        bProcessing: true,
        bAutoWidth: false,
        bFilter: true,        /* Allow filter by coding, but   */
        sDom: '<"H"r>t<"F">', /* remove filter input from DOM. */
        aoColumnDefs: [
            { sWidth: "90%", aTargets: [ 0 ] },
            { sWidth: "1%", aTargets: [ 1, 2 ] }
        ]
    }).treetable({
        initialState: 'expanded',
        indent: 24,
        expandable: true,
        stringExpand: '{{Expand}}',
        stringCollapse: '{{Collapse}}',

        /* Helper functions for local storage handling */
        isCollapsed: function(id) {
            return (hiddenNodes.indexOf(id) != -1);
        },
        markCollapsed: function(id, collapsed) {
            var idx = hiddenNodes.indexOf(id);
            if (collapsed) {
                if (idx == -1) hiddenNodes.push(id);
            } else {
                if (idx != -1) hiddenNodes.splice(idx, 1);
                lscache.set('HiddenNodesCollapsed', false);
            }
            lscache.set('HiddenNodes', hiddenNodes);
        },

        onNodeInitialized: function() {
            /* check if the node is marked as collapsed */
            if (lscache.get('HiddenNodesCollapsed') || this.settings.isCollapsed(this.id)) this.collapse();
        },
        onInitialized: function() {
            /* set callbacks here AFTER stripes are initialized */
            this.settings.onNodeCollapse = function(node) {
                if (!pauseRedraw) oTable.fnDraw();
                /* mark node as collapsed */
                this.settings.markCollapsed(this.id, true);
            };
            this.settings.onNodeExpand = function(node) {
                if (!pauseRedraw) oTable.fnDraw();
                /* mark node as exoanded */
                this.settings.markCollapsed(this.id, false);
            };
            this.settings.onNodeExpanded = function(node) {
                if (!pauseRedraw) oTable.fnDraw();
            };
        }
    });

    $('#treetoggle').click(function(event) {
        event.preventDefault();

        $(document.body).addClass('wait');

        /* Forces show of processing indicator ... */
        setTimeout(function() {
            var toggler = $('#treetoggle');
            pauseRedraw = true;
            if (toggler.data('expanded') == 1) {
                oTable.treetable('collapseAll');
                toggler.attr('src','/images/ico/toggle_expand.png').attr('alt','[+]').data('expanded', 0);
                $('#treetoggletip').html('{{ExpandAll}}');
                lscache.set('HiddenNodesCollapsed', true);
            } else {
                hiddenNodes = [];
                lscache.set('HiddenNodes', hiddenNodes);
                oTable.treetable('expandAll');
                toggler.attr('src','/images/ico/toggle.png').attr('alt','[-]').data('expanded', 1);
                $('#treetoggletip').html('{{CollapseAll}}');
                lscache.set('HiddenNodesCollapsed', false);
            }
            pauseRedraw = false;
            oTable.fnDraw();

            $(document.body).removeClass('wait');
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
            .append($('<input/>', { type: 'hidden', name: 'entity', value: ui.draggable.data('entity') }))
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
        width: 480,
        modal: true,
        buttons: {
            '{{Add}}': function() {
                $(this).dialog('close');
                overlay.show();
                $(this).find('form').submit();
            },
            '{{Cancel}}': function() {
                $(this).dialog('close');
                return false;
            }
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
            '{{Cancel}}': function() {
                $(this).dialog('close');
                return false;
            }
        }
    });

    $('.delete-form').submit(function(){
        $('#dialog-confirm').data('form', this).dialog('open');
        return false;
    });

    /* Bind click listener to all delete node images */
    $('#tree tbody').on('click', '.delete-node', function() {

        var node = $(this.parentNode.parentNode).data('tt-id'),
            pos = oTable.fnGetPosition(this.parentNode.parentNode),
            url = PVLngAPI + 'tree/' + node + '.json';

        if (confirm('{{ConfirmDeleteTreeNode}}')) {
            $(document.body).addClass('wait');

            $.ajax({
                type: 'DELETE',
                url: url,
                dataType: 'json',
            }).done(function(data, textStatus, jqXHR) {
                oTable.fnDeleteRow(pos);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false, sticker: false,
                    text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                });
            }).always(function() {
                $(document.body).removeClass('wait');
            });
        }

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
            '{{Cancel}}': function() {
                $(this).dialog('close');
                return false;
            }
        }
    });

    $('#addmorechild').click(function() {
        // http://stackoverflow.com/a/17381913
        $('#form-addchild')
            .children('select')
            // call destroy to revert the changes made by Select2
            .select2('destroy')
            .end()
            .append(
                // clone the row and insert it in the DOM
                $('#form-addchild')
                .children('select')
                .first()
                .clone()
        )
        // enable Select2 on the select elements
        .children('select').select2();
    });

    $('.guid').click(function() {
        /* select GUID, make ready for copy */
        $(this).select();
    }).mouseup(function(e) {
        e.preventDefault();
    });

    shortcut.add('ESC', function() { cancelDragging = true; });
    shortcut.add('Alt+N', function() { window.location = '/channel/add'; });
});

</script>
