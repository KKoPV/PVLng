<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

/* ------------------------------------------------------------------------ */
</script>

<script src="/js/Blob.js+FileSaver.js"></script>

<script>
/**
 *
 */
function changeDates( dir ) {
    dir *= 24*60*60*1000;

    var from = new Date(Date.parse($('#from').datepicker('getDate')) + dir),
        to = new Date(Date.parse($('#to').datepicker('getDate')) + dir);

    if (to > new Date) {
        $.pnotify({ type: 'info', text: "Can't go beyond today." });
        return;
    }

    $('#from').datepicker('option', 'maxDate', to).datepicker('setDate', from);
    $('#to').datepicker('option', 'minDate', from).datepicker('setDate', to);

    updateList();
}

/**
 *
 */
var channel = {},
    /* String length for Datetime column */
    dtLen = { '': 19, i: 16, h: 13, d: 10, w: 10, m: 7, q: 7, y: 4 };

/**
 *
 */
function updateList() {

    var guid = $('#channel option:selected').val();

    if (!guid) {
        var icon = $('#icon');
        icon.prop('src', icon.data('none'));
        $('#icon-private').hide();
        listTable.fnClearTable();
        $('.export').button('option', 'disabled', true);
        return;
    }

    $(document.body).addClass('wait');
    listTable.fnClearTable(false);
    listTable.fnProcessingIndicator();

    var period_count = +$('#periodcnt').val(),
        period = $('#period').val(),
        url = PVLngAPI + 'data/' + guid + '.json';

    _log('Fetch', url);

    $.getJSON(
        url,
        {
            attributes: true,
            full:       true,
            start:      $('#fromdate').val(),
            end:        $('#todate').val() + ' 24:00',
            period:     period_count + period
        },
        function(data) {
            /* pop out 1st row with attributes */
            channel = data.shift();

            _log('Attributes', channel);
/*
            _log('Data', data);
*/
            $('#icon').prop('src', channel.icon).prop('title', channel.type).tipTip();
            $('#icon-private').toggle(!channel.public);
            $('.export').button( 'option', 'disabled', data.length == 0);

            $(data).each(function(id, row) {
                listTable.fnAddData(
                    [
                        row.datetime.substr(0, dtLen[period]),
                        channel.numeric ? +row.data.toFixed(channel.decimals) : row.data,
                        /* These 3 columns are hidden for non-numeric channels */
                        channel.numeric ? +row.min.toFixed(channel.decimals) : null,
                        channel.numeric ? +row.max.toFixed(channel.decimals) : null,
                        channel.numeric ? +row.consumption.toFixed(channel.decimals) : null,
                        row.count,
                        period ? '' : '<img class="delete-reading" class="tip" title="{{DeleteReading}}" src="/images/ico/minus_circle.png" data-timestamp="'+row.timestamp+'" data-value="'+row.data+'" />'
                    ],
                    false
                );
            });

            listTable.fnSetColumnVis( 2, channel.numeric && period && !channel.meter );
            listTable.fnSetColumnVis( 3, channel.numeric && period && !channel.meter );
            listTable.fnSetColumnVis( 4, channel.numeric && channel.meter );
            listTable.fnSetColumnVis( 5, channel.numeric && period );
            listTable.fnSetColumnVis( 6, !period );
        }
    ).fail(function (jqXHR, textStatus, error) {
        $.pnotify({
            type: textStatus,
            text: jqXHR.responseText.trim(),
            hide: false,
            sticker: false
        });
    }).complete(function () {
        listTable.fnDraw();
        listTable.fnAdjustColumnSizing();
        listTable.fnProcessingIndicator(false);
        $(document.body).removeClass('wait');
    });
}

/**
 *
 */
function changePreset() {

    var preset = $('#preset').val().match(/(\d+)(\w+)/);
    var from = new Date($("#from").datepicker('getDate'));

    if (!preset) {
        $('#periodcnt').val(1);
        $('#period').val('');
        return;
    }

    switch (preset[2]) {
        case 'd': /* day - set start to 1st day of month */
            from.setDate(1);
            break;
        case 'w': /* week - set start to 1st day of month */
            from.setDate(1);
            break;
        case 'm': /* month - set start to 1st day of year */
            from.setDate(1);
            from.setMonth(0);
            break;
    }

    $("#from").datepicker('setDate', from);
    $('#periodcnt').val(preset[1]);
    $('#period').val(preset[2]);
}

/**
 *
 */
var listTable;

/**
 *
 */
