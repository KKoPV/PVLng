/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 * @revision    $Rev$
 */

$(function() {

  /* dataTables sorting functions */

  /* Numerics with dot! as dec. separator */
  jQuery.fn.dataTableExt.oSort['numeric-dot-asc']  = function(a, b) {
    var x = (a == '-') ? 0 : a.replace(/,/, '');
    x = parseFloat( x );
    var y = (b == '-') ? 0 : b.replace(/,/, '');
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
  };

  jQuery.fn.dataTableExt.oSort['numeric-dot-desc'] = function(a, b) {
    var x = (a == '-') ? 0 : a.replace(/,/, '');
    x = parseFloat( x );
    var y = (b == '-') ? 0 : b.replace(/,/, '');
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
  };

  /* Numerics with comma! as dec. separator */
  jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a, b) {
    var x = (a == '-') ? 0 : a.replace(/\./, "").replace(/,/, ".");
    x = parseFloat( x );
    var y = (b == '-') ? 0 : b.replace(/\./, "").replace(/,/, ".");
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
  };

  jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a, b) {
    var x = (a == '-') ? 0 : a.replace(/\./, "").replace(/,/, ".");
    x = parseFloat( x );
    var y = (b == '-') ? 0 : b.replace(/\./, "").replace(/,/, ".");
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
  };

});
