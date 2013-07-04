/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

/* ------------------------------------------------------------------------ */

verbose = true;

var	Period = '{MOBILE_PERIOD}';

var ChartHeight = '{MOBILE_CHARTHEIGHT}';

/* ------------------------------------------------------------------------ */
</script>

<script src="/js/chart.js"></script>

<!-- load Highcharts scripts direct from highcharts.com -->
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>

<!--
<script src="/js/highcharts.js"></script>
<script src="/js/highcharts-more.js"></script>
<script src="/js/highcharts-exporting.js"></script>
-->

<script>

var views = {
		<!-- BEGIN VIEWS -->
		"{NAME}": '{DATA}',
		<!-- END -->
	},

	options = {
		chart: {
			renderTo: 'chart',
			height: ChartHeight,
			spacingRight: 15,
			spacingTop: 15,
			spacingBottom: 5,
			alignTicks: false
		},
		credits: { enabled: false },
		title: { text: '' },
		plotOptions: {
			line: {
				marker: { enabled: false }
			},
			spline: {
				marker: { enabled: false }
			},
			areaspline: {
				marker: { enabled: false },
				shadow: false,
				fillOpacity: 0.2
			},
			areasplinerange: {
				marker: { enabled: false },
				shadow: false,
				fillOpacity: 0.2
			},
			bar: {
				groupPadding: 0.1
			}
		},
		xAxis : {
			type: 'datetime',
			/*
			title: { text: '' },
			labels: false,
			lineWidth: 0,
			maxZoom: 3600 * 1000 /* 1 hour */
		},
		legend: {
			enabled: false
		},
		tooltip: {
			enabled: false
		},
		loading: {
		    hideDuration: 0,
			showDuration: 0,
			labelStyle: {
				top: '40%',
				fontSize: '200%',
				color: 'black'
			}
		}
	};

Highcharts.setOptions({
	global: {
		useUTC: false,
		alignTicks: false
	},
	lang: {
		thousandsSep: '{TSEP}',
		decimalPoint: '{DSEP}',
		resetZoom: '{{resetZoom}}',
		resetZoomTitle: '{{resetZoomTitle}}'
	}
});

/**
 *
 */
var lock = false,
	channels = [],
	loading = '•';

var chart = new Highcharts.Chart(options);

chart.showLoading(loading);

$('#page-home').on('pageshow', function( event, ui ) {
	updateChart();
});

/**
 *
 */
