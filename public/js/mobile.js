/**
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

// $(function() {});

/**
 *
 */
var verbose = false;

/**
 * Idea from http://paulgueller.com/2011/04/26/parse-the-querystring-with-jquery/
 */
$.parseQueryString = function() {
    var qs = window.location.search.replace('?', ''), result = {}, v;
    if (qs) {
        $.each(qs.split('&'), function(id, data) {
            v = data.split('=');
            if (v.length == 2) result[v[0]] = decodeURIComponent(v[1]);
        });
    }
    return result;
};

/**
 *
 */
function _log() {
  if (!verbose) return;
  var d = new Date;
  $(arguments).each(function(id, data) {
    console.log(d.toLocaleString()+'.'+d.getMilliseconds());
    console.log(data);
  });
}

/**
 *
 */
String.prototype.repeat = function(count) {
  if (count < 1) return '';
  var result = '', pattern = this.valueOf();
  while (count > 0) {
    if (count & 1) result += pattern;
    count >>= 1, pattern += pattern;
  }
  return result;
};
