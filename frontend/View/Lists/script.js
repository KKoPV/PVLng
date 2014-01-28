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
    var from = Date.parse($('#from').datepicker('getDate')) + dir*24*60*60*1000;
    if (from > (new Date).getTime()) {
        $.pnotify({ type: 'info', text: "Can't go beyond today." });
        return;
    }
    var to = Date.parse($('#to').datepicker('getDate')) + dir*24*60*60*1000;
    $('#from').datepicker('option', 'maxDate', 0);
    $('#to').datepicker('option', 'maxDate', 0);
    if (dir < 0) {
        /* backwards */
        $('#from').datepicker('setDate', new Date(from));
        $('#to').datepicker('setDate', new Date(to));
    } else {
        /* foreward */
        $('#to').datepicker('setDate', new Date(to));
        $('#from').datepicker('setDate', new Date(from));
    }
    updateList();
}

/**
 *
 */
var channel = {}, consumption_raw,
    /* String length for Datetime column */
    dtLen = { '': 19, i: 16, h: 13, d: 10, w: 10, m: 7, q: 7, y: 4 };

/**
 *
 */
function updateList() {

    var guid = $('#channel').find('option:selected').val();

    if (!guid) {
        var icon = $('#icon');
        icon.prop('src', icon.data('none'));
        listTable.fnClearTable();
        $('.export').button('option', 'disabled', true);
        return;
    }

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
            _log('Data', data);

            $('#icon').prop('src', channel.icon);
            $('.export').button( 'option', 'disabled', data.length == 0);

            consumption_raw = {};

            $(data).each(function(id, row) {
                /* remeber raw consumption data for footer calculation */
                if (channel.meter) consumption_raw[id] = row.consumption;

                listTable.fnAddData(
                    [
                        row.timestamp,
                        row.datetime.substr(0, dtLen[period]),
                        channel.numeric ? $.number(row.data, channel.decimals, '{{DSEP}}', '{{TSEP}}') : row.data,
                        /* These 3 columns are hidden for non-numeric channels */
                        channel.numeric ? $.number(row.min, channel.decimals, '{{DSEP}}', '{{TSEP}}') : null,
                        channel.numeric ? $.number(row.max, channel.decimals, '{{DSEP}}', '{{TSEP}}') : null,
                        channel.numeric ? $.number(row.consumption, channel.decimals, '{{DSEP}}', '{{TSEP}}') : null,
                        row.count
                    ],
                    false
                );
            });

            listTable.fnSetColumnVis( 3, channel.numeric && period && !channel.meter );
            listTable.fnSetColumnVis( 4, channel.numeric && period && !channel.meter );
            listTable.fnSetColumnVis( 5, channel.numeric && channel.meter );
            listTable.fnSetColumnVis( 6, channel.numeric && period );
        }
    ).fail(function (jqxhr, textStatus, error) {
        $.pnotify({
            type: textStatus,
            text: jqxhr.responseText,
            hide: false,
            sticker: false
        });
    }).complete(function () {
        listTable.fnDraw();
        listTable.fnAdjustColumnSizing();
        listTable.fnProcessingIndicator(false);
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
        beforeSend: function setHeader(xhr) {
            xhr.setRequestHeader('X-PVLng-Key', '{APIKEY}');
        }
    });

    $.fn.dataTableExt.oApi.fnProcessingIndicator = function ( oSettings, onoff ) {
        if (typeof(onoff) == 'undefined') onoff = true;
        this.oApi._fnProcessingDisplay( oSettings, onoff );
    };

    <!-- IF {LANGUAGE} != 'en' -->
    $.datepicker.setDefaults($.datepicker.regional['{LANGUAGE}']);
    <!-- ENDIF -->

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
            $('#to').datepicker( 'option', 'maxDate', 0 );
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
        sDom: 'r<"H"l<"#dt-toolbar">>t<"F"ip><"clear">',
        aLengthMenu: [ [10, 20, 50, 100, -1], [10, 20, 50, 100, '{{All}}'] ],
        iDisplayLength: 20,
        sPaginationType: 'full_numbers',
        aaSorting: [[ 1, 'desc' ]],
        aoColumnDefs: [
            { bVisible: false, aTargets: [ 0 ] },
            { sType: 'numeric', aTargets: [ 2, 3, 4, 5, 6 ] },
            { sClass: 'r', aTargets: [ 2, 3, 4, 5, 6 ] }
        ],
        fnInitComplete: function(oSettings, json) {
            /* Fill extra div in sDom with pre defined content */
            $('#dt-toolbar')
                /* replacing filter div, so reuse styling... */
                .addClass('dataTables_filter')
                /* Move buttons toolbar in */
                .append($('#toolbar').unwrap());
            $('select[name=list_length]').addClass('ui-corner-all');
            updateList();
        },
        fnHeaderCallback: function( nHead, aData, iStart, iEnd, aiDisplay ) {
            var th = nHead.getElementsByTagName('th')[1],
                match = th.innerHTML.match(/{{Reading}}[^<]*/g),
                unit = channel.unit ? ' ['+channel.unit+']' : '';
            th.innerHTML = th.innerHTML.replace(match[0], '{{Reading}}' + unit);
        },
        fnFooterCallback: function( nFoot, aData, iStart, iEnd, aiDisplay ) {
            $('#tf-consumption').html('&nbsp;');
            if (!channel.meter || (iEnd - iStart) == 0) return;

            var consumption = 0;
            for (var i=iStart; i<iEnd; i++) {
                consumption += consumption_raw[aiDisplay[i]];
            }
            $('#tf-consumption').number(consumption, channel.decimals, '{{DSEP}}', '{{TSEP}}');
        },
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' }
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
        icons: {
            primary: 'ui-icon-calendar'
        },
        text: false
    }).click(function(event) {
        event.preventDefault();
        var d = new Date;
        /* Reset max. date today */
        $('#from').datepicker( 'option', 'maxDate', d );
        $('#to').datepicker( 'option', 'maxDate', d );
        /* Set max. date today */
        $('#from').datepicker('setDate', d);
        $('#to').datepicker('setDate', d);
        updateList();
    });

    $('#btn-refresh').button({
        icons: {
            primary: 'ui-icon-refresh'
        },
        text: false
    }).click(function(e) {
        updateList();
        return false;
    });

    $('.export').click(function(event) {
        event.preventDefault();

        var i,
            separator = $(this).data('separator'),
            mime = $(this).data('mime'),
            extension = $(this).data('extension'),
            data = listTable.fnGetData(),
            len = data.length,
            csv = [
                [
                    'Timestamp',
                    '"Date time"',
                    'Data',
                    'Min',
                    'Max',
                    'Consumption',
                    '"Row count"'
                ].join(separator) + "\n"
            ],
            d = new Date(),
            name = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate() + '-' +
                   (d.getHours()+1) + ':' + d.getMinutes() + ':' + d.getSeconds() + '-' +
                   channel.name;

        if (channel.description) name += '-' + channel.description;

        listTable.fnProcessingIndicator();

        var period_count = +$('#periodcnt').val(),
            period = $('#period').val();

        $.getJSON(
            PVLngAPI + 'data/' + channel.guid + '.json',
            {
                full:       true,
                short:      true,
                start:      $('#fromdate').val(),
                end:        $('#todate').val() + ' 24:00',
                period:     period_count + period
            },
            function(data) {
                /* Transform to array of strings for saveAs */
                $(data).each(function (id, row) {
                    csv.push(row.join(separator) + "\n");
                });

                $.getJSON(
                    PVLngAPI + 'hash.json',
                    { text: name },
                    function(data) {
                        saveAs(
                            new Blob(csv, { type: mime + ';charset=utf-8' }),
                            data.slug + '.' + extension
                        );
                    }
                );
            }
        ).complete(function () {
            listTable.fnProcessingIndicator(false);
        });
    });

    shortcut.add('Alt+P', function() { changeDates(-1); });
    shortcut.add('Alt+N', function() { changeDates(1); });
    shortcut.add('F6',    function() { updateList(); });
});

</script>
