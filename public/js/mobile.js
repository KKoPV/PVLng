/**
 *
 */
$(function() {});

/**
 *
 */
var verbose = false;

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
