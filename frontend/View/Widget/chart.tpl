/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

function _pvlng_chart() {

    new Highcharts.Chart({
        chart: {
            renderTo: 'pvlng-{GUID}',
            margin: [
                <!-- IF {LABELS} --> 5 <!-- ELSE --> 0 <!-- ENDIF -->,
                0,
                15,
                <!-- IF {LABELS} --> null <!-- ELSE --> 0 <!-- ENDIF -->
            ]
        },
        credits: { enabled: false },
        title: { text: '' },
        subtitle: {
            text: '{MAX} {UNIT}',
            x: <!-- IF {LABELS} --> 15 <!-- ELSE --> 0 <!-- ENDIF -->,
            y: {HEIGHT} - 10
        },
        legend: { enabled: false },
        tooltip: { enabled: false },
        xAxis: [{
            tickLength: 0,
            title: false,
            labels: false
        }],
        yAxis: [{
            title: false
            ,minPadding: 0.001
            ,endOnTick: false
            <!-- IF !{LABELS} -->
            ,labels: false
            ,lineWidth: 0
            <!-- ENDIF -->
        }],
        series: [
            {
                type: '{TYPE}',
                color: '{COLOR}',
                enableMouseTracking: false,
                marker: { enabled: false },
                data: {DATA}
            }
        ]
    }, function(chart) {
        var box = chart.renderer.text('{TIME1}', chart.plotLeft, chart.chartHeight)
                  .attr({ zIndex: 5 }).add().getBBox();
        chart.renderer.text('{TIME2}', chart.chartWidth - box.width, chart.chartHeight)
        .attr({ zIndex: 5 }).add();
    });

}

if (typeof Highcharts === 'undefined') document.write('<script src="http://code.highcharts.com/highcharts.js"></script>');

document.write('<div id="pvlng-{GUID}" style="width:{WIDTH}px;height:{HEIGHT}px"></div>');

/* code.stephenmorley.org - use Highcharts without jQuery */
var HighchartsAdapter = function() { function g(a,b,c) { "_listeners"in a||(a._listeners= {  } );b in a._listeners||(a._listeners[b]=[]);a._listeners[b].push(c) } function h(a,b,c) { "_listeners"in a&&b in a._listeners&&(c=k(c,a._listeners[b]),-1!=c&&a._listeners[b].splice(c,1)) } function k(a,b,c) { if(!b)return-1;for(c=void 0===c?0:c;c<b.length;c++)if(b[c]===a)return c;return-1 } var m,l="blur change click dblclick focus keydown keupress keyup load mousedown mouseenter mouseleave mousemove mouseout mouseover mouseup reset resize select submit touchcancel touchend touchenter touchleave touchmove touchstart unload wheel".split(" "); return { init:function(a) { m=a } ,getScript:function(a,b) { var c=document.createElement("script");c.src=a;c.onload=b;document.getElementsByTagName("head")[0].appendChild(c) } ,adapterRun:function(a,b) { return"getComputedStyle"in window?parseInt(window.getComputedStyle(a)[b]):a["client"+b.charAt(0).toUpperCase()+b.slice(1)] } ,addEvent:function(a,b,c) { -1==k(b,l)?g(a,b,c):"addEventListener"in a?a.addEventListener(b,c,!1):"attachEvent"in a?a.attachEvent("on"+b,c):g(a,b,c) } ,removeEvent:function(a,b,c) { -1==k(b,l)? h(a,b,c):"addEventListener"in a?a.removeEventListener(b,c,!1):"attachEvent"in a?a.detachEvent("on"+b,c):h(a,b,c) } ,fireEvent:function(a,b,c,d) { c||(c= {  } );if("_listeners"in a&&b in a._listeners)for(var f=0;f<a._listeners[b].length;f++)a._listeners[b][f].call(a,c);d&&!c.defaultPrevented&&d(c) } ,washMouseEvent:function(a) { return a } ,animate:function(a,b,c) { var d="attr"in a,f= {  } ,e;for(e in b)if(d&&"d"==e)f[e]= { end:b[e],paths:m.init(a,a.d,b[e]) } ;else { var n=d?a.attr(e):a.style[e],g=(b[e]?b[e]:0)-n;d&&a.attr(e, n);f[e]= { start:n,change:g }  } var h="duration"in c?c.duration:400,l=(new Date).getTime();"_animations"in a||(a._animations=[]);var p=window.setInterval(function() { var b=(new Date).getTime()-l,b=b>h?1:b/h,e;for(e in f) { var g=d&&"d"==e?m.step(f[e].paths[0],f[e].paths[1],b,f[e].end):f[e].start+f[e].change*b;d?a.attr(e,g):a.style[e]=g } 1==b&&("complete"in c&&c.complete(),window.clearInterval(p),a._animations.splice(k(p,a._animations),1)) } ,20);a._animations.push(p) } ,stop:function(a) { if("_animations"in a)for(;0< a._animations.length;)window.clearInterval(a._animations.pop()) } ,offset:function(a) { for(var b= { top:0,left:0 } ;a;)b.top+=a.offsetTop,b.left+=a.offsetLeft,a=a.offsetParent;return b } ,inArray:k,each:function(a,b) { for(var c=0;c<a.length;c++)b.call(a[c],a[c],c,a) } ,map:function(a,b) { for(var c=[],d=0;d<a.length;d++)c[d]=b.call(a[d],a[d],d);return c } ,grep:function(a,b) { for(var c=[],d=0;d<a.length;d++)b.call(a[d],a[d],d)&&c.push(a[d]);return c }  }  } ();
var runOnLoad = function(s,w,d,l,h,y) { function r() { for(y=1;s.length;)s.shift()() } d[l]?(d[l]('DOMContentLoaded',r,0),w[l]('load',r,0)):(d[h]('onreadystatechange',function() { d.readyState=='complete'&&r() } ),w[h]('onload',r));return function(t) { y?t():s.push(t) }  } ([],window,document,'addEventListener','attachEvent');

runOnLoad(_pvlng_chart);
