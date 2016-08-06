/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

$(function() {

    $.extend( $.fn.dataTable.defaults, {
        bLengthChange: false,
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bJQueryUI: true,
        bProcessing: true,
        bStateSave: DatatablesStateSave,
        iCookieDuration: 365*24*60*60,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
    });

    // dataTables sorting functions

    // Numerics with dot! as dec. separator
    $.fn.dataTableExt.oSort['numeric-dot-asc'] = function(a, b) {
        var x = (a == '-') ? 0 : a.replace(/,/, '');
        x = parseFloat( x );
        var y = (b == '-') ? 0 : b.replace(/,/, '');
        y = parseFloat( y );
        return ((x < y) ? -1 : ((x > y) ?    1 : 0));
    };

    $.fn.dataTableExt.oSort['numeric-dot-desc'] = function(a, b) {
        var x = (a == '-') ? 0 : a.replace(/,/, '');
        x = parseFloat( x );
        var y = (b == '-') ? 0 : b.replace(/,/, '');
        y = parseFloat( y );
        return ((x < y) ?    1 : ((x > y) ? -1 : 0));
    };

    // Numerics with comma! as dec. separator
    $.fn.dataTableExt.oSort['numeric-comma-asc'] = function(a, b) {
        var x = (a == '-') ? 0 : a.replace(/\./, '').replace(/,/, '.');
        x = parseFloat( x );
        var y = (b == '-') ? 0 : b.replace(/\./, '').replace(/,/, '.');
        y = parseFloat( y );
        return ((x < y) ? -1 : ((x > y) ?    1 : 0));
    };

    $.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a, b) {
        var x = (a == '-') ? 0 : a.replace(/\./, '').replace(/,/, '.');
        x = parseFloat( x );
        var y = (b == '-') ? 0 : b.replace(/\./, '').replace(/,/, '.');
        y = parseFloat( y );
        return ((x < y) ?    1 : ((x > y) ? -1 : 0));
    };

});
