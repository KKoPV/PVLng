<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id: v1.0.0.2-19-gf67765b 2013-05-05 22:03:31 +0200 Knut Kohl $
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<!-- IF !{DEVELOPMENT} -->
	<!-- load Highcharts scripts direct from highcharts.com -->
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/highcharts-more.js"></script>
<!-- ELSE -->
	<!-- load local Highcharts scripts -->
	<script src="/js/highcharts.js"></script>
	<script src="/js/highcharts-more.js"></script>
<!-- ENDIF -->

<script>

var
	charts = [],
	chartsNoData = [],
	timeout,
	TreeExpanded = true;

var chartOptions = {

	plotOptions: {
		gauge: {
			dial: {
				backgroundColor: 'gray',
				rearLength: '25%'
			},
			pivot: {
				radius: 10,
				borderWidth: 1,
				borderColor: 'gray',
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [ [0, 'white'], [1, 'gray'] ]
				}
			}
		}
	},

	pane: {
		startAngle: -135,
		endAngle: 135,
		background: [{
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
				stops: [ [0, '#FFF'], [1, '#333'] ]
			},
			borderWidth: 0,
			outerRadius: '109%'
		}, {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
				stops: [ [0, '#333'], [1, '#FFF'] ]
			},
			borderWidth: 1,
			outerRadius: '107%'
		}, {
			/* default background */
		}, {
			backgroundColor: '#DDD',
			borderWidth: 0,
			outerRadius: '105%',
			innerRadius: '103%'
		}]
	},

	tooltip: { enabled: false },
	credits: { enabled: false }
};

/**
 *
 */
function ToggleTree( force ) {
	TreeExpanded = (force != undefined) ? force : !TreeExpanded;

	$('input.channel').each(function(id, el) {
		/* checkbox -> wrapper div -> td -> tr */
		$(el).parent().parent().parent().toggle(TreeExpanded || $(el).is(':checked'));
	});

	$('tr.no-graph').each(function(id, el) {
		$(el).toggle(TreeExpanded);
	});

	var css;

	if (TreeExpanded) {
		$('#treetoggle').attr('src','/images/ico/toggle.png').attr('alt','[-]');
		$('#tiptoggle').html('{{CollapseAll}}');

		$('span.indenter').each(function(id, el) {
			el = $(el);
			/* Restore left indent */
			el.css('padding-left', el.data('indent'));
		});
	} else {
		$('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]');
		$('#tiptoggle').html('{{ExpandAll}}');

		$('span.indenter').each(function(id, el) {
			/* Remove left indent */
			$(el).css('padding-left', 0);
		});
	}

}

/**
 *
 */
function noDataChart( id, name ) {
	return new Highcharts.Chart({
		chart:    { renderTo: 'chart-'+id },
		credits:  { enabled: false },
		title:    { text: name },
		subtitle: {
			text: '{{NoDataAvailable}}',
			style: { color: '#FF0000' }
		}
	});
}

/**
 *
 */
function updateClock() {
	$('#date-time').html((new Date).toLocaleString());
	setTimeout(updateClock, 1000);
}

/**
 *
 */
