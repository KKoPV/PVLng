/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

document.write('<table style="border-collapse:collapse">
<tr>
  <td colspan="3" style="padding:0;margin:0">
    <a target="_blank" href="http://pvoutput.org/intraday.jsp?sid=13606">
      <span id="pvlng-UUID"
          values="42,134,303,605,851,1083,1522,2103,2887,3274,2383,2309,3130,4119,4829,4359,5002,5736,5758,6188,3347,2636,3312,2311,2271">
      </span>
    </a>
  </td>
</tr>

/* for each channel >>> */

<tr style="margin:3px 0">
  <td style="padding:0;margin:0;font-family:helvetica;font-size:11px;text-align:left">
    name
  </td>
  <td style="padding:0;margin:0 5px 0 5px;font-family:helvetica;font-size:11px;text-align:right">
    value
  </td>
  <td style="padding:0;margin:0;font-family:helvetica;font-size:11px;text-align:left">
    unit
  </td>
</tr>

/* <<< for each channel */

<tr>
  <td colspan="3" style="padding:0;margin:0;font-family:helvetica;font-size:10;text-align:center">
    3-Jan-14 12:20PM
  </td>
</tr>
</table>');

/* http://stackoverflow.com/questions/2170439/how-to-embed-javascript-widget-that-depends-on-jquery-into-an-unknown-environmen */

(function() {

    /* Localize jQuery variable */
    var jQuery;


    /* Load jQuery if not present */
    if (window.jQuery === undefined || window.jQuery.fn.jquery !== '2.0.0') {
        var script_tag = document.createElement('script');
        script_tag.setAttribute("type", "text/javascript");
        script_tag.setAttribute("src", "http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js");
        if (script_tag.readyState) {
          script_tag.onreadystatechange = function () { /* For old versions of IE */
              if (this.readyState == 'complete' || this.readyState == 'loaded') {
                  scriptLoadHandler();
              }
          };
        } else { /* Other browsers */
          script_tag.onload = scriptLoadHandler;
        }
        /* Try to find the head, otherwise default to the documentElement */
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    } else {
        /* The jQuery version on the window is the one we want to use */
        jQuery = window.jQuery;
        main();
    }

    /* Called once jQuery has loaded */
    function scriptLoadHandler() {
        /* Restore $ and window.jQuery to their previous values and store the
           new jQuery in our local jQuery variable */
        jQuery = window.jQuery.noConflict(true);
        /* Call our main function */
        main();
    }

    /* Our main function */
    function main() {
        jQuery(document).ready(function($) {
            {SPARKLINE_JS}

            /* We can use jQuery 2.0.0 here */
            jQuery(document).ready(function($) {
                $("#pvlng-UUID").sparkline("html", { type: "line", lineColor: "#339933", fillColor: "#CCFF66", width: "220", height: "50", spotRadius: 0 });
            });
        });
    }

})(); /* We call our anonymous function immediately */
