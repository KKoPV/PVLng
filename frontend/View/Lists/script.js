<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
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
/*
            _log('Attributes', channel);
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

        $(document.body).removeClass('wait');
    });
}

/**
 *
 */
function updateOutput() {
    updateList();
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
        bDeferRender: true,
        bProcessing: true,
        bLengthChange: true,
        aLengthMenu: [ [20, 50, 100, -1], [20, 50, 100, '{{All}}'] ],
        iDisplayLength: 20,
        bInfo: true,
        bPaginate: true,
        sPaginationType: 'full_numbers',
        /* Add placeholder div into table header */
        sDom: 'r<"H"l<"#dt-toolbar">>t<"F"ip>',
        aoColumns: [
            { sWidth: '20%' },
            { sClass: 'r' },
            { sClass: 'r' },
            { sClass: 'r' },
            { sClass: 'r' },
            { sClass: 'r' },
            { sWidth: '1%', bSortable: false }
        ],
        aaSorting: [[ 0, 'desc' ]],
        fnInitComplete: function(oSettings, json) {
            /* Fill extra div #dt-toolbar in sDom with pre defined content,
               Remove wrapper div, replace filter div - reuse styling, Move in */
            $('#toolbar').unwrap().addClass('dataTables_filter').appendTo('#dt-toolbar');
            $('select', '#list_wrapper').select2();
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
    $('#list tbody').on('click', '.delete-reading', function() {
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

    $('#channel').change(function() {
        /* Scroll to navigation as top most visible element */
        $('html, body').animate({ scrollTop: $("#nav").offset().top-3 }, 2000);
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

    shortcut.add('Alt+P', function() { pvlng.changeDates(-1); });
    shortcut.add('Alt+N', function() { pvlng.changeDates(1); });
    shortcut.add('F6',    function() { updateList(); });
});

</script>