$(function() {

    $.ajaxSetup({
        beforeSend: function setHeader(XHR) {
            XHR.setRequestHeader('X-PVLng-Key', PVLngAPIkey);
        }
    });

    $.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
        if (typeof(onoff) == 'undefined') onoff = true;
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };

    $.fn.dataTableExt.oPagination.listbox = {
        /*
         * Function: oPagination.listbox.fnInit
         * Purpose:  Initalise dom elements required for pagination with listbox input
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           node:nPaging - the DIV which contains this pagination control
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        fnInit: function (oSettings, nPaging, fnCallbackDraw) {
            var nInput = document.createElement('select');
            var nPage = document.createElement('span');
            var nOf = document.createElement('span');
            nOf.className = "paginate_of";
            nPage.className = "paginate_page";
            if (oSettings.sTableId !== '') {
                nPaging.setAttribute('id', oSettings.sTableId + '_paginate');
            }
            nInput.style.display = "inline";
            nPage.innerHTML = "{{Page}} ";
            nPaging.appendChild(nPage);
            nPaging.appendChild(nInput);
            nPaging.appendChild(nOf);
            this.nPaging = nPaging; /* Remember div for toggle */
            $(nInput).change(function (e) { /* Set DataTables page property and redraw the grid on listbox change event. */
                window.scroll(0,0); /* scroll to top of page */
                if (this.value === "" || this.value.match(/[^0-9]/)) { /* Nothing entered or non-numeric character */
                    return;
                }
                var iNewStart = oSettings._iDisplayLength * (this.value - 1);
                if (iNewStart > oSettings.fnRecordsDisplay()) { /* Display overrun */
                    oSettings._iDisplayStart = (Math.ceil((oSettings.fnRecordsDisplay() - 1) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
                    fnCallbackDraw(oSettings);
                    return;
                }
                oSettings._iDisplayStart = iNewStart;
                fnCallbackDraw(oSettings);
            }); /* Take the brutal approach to cancelling text selection */
            $('span', nPaging).bind('mousedown', function () {
                return false;
            });
            $('span', nPaging).bind('selectstart', function () {
                return false;
            });
        },

        /*
         * Function: oPagination.listbox.fnUpdate
         * Purpose:  Update the listbox element
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        fnUpdate: function (oSettings, fnCallbackDraw) {
            if (!oSettings.aanFeatures.p) {
                return;
            }
            var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
            $(this.nPaging).toggle(!!iPages); /* Hide paging div for empty table */
            var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1; /* Loop over each instance of the pager */
            var an = oSettings.aanFeatures.p;
            for (var i = 0, iLen = an.length; i < iLen; i++) {
                var spans = an[i].getElementsByTagName('span');
                var inputs = an[i].getElementsByTagName('select');
                var elSel = inputs[0];
                if(elSel.options.length != iPages) {
                    elSel.options.length = 0; /* clear the listbox contents */
                    for (var j = 0; j < iPages; j++) { /* add the pages */
                        var oOption = document.createElement('option');
                        oOption.text = j + 1;
                        oOption.value = j + 1;
                        try {
                            elSel.add(oOption, null); /* standards compliant; doesn't work in IE */
                        } catch (ex) {
                            elSel.add(oOption); /* IE only */
                        }
                    }
                    spans[1].innerHTML = '&nbsp; {{of}} &nbsp;' + iPages;
                }
                elSel.value = iCurrentPage;
            }
        }
    };

    if (language != 'en') {
        $.datepicker.setDefaults($.datepicker.regional[language]);
    }

    var d = new Date();

    $('#from').datepicker({
        altField: '#fromdate',
        altFormat: 'mm/dd/yy',
        maxDate: 0,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $('#to').datepicker( 'option', 'minDate', selectedDate );
        }
    }).datepicker('setDate', d);

    $('#to').datepicker({
        altField: '#todate',
        altFormat: 'mm/dd/yy',
        maxDate: 0,
        showButtonPanel: true,
        showWeek: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            $('#from').datepicker( 'option', 'maxDate', selectedDate );
        }
    }).datepicker('setDate', d);

    listTable = $('#list').DataTable({
        bFilter: false,
        bJQueryUI: true,
        bDeferRender: true,
        bProcessing: true,
        /* Add placeholder div into table header */
        sDom: 'r<"H"l<"#dt-toolbar">>t<"F"ip>',
        aLengthMenu: [ [10, 20, 50, 100, -1], [10, 20, 50, 100, '{{All}}'] ],
        iDisplayLength: 20,
        sPaginationType: 'full_numbers',
        sPaginationType: 'listbox',
        bAutoWidth: false,
        aaSorting: [[ 0, 'desc' ]],
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumnDefs: [
            { sClass: 'r', aTargets: [ 1, 2, 3, 4, 5 ] },
            { bSortable: false, aTargets: [ 6 ] },
            { sWidth: "20%", aTargets: [ 0 ] },
            { sWidth: "1%", aTargets: [ 6 ] }
        ],
        fnInitComplete: function(oSettings, json) {
            /* Fill extra div #dt-toolbar in sDom with pre defined content,
               Remove wrapper div, replace filter div - reuse styling, Move in */
            $('#toolbar').unwrap().addClass('dataTables_filter').appendTo('#dt-toolbar');
            $('select[name=list_length]').addClass('ui-corner-all');
            updateList();
        },
        fnFooterCallback: function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            $('#tf-consumption').html('&nbsp;');
            if (!channel.meter || (iEnd - iStart) == 0) return;

            var consumption = 0;
            for (var i=iStart; i<iEnd; i++) {
                consumption += aData[aiDisplay[i]][4];
            }
            $('#tf-consumption').html(consumption.toFixed(channel.decimals));
        },
        fnDrawCallback: function(oSettings) {
            $('img.delete-reading').button();
        }
    });

    /* Bind click listener to all delete buttons */
    $('#list tbody').on('click', 'img.delete-reading', function() {
        var ts = $(this).data('timestamp'),
            question = '{{ConfirmDeleteReading}}\n\n' +
                       '- ' + (new Date(ts*1000)).toLocaleString() + '\n' +
                       '- {{Reading}} : ' + $(this).data('value');

        if (confirm(question)) {
            listTable.fnProcessingIndicator();
            $(document.body).addClass('wait');

            var url = PVLngAPI + 'data/' + channel.guid + '/' + ts + '.json',
                pos = listTable.fnGetPosition(this.parentNode.parentNode);

            $.ajax({
                type: 'DELETE',
                url: url,
                dataType: 'json',
            }).done(function(data, textStatus, jqXHR) {
                listTable.fnDeleteRow(pos);
                $.pnotify({ type: 'success', text: '{{ReadingDeleted}}' });
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    type: textStatus, hide: false, sticker: false,
                    text: jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText
                });
            }).always(function() {
                listTable.fnProcessingIndicator(false);
                $(document.body).removeClass('wait');
            });
        }
    });

    $(window).bind('resize', function() {
        listTable.fnAdjustColumnSizing();
    });

    $('#preset').change(function() {
        changePreset();
        updateList();
    });

    $('#channel').change(function() {
        updateList();
    });

    $('#btn-reset').button({
        icons: { primary: 'ui-icon-calendar' },
        text: false
    }).click(function(event) {
        event.preventDefault();
        var d = new Date;
        /* Set date ranges */
        $('#from').datepicker('option', 'maxDate', d);
        $('#to').datepicker('option', 'minDate', d);
        /* Set date today */
        $('#from').datepicker('setDate', d);
        $('#to').datepicker('setDate', d);
        updateList();
    });

    $('#btn-refresh').button({
        icons: { primary: 'ui-icon-refresh' },
        text: false
    }).click(function(e) {
        updateList();
        return false;
    });

    $('.export').click(function(event) {
        event.preventDefault();
        listTable.fnProcessingIndicator();

        var separator = $(this).data('separator'),
            mime = $(this).data('mime'),
            extension = $(this).data('extension'),
            csv = [
                ['Timestamp', '"Date time"', 'Data', 'Min', 'Max',
                 'Consumption', '"Row count"'].join(separator) + "\n"
            ],
            d = new Date(),
            name = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate() + '-' +
                   (d.getHours()+1) + ':' + d.getMinutes() + ':' + d.getSeconds() + '-' +
                   channel.name;

        if (channel.description) name += '-' + channel.description;

        $.when(
            /* Fetch raw data */
            $.getJSON(
                PVLngAPI + 'data/' + channel.guid + '.json',
                {   full:       true,
                    short:      true,
                    start:      $('#fromdate').val(),
                    end:        $('#todate').val() + ' 24:00',
                    period:     $('#periodcnt').val() + $('#period').val()
                }
            ),
            /* Get hashes for channel name */
            $.getJSON(PVLngAPI + 'hash.json', { text: name })
        ).then(
            function(jqXHR1, jqXHR2) {
                /* Transform to array of strings for saveAs */
                $(jqXHR1[0]).each(function (id, row) {
                    csv.push(row.join(separator) + "\n");
                });
                saveAs(
                    new Blob(csv, { type: mime + ';charset=utf-8' }),
                    jqXHR2[0].text + '.' + extension
                );
            },
            function(jqXHR) {
                alert(jqXHR.responseText);
            }
        ).always(function() {
            listTable.fnProcessingIndicator(false);
        });
    });

    shortcut.add('Alt+P', function() { changeDates(-1); });
    shortcut.add('Alt+N', function() { changeDates(1); });
    shortcut.add('F6',    function() { updateList(); });
});

</script>