function updateCharts() {

	clearTimeout(timeout);

	var date = new Date;

	$('input.channel:checked').each(function(chart_id, el) {

		chart_id++;
		el = $(el);

		var t, url = PVLngAPI + 'data/' + el.data('guid') + '.json';
		_log('Fetch: '+url);

		$.getJSON(
			url,
			{
			    attributes: true,
			    full:       true,
				period:     'last',
				t:          date.getTime()
			},
			function(data) {
				/* pop out 1st row with attributes */
				var	attr = data.shift();

				_log('Attributes:', attr);
				_log('Data:', data);

				if (!data[0] || (date.getTime()/1000 - data[0].timestamp) > 600) {
					/* NO data row found or data older than 10 minuts */
					chartsNoData[chart_id] = true;
				    charts[chart_id] = noDataChart(chart_id, attr.name);
				} else {
					if (charts[chart_id] == undefined || chartsNoData[chart_id]) {
						chartsNoData[chart_id] = false;
						$('#chart-'+chart_id).empty();
					    var options = $.extend({}, chartOptions, {
							chart:    { renderTo: 'chart-'+chart_id, type: 'gauge' },
							title:    { text: attr.name },
							subtitle: { text: attr.description ? $('<div/>').html(attr.description).text() : ' ' },
							yAxis: {
								min: attr.valid_from,
								max: attr.valid_to,
								title: { text: attr.unit },
								plotBands: []
							},
							series: [ { name: attr.name } ]
						});

						if (attr.comment) {
							/* draw colored plot bands
							   <from> > <to> : <color>
							   > <to> : <color>
							   <from> > : <color>
							   missing <from> and <to> are replaced
							   by valid_from and valid_to

							/* split into bands */
							var bands = attr.comment.split("\n");

							$(bands).each(function(id, band) {
								/* split into from-to and color */
								var fromto_color = band.trim().split(':');

								/* split from and to */
								var fromto = fromto_color[0].trim().split('>');

								if (fromto[0] == '') {
									fromto[0] = attr.valid_from;
								} else if (fromto[0].indexOf('%') != -1) {
								    fromto[0] = fromto[0].replace('%', '');
								    fromto[0] = attr.valid_from + (attr.valid_to - attr.valid_from) * fromto[0] / 100;
								}

								if (fromto[1] == '') {
									fromto[1] = attr.valid_to;
								} else if (fromto[1].indexOf('%') != -1) {
								    fromto[1] = fromto[1].replace('%', '');
								    fromto[1] = attr.valid_from + (attr.valid_to - attr.valid_from) * fromto[1] / 100;
								}

								options.yAxis.plotBands.push({
									from:  +fromto[0],
									to:    +fromto[1],
									color: fromto_color[1]
								});
							});
						}

						charts[chart_id] = new Highcharts.Chart(options);
					}

					charts[chart_id].series[0].setData([
						+Highcharts.numberFormat(data[0].data, attr.decimals, '.', '')
					]);
				}
			}
		);
	});

	timeout = setTimeout(updateCharts, 60 * 1000);
}

/**
 *
 */
$(function() {

	updateClock();

	<!-- IF {USER} -->
	$.ajaxSetup({
		beforeSend: function setHeader(xhr) {
			xhr.setRequestHeader('X-PVLng-Key', '{APIKEY}');
		}
	});
	<!-- ENDIF -->

	Highcharts.setOptions({
		lang: {
			thousandsSep: '{TSEP}',
			decimalPoint: '{DSEP}'
		}
	});

	updateCharts();

	$('#tree').DataTable({
		bPaginate: false,
		bLengthChange: false,
		bFilter: false,
		bSort: false,
		bInfo: false,
		bJQueryUI: true
	});

	$('.treeTable').treetable({
		initialState: 'expanded',
		indent: 24,
		column: 1
	});

	/* Remember left padding of indenter */
	$('span.indenter').each(function(id, el) {
		el = $(el);
		el.data('indent', el.css('padding-left'));
	});

	$('input.iCheck').iCheck('update');

	$('#btn-refresh').button({
		icons: {
			primary: 'ui-icon-refresh'
		},
		text: false
	});

	$('#togglewrapper').click(function() {
		var visible = ! $('#wrapper').is(':visible');
			var link = $(this);

		$('#wrapper').animate(
			{ height: 'toggle', opacity: 'toggle' }, 'slow', 'linear',
			function() {
				if (visible) {
					$('#wrapper').css('visibility', 'visible');
					link.button({ icons: { primary:'ui-icon-carat-1-n' }, text:false });
				} else {
					$('#wrapper').css('visibility', 'hidden');
					link.button({ icons: { primary:'ui-icon-carat-1-s' }, text:false });
				}
			}
		);

		return false;
	}).button({ icons: { primary: 'ui-icon-carat-1-n' }, text: false });

	if ('{CHANNELCOUNT}' != 0) {
		ToggleTree(false);
		$('#togglewrapper').trigger('click');
	}
});

</script>