function updateChart() {

	if (lock) return;
	lock = true;

	var view = $('#page-home').data('view');

	$('#view').html(view);

	view = views[view];

	try {
		view = JSON.parse(view);
	} catch (e) {
	    return
	}

	if (view == '') return;

	loading = '•' + '•'.repeat(view.length);
	chart.showLoading(loading);

	$('#table-cons tbody tr').remove();

	var channels_new = [], yAxisMap = [], yAxis = [],
		channel, buffer = [],
		channel_clone;

	/* find active channels, map and sort axis */
	$(view).each(function(id, view) {
		channel = new presentation(view.presentation);
		channel.id = view.id;
		channel.guid = view.guid;
		channel.unit = view.unit;
		/* remember channel */
		buffer.push(channel);
		/* still mapped? */
		if (yAxisMap.indexOf(channel.axis) == -1) yAxisMap.push(channel.axis);
	});

	/* sort axis to make correct order for Highcharts */
	yAxisMap.sort();

	/* build channels */
	$(buffer).each(function(id, channel) {
		/* axis from chart point of view */
		var opposite = !(channel.axis & 1);
		channel.axis = yAxisMap.indexOf(channel.axis);

		if (channel.type == 'areasplinerange') {
			channel.type = 'spline';
		}
		channels_new.push(channel);

		/* prepare axis */
		if (!yAxis[channel.axis]) {
			yAxis[channel.axis] = {
				title: { text: '' },
				labels: false,
				lineWidth: 0,
				/* unit as axis title * /
				title: { text: channel.unit },
				showEmpty: false,
				/* odd axis on left, even on right side */
				opposite: opposite
			};
			/* only 1st left axis shows grid lines */
			if (channel.axis != 0) {
				yAxis[channel.axis].gridLineWidth = 0;
			}
		}
	});

	_log('Channels:', channels_new);
	_log('yAxis:', yAxis);

	/* check for changed channels */
	var changed = false;

	if (channels_new.length != channels.length) {
		changed = true;
		channels = channels_new;
	} else {
		for (var i=0, l=channels_new.length; i<l; i++) {
			if (JSON.stringify(channels_new[i]) != JSON.stringify(channels[i])) {
				changed = true;
				channels = channels_new;
			}
		}
	}

	if (changed) {
		/* happens also on 1st call! */
		options.yAxis = yAxis;
		/* (re)create chart */
		chart = new Highcharts.Chart(options);
	}

	var series = [], costs = 0;

	/* get data */
	$(channels).each(function(id, channel) {

		$('#s'+channel.id).show();

		var url = PVLngAPI + channel.guid + '.json';
		_log('Fetch: '+url);

		$.getJSON(
			url,
			{
			    period: (channel.type != 'scatter') ? Period : '',
				mobile: true,
				_ts: (new Date).getTime()
			},
			function(data) {
				/* pop out 1st row with attributes */
				attr = data.shift();
				_log('Attributes:', attr);
				_log('Data: ', data);

				var tr;

				if (attr.consumption) {
				    tr = $('<tr/>');
				    tr.append($('<th/>')
					  .html(attr.name));
					tr.append($('<td/>')
					  .addClass('r')
					  .html(Highcharts.numberFormat(attr.consumption, 2)));
				}

				if (attr.costs) {
					costs += +attr.costs.toFixed(2);
					tr.append($('<td/>')
					  .addClass('cost')
					  .html(Highcharts.numberFormat(attr.costs, 2)));
				}

				if (tr) $('#table-cons tbody').append(tr);

				var serie = { /* HTML decode channel name */
					name:	$('<div/>').html(attr.name).text(),
					color:	channel.color,
					type:	channel.type,
					yAxis:	channel.axis,
					data:	[]
				};

				if (channel.linkedTo != undefined) serie.linkedTo = channel.linkedTo;
				if (attr.unit) serie.tooltip = { valueSuffix: attr.unit };

				if (channel.type == 'scatter') {
					serie.dataLabels = {
						enabled: true,
						align: 'left',
						rotation: 270,
						align: 'left',
						x: 4,
						y: -7,
						formatter: function() { return this.point.name }
					};
				} else if (channel.type != 'bar') {
					serie.dashStyle = channel.style;
					serie.lineWidth = channel.bold
					                ? defaults.line.bold
					                : defaults.line.normal;
				}

				$(data).each(function(id, row) {
					if ($.isNumeric(row.d)) {
						if (channel.type == 'areasplinerange') {
							serie.data.push([row.t*1000, row.i, row.a]);
						} else {
							serie.data.push([row.t*1000, row.d]);
						}
					} else {
						serie.data.push({
							x: row.t*1000,
							y: 0,
							name: row.d
						});
					}
				});

				if (channel.type != 'areasplinerange' && (channel.min || channel.max)) {
					serie = setMinMax(serie, channel.min, channel.max);
				}

				_log('Serie: ', serie);

				series[id] = serie;

				$('#s'+channel.id).hide();
			}
		).always(function() {
			/* check real count of elements in series array! */
			var completed = series.filter(function(a){ return a !== undefined }).length;
			_log(completed+' completed');

			loading = loading.substring(0, loading.length-1);
			chart.showLoading(loading);

			/* check if all getJSON() calls finished */
			if (completed == channels.length) {
				costs = costs ? Highcharts.numberFormat(costs, 2) : false;
				if (costs) {
					$('#table-cons tbody').append(
						$('<tr/>')
						.append($('<td colspan="3" />')
						.html(costs)
						.addClass('costs'))
					);
				}
				var t = $('#from').val();
				var s = $('#to').val();
				if (t != s) t += ' - ' + s;
				/* chart.setTitle({ text: t }, { text: $('#view-choice').val() }); */

				_log('Apply series');

				if (changed) {
					/* remove all existing series */
					while (chart.series.length) {
						chart.series[0].remove();
					}
					/* add new series */
					$.each(series, function(i, serie) {
						chart.addSeries(serie, false);
					});
				} else {
					/* replace series data */
					$.each(series, function(i, serie) {
						chart.series[i].setData(serie.data, false);
					});
				}

				chart.redraw();
				chart.hideLoading();
				setTimeout(setExtremes, channels.length*100);
				lock = false;
			}
		});
	});
}
