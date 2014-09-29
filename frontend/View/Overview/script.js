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
 * Add class "marked-for-deletion" for all relevant rows,
 * call recursive and move downwards to handle all sub channels
 */
function markRows4Deletion( id) {
    oTable.$('tr').each(function(i, tr) {
        tr = $(tr);
        if (tr.data('tt-id') == id) {
            tr.animate({ backgroundColor: '#FFCCCC' }).addClass('marked-for-deletion');
        } else if (tr.data('tt-parent-id') == id) {
            markRows4Deletion(tr.data('tt-id'));
        }
    });
}

/**
 *
 */
var oTable, cancelDragging;

/**
 *
 */
$(function() {

    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            return !$(oTable.fnGetNodes()[iDataIndex]).hasClass('hidden');
        }
    );

    var sHN = 'Overview-HiddenNodes', sHNC = 'Overview-HiddenNodes-Collapsed';

    var hiddenNodes = lscache.get(sHN) || [];
    var pauseRedraw = false;

    if (lscache.get(sHNC)) {
        $('#treetoggle', '#tree').removeClass('off').addClass('on');
        $('#treetoggletip', '#tree').html('{{ExpandAll}}');
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
                lscache.set(sHNC, false);
            }
            lscache.set(sHN, hiddenNodes);
        },

        onNodeInitialized: function() {
            /* check if the node is marked as collapsed */
            if (lscache.get(sHNC) || this.settings.isCollapsed(this.id)) this.collapse();
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

    $('#treetoggle', '#tree').click(function(event) {
        pauseRedraw = true;
        var el = $(this);
        if (el.hasClass('off')) {
            oTable.treetable('collapseAll');
            el.removeClass('off').addClass('on');
            $('#treetoggletip').html('{{ExpandAll}}');
            lscache.set(sHNC, true);
        } else {
            hiddenNodes = [];
            lscache.set(sHN, hiddenNodes);
            oTable.treetable('expandAll');
            el.removeClass('on').addClass('off');
            $('#treetoggletip').html('{{CollapseAll}}');
            lscache.set(sHNC, false);
        }
        pauseRedraw = false;
        oTable.fnDraw();
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

    /* Bind click listener to all GUID images */
    $('#tree tbody').on('click', '.guid', function() {
        $.alert(
            $('<p/>').append(
                $('<input/>')
                    .css({ fontFamily: 'monospace', fontSize: '150%', width: '100%', border: 0, outline: 0 })
                    .prop('readonly', 'readonly')
                    .val($(this).data('guid'))
                    /* Prepare to copy into clipboard ... */
                    .on('click', function() { this.select() })
            ),
            '{{Channel}} GUID'
        );
    });

    /* Bind click listener to all create alias images */
    $('#tree tbody').on('click', '.create-alias', function() {

        overlay.show();

        /* Get tree table Id from parent <tr> */
        var node = $(this.parentNode.parentNode).data('tt-id'),
            that = this;

        $.ajax({
            type: 'PUT',
            url: PVLngAPI + 'tree/alias/' + node + '.json',
            dataType: 'json',
        }).done(function(data, textStatus, jqXHR) {
            $.pnotify({
                type: textStatus,
                text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
            });
            /* Remove icon, unbind handler and reset cursor */
            $(that).prop('src', '/images/pix.gif').removeClass('create-alias').removeClass('btn');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $.pnotify({
                type: textStatus, hide: false, sticker: false, text: errorThrown
            });
        }).always(function() {
            overlay.hide();
        });
    });

    /* Bind click listener to all delete node images */
    $('#tree tbody').on('click', '.node-delete, .node-delete-next', function() {

        /* Get tree table Id from parent <tr> */
        var tr = $(this.parentNode.parentNode);

        markRows4Deletion(tr.data('tt-id'));

        var msg = tr.hasClass('group') ? '{{ConfirmDeleteTreeItems}}' : '{{ConfirmDeleteTreeNode}}';

        $.confirm($('<p/>').html(msg), '{{Confirm}}', '{{Yes}}', '{{No}}')
        .then(function(ok) {
            var rows = $('.marked-for-deletion');

            if (!ok) {
                rows.removeClass('.marked-for-deletion').css({ backgroundColor: '' });
                return;
            }

            oTable.addClass('wait');

            $.ajax({
                type: 'DELETE',
                url: PVLngAPI + 'tree/' + node + '.json',
                dataType: 'json',
            }).done(function(data, textStatus, jqXHR) {
                /* Loop all rows and delete also rows with tt-parent-id = node if any */
                rows.animate({ backgroundColor: '#CCFFCC' }, 'slow', function() {
                    rows.each(function(i, tr) {
                        /* Get row position and delete without redraw */
                        oTable.fnDeleteRow(oTable.fnGetPosition(tr), null, false);
                    });
                    oTable.fnDraw();
                });
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false, sticker: false,
                    text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                });
            }).always(function() {
                rows.removeClass('.marked-for-deletion').css({ backgroundColor: '' });
                oTable.removeClass('wait');
            });
        });
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

    shortcut.add('ESC', function() { cancelDragging = true; });
    shortcut.add('Alt+N', function() { window.location = '/channel/add'; });
});

</script>
