<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */

/**
Chart canvas height
Width is 940px,
- Ratio  5 x  4 : 752
- Ratio  4 x  3 : 705
- Ratio 16 x 10 : 587
- Ratio 10 x  6 : 564
- Ratio 16 x  9 : 528
 */

var ChartHeight = 564;

/* refresh timeout in sec., set 0 for no automatic refresh */
var RefreshTimeout = 300;

/* ------------------------------------------------------------------------ */
</script>

<script src="/js/chart.js"></script>
<script src="/js/jquery.treetable.js"></script>
<script src="/js/spectrum.js"></script>
<!--
<script src="/js/palettes.js"></script>
-->

<!-- load Highcharts scripts direct from highcharts.com -->
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<script>

var
	chart, timeout,

	options = {
		chart: {
			renderTo: 'chart',
			height: $.parseQueryString().ChartHeight || ChartHeight,
			paddingRight: 15,
			alignTicks: false,
			zoomType: 'x',
			events: {
				selection: function(event) {
					setTimeout(setExtremes, 100);
				}
			}
		},
		title: { text: '' },
		plotOptions: {
			line: {
				marker: { enabled: false }
			},
			spline: {
				marker: { enabled: false },
			},
			areaspline: {
				marker: { enabled: false },
				shadow: false,
				fillOpacity: 0.2,
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
			type: 'datetime'
		},
		tooltip: {
			useHTML: true,
			formatter: function() {
				var body = '', value;
				$.each(this.points, function(id, point) {
					if (point.point.low != undefined && point.point.high != undefined) {
						value = Highcharts.numberFormat(+point.point.low, point.series.options.decimals) + ' - ' +
						        Highcharts.numberFormat(+point.point.high, point.series.options.decimals);
					} else if (point.y != undefined) {
						value = Highcharts.numberFormat(+point.y, point.series.options.decimals);
					} else {
						return;
					}
					body += '<tr style="color:' + point.series.color + '"';
					if (id & 1) body += ' class="even"'; /* id starts by 0 */
					body += '>' +
					        '<td class="name">' + point.series.name + '</td>' +
					        '<td class="value">' + value + '</td>' +
					        '<td class="unit"> ' + point.series.tooltipOptions.valueSuffix + '</td>' +
							'</tr>';
				});
				return '<table id="chart-tooltip"><thead><tr><th colspan="3">' +
				       Highcharts.dateFormat('%a. %Y-%m-%d %H:%M',this.x).replace(' 00:00', '') +
				       '</th></tr></thead><tbody>' + body + '</tbody></table>';
			},
			borderWidth: 0,
			shadow: true,
			crosshairs: true,
			shared: true
		},
		loading: {
			labelStyle: {
				top: '40%',
				fontSize: '200%'
			}
		},

		exporting: {
			buttons: {
				contextButton: {
					menuItems: [{
						text: 'Export to PNG (small)',
						onclick: function() {
							this.exportChart({
								width: 250
							});
						}
					}, {
						separator: true
					}, {
						text: 'Export to PNG (large)',
						onclick: function() {
							this.exportChart();
						},
						separator: false
					}]
				}
			}
		}
	};

/**
 *
 */
function changeDates( dir ) {
	var from = Date.parse($('#from').datepicker('getDate')) + dir*24*60*60*1000;
	var to = Date.parse($('#to').datepicker('getDate')) + dir*24*60*60*1000;
	$('#from').datepicker( 'option', 'maxDate', 0 );
	$('#to').datepicker( 'option', 'maxDate', 0 );
	if (dir < 0) {
		/* backwards */
		$('#from').datepicker('setDate', new Date(from));
		$('#to').datepicker('setDate', new Date(to));
	} else {
		/* foreward */
		$('#to').datepicker('setDate', new Date(to));
		$('#from').datepicker('setDate', new Date(from));
	}
	updateChart();
}

/**
*
*/
var TreeExpanded = true;

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
			el.css('padding-left', el.data('indent')).css('width', '19px');
		});
	} else {
		$('#treetoggle').attr('src','/images/ico/toggle_expand.png').attr('alt','[+]');
		$('#tiptoggle').html('{{ExpandAll}}');

		$('span.indenter').each(function(id, el) {
			/* Remove left indent */
			$(el).css('padding-left', 0).css('width', '8px');
		});
	}

    $('#tree tbody tr:visible').each(function(id, el) {
		el = $(el);
		if (id & 1) {
			/* Set to odd if needed */
			if (el.hasClass('even')) el.removeClass('even').addClass('odd');
		} else {
			/* Set to even if needed */
			if (el.hasClass('odd'))  el.removeClass('odd').addClass('even');
		}
	});
}

/**
 *
 */
function ChartDialog( id, name ) {
	/* get stringified settings */
	var p = new presentation($('#c'+id).val());
	/* set dialog properties */
	/* find the radio button with the axis value and check it */
	$('input[name="d-axis"][value="' + p.axis + '"]').prop('checked', true);
	$('#d-type').val(p.type);
	$('#d-cons').prop('checked', p.consumption);
	$('input[name="d-width"][value="' + p.width + '"]').prop('checked', true);
	$('#d-min').prop('checked', p.min);
	$('#d-max').prop('checked', p.max);
	$('#d-style').val(p.style);
	$('#d-color').val(p.color);
	$('#d-color').spectrum('set', p.color);
	$('#d-color-use-neg').prop('checked', p.coloruseneg);
	$('#d-color-neg').val(p.colorneg);
	$('#d-color-neg').spectrum('set', p.colorneg);
	$('#d-color-neg').spectrum($('#d-color-use-neg').is(':checked') ? 'enable' : 'disable');
	$('#d-threshold').val(p.threshold);
	$('#d-threshold').prop('disabled', !$('#d-color-use-neg').is(':checked'));
	$('input').iCheck('update');
	/* set the id into the dialog for onClose to write data back */
	$('#dialog-chart').data('id', id);
	$('#dialog-chart').dialog('option', 'title', name);
	$('#dialog-chart').dialog('open');
}

/**
 *
 */
var channels = [];

/**
 * Scale timestamps down to full hour, day, week, month, quarter or year
 */
var xAxisResolution = {
	h: 3600,
	d: 3600 * 24,
	w: 3600 * 24 * 7,
	m: 3600 * 24 * 30,
	q: 3600 * 24 * 90,
	y: 3600 * 24 * 360,
};

var lastChanged = (new Date).getTime() / 1000 / 60;

/**
 *
 */
function updateChart() {

	clearTimeout(timeout);

	<!-- IF {USER} AND {EMBEDDED} != "2" -->
	/* Provide permanent link only for logged in user and not embedded view level 2 */
	var btn = $('#btn-permanent'), date = $('#from').val(), to = $('#to').val();
	if (date != to) date += ' - ' + to;
	var text = btn.data('text').replace('&', date);

	btn.prop('href', btn.data('url') + encodeURI('?from='+$('#fromdate').val()+'&to='+$('#todate').val()))
	   .prop('title', text).html(text);

	/* Rebuild button after text and title changes */
	btn.button({ icons: { primary: 'ui-icon-image' }, text: false });
	<!-- ENDIF -->

	var ts = (new Date).getTime(),
	    channels_new = [], yAxisMap = [], yAxis = [],
		channel, channel_clone, buffer = [],
		period_count = +$('#periodcnt').val(),
		period = $('#period').val();

	/* reset consumption and costs data */
	$('.minmax, .consumption, .costs, #costs').each(function(id, el) {
		$(el).html('');
	});

	/* find active channels, map and sort axis */
	$('input.channel:checked').each(function(id, el) {
		channel = new presentation($(el).val());
		channel.id = $(el).data('id');
		channel.name = $('<div/>').html($(el).data('name')).text()
		channel.guid = $(el).data('guid');
		channel.unit = $(el).data('unit');
		/* remember channel */
		buffer.push(channel);
		/* still mapped? */
		if (yAxisMap.indexOf(channel.axis) == -1) yAxisMap.push(channel.axis);
	});

	/* sort axis to make correct order for Highcharts */
	yAxisMap.sort();

	/* build channels */
	$(buffer).each(function(id, channel) {
		/* axis on right side */
		var is_right = !(channel.axis % 2);

		/* axis from chart point of view */
		channel.axis = yAxisMap.indexOf(channel.axis);

		if (channel.type == 'areasplinerange') {
			/* handling area splines */
			if (period == '') {
				/* no period => show spline */
				channel.type = 'spline';
				channels_new.push(channel);
			} else {
				/* period, add channel and ... */
				channels_new.push(channel);
				/* ... add 2nd spline channel! */
				var channel_clone = $.extend({}, channel);
				channel_clone.linkedTo = ':previous';
				channel_clone.type = 'spline';
				channels_new.push(channel_clone);
			}
		} else {
			channels_new.push(channel);
		}

		/* prepare axis */
		if (!yAxis[channel.axis]) {
			yAxis[channel.axis] = {
				/* unit as axis title */
				title: { text: channel.unit },
				lineColor: channel.color,
				showEmpty: false,
				minPadding: 0,
				maxPadding: 0,
				opposite: is_right
			};
			/* only 1st left axis shows grid lines */
			if (channel.axis != 0) {
				yAxis[channel.axis].gridLineWidth = 0;
			}
		}
	});

	if (yAxis.length > 1) {
		$(yAxis).each(function(id) {
			yAxis[id].startOnTick = false;
			yAxis[id].endOnTick = false;
		});
	}

	_log('Channels:', channels_new);
	_log('yAxis:', yAxis);

	/* check for changed channels */
	var changed = false, now = (new Date).getTime() / 1000 / 60;

	/* renew chart at least each half hour to auto adjust axis ranges by Highcharts */
	if (channels_new.length != channels.length || now - lastChanged > 30) {
		changed = true;
		channels = channels_new;
		lastChanged = now;
	} else {
		for (var i=0, l=channels_new.length; i<l; i++) {
			if (JSON.stringify(channels_new[i]) != JSON.stringify(channels[i])) {
				changed = true;
				channels = channels_new;
			}
		}
	}

	var res = xAxisResolution[period];

	if (period_count < 1) {
		switch(period) {
			case 'h':  res = null;  break;
			case 'd':  res = xAxisResolution['h'];  break;
			case 'w':  res = xAxisResolution['d'];  break;
			case 'm':  res = xAxisResolution['w'];  break;
			case 'q':  res = xAxisResolution['m'];  break;
			case 'y':  res = xAxisResolution['q'];  break;
		}
	}

	if (changed) {
		/* use UTC for timestamps with a period >= day to avoid wrong hours in hint */
		Highcharts.setOptions({	global: { useUTC: (res >= xAxisResolution.d) } });

		/* happens also on 1st call! */
		options.yAxis = yAxis;
		/* (re)create chart */
		chart = new Highcharts.Chart(options);
	}

	var loading = channels.length;
	chart.showLoading('- ' + loading + ' -');

	var series = [], costs = 0;

	/* get data */
	$(channels).each(function(id, channel) {

		$('#s'+channel.id).show();

		var t, url = PVLngAPI + 'data/' + channel.guid + '.json';
		_log('Fetch', url);

		$.getJSON(
			url,
			{
			    attributes: true,
			    full:       true,
				start:	    $('#fromdate').val(),
				end:        $('#todate').val() + '+1day',
				period:     (channel.type != 'scatter') ? period_count + period : '',
				_ts:        (new Date).getTime()
			},
			function(data) {
				/* pop out 1st row with attributes */
				attr = data.shift();
				_log('Attributes', attr);
				_log('Data', data);

				if (attr.consumption) {
					$('#cons'+channel.id).html(Highcharts.numberFormat(attr.consumption, attr.decimals));
				}

				if (attr.costs) {
					costs += +attr.costs.toFixed({CURRENCYDECIMALS});
					$('#costs'+channel.id).html(Highcharts.numberFormat(attr.costs, {CURRENCYDECIMALS}));
				}

				t = (attr.description) ? ' (' + attr.description + ')' : '';

				var serie = { /* HTML decode channel name */
					name: $('<div/>').html(attr.name + t).text(),
				    id: channel.id,
				    decimals: attr.decimals,
					unit: attr.unit,
					color: channel.color,
					type: channel.type,
					yAxis: channel.axis,
					data: []
				};

				if (channel.coloruseneg) {
					serie.threshold = channel.threshold;
					serie.negativeColor = channel.colorneg;
				}

				if (channel.linkedTo != undefined) serie.linkedTo = channel.linkedTo;
				if (attr.unit) serie.tooltip = { valueSuffix: attr.unit };

				if (channel.type == 'scatter') {
					serie.dataLabels = {
						enabled: true,
						formatter: function() {
							/* Switch for non-numeric / numeric channels */
							return this.point.name ? this.point.name : this.point.y;
						}
					};
					if (!attr.unit) {
						/* mostly non-numeric channels */
						serie.dataLabels.align = 'left';
						serie.dataLabels.rotation = 270;
						serie.dataLabels.x = 3;
						serie.dataLabels.y = -7;
					}
				} else if (channel.type != 'bar') {
					serie.dashStyle = channel.style;
					serie.lineWidth = channel.width;
				}

				$(data).each(function(id, row) {
					var ts = res
					       ? Math.round(row.timestamp / res) * res * 1000
					       : row.timestamp * 1000;
					if ($.isNumeric(row.data)) {
						if (channel.type == 'areasplinerange') {
							serie.data.push([ts, row.min, row.max]);
						} else if (channel.consumption) {
							serie.data.push([ts, row.consumption]);
						} else {
							serie.data.push([ts, row.data]);
						}
					} else {
						serie.data.push({
							x: row.timestamp*1000,
							y: 0,
							name: row.data
						});
					}
				});

				if (channel.type != 'areasplinerange' && (channel.min || channel.max)) {
					serie = setMinMax(serie, channel);
				}

				_log('Serie', serie);

				series[id] = serie;

				if ('{INDEX_NOTIFYLOAD}') $.pnotify({
					type: 'success',
					text: attr.name + ' loaded'
				});
			}
		).fail(function(jqxhr, textStatus, error) {
		    _log('FAIL', textStatus + ', ' + error);

			$.pnotify({
				type: jqxhr.responseJSON.status,
				text: jqxhr.responseJSON.message,
				hide: false,
				sticker: false
			});

			/* Set pseudo channel */
			series[id] = {};

		}).always(function(data, status) {

			$('#s'+channel.id).hide();

			/* Force redraw */
			chart.hideLoading();
			chart.showLoading('- ' + (--loading) + ' -');

			/* check real count of elements in series array! */
			var completed = series.filter(function(a){ return a !== undefined }).length;
			_log(completed+' series completed');

			/* check if all getJSON() calls finished */
			if (completed != channels.length) return;

			$.pnotify({
				type: 'success',
				text: completed + ' channels loaded ' +
				      '(' + (((new Date).getTime() - ts)/1000).toFixed(1) + 's)'
			});
			$('#costs').html(costs ? Highcharts.numberFormat(costs, {CURRENCYDECIMALS}) : '');
			var t = $('#from').val();
			var s = $('#to').val();
			if (t != s) t += ' - ' + s;
			chart.setTitle({ text: $('#loaddeleteview').val() }, { text: t });

			_log('Apply series');

			if (changed) {
				/* remove all existing series */
				while (chart.series.length) {
					chart.series[0].remove();
				}
				/* add new series */
				$.each(series, function(i, serie) {
					if (serie.id) {
						/* Valid channel with id */
						chart.addSeries(serie, false);
					}
				});
			} else {
				/* replace series data */
				var sid = 0;
				$.each(series, function(i, serie) {
					if (serie.id) {
						/* Valid channel with id */
						chart.series[sid++].setData(serie.data, false);
					}
				});
			}

			chart.hideLoading();
			chart.redraw();

			resizeChart();
			setExtremes();

			if (RefreshTimeout > 0) {
				timeout = setTimeout(updateChart, RefreshTimeout*1000);
			}

		});
	});
}

var resizeTimeout;

/**
 *
 */
function resizeChart() {
	clearTimeout(resizeTimeout);
	/* Resize chart correct into parent container */
	var c = $('#chart')[0];
	chart.setSize(c.offsetWidth, c.offsetHeight);
}

/**
 *
 */
$(function() {

	$(window).resize(function() {
		resizeTimeout = setTimeout(resizeChart, 500);
	});

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

	$("#dialog-chart").dialog({
		autoOpen: false,
		width: 750,
		modal: true,
		buttons: {
			'{{Ok}}': function() {
				p = new presentation();
				p.axis = +$('input[name="d-axis"]:checked').val();
				p.type = $('#d-type').val();
				p.consumption = $('#d-cons').is(':checked');
				p.style = $('#d-style').val();
				p.width = +$('input[name="d-width"]:checked').val();
				p.min = $('#d-min').is(':checked');
				p.max = $('#d-max').is(':checked');
				p.color = $('#d-color').spectrum('get').toHexString();
				p.coloruseneg = $('#d-color-use-neg').is(':checked');
				p.colorneg = $('#d-color-neg').spectrum('get').toHexString();
				p.threshold = +$('#d-threshold').val().replace(',', '.');
				$('#c'+$(this).data('id')).val(p.toString());
				$(this).dialog('close');
			},
			'{{Cancel}}': function() {
				$(this).dialog('close');
			}
		}
	});

	$('.spectrum').spectrum({
		showPalette: true,
/*
		showPaletteOnly: true,
		localStorageKey: 'pvlng.channel.color',
*/
		palette: [
			['#404040', '#4572A7'],
			['#AA4643', '#89A54E'],
			['#80699B', '#3D96AE'],
			['#DB843D', '#92A8CD'],
			['#A47D7C', '#B5CA92']
		],
		showInitial: true,
		showInput: false,
		showButtons: false,
		preferredFormat: 'hex',
		hide: function(color) { color.toHexString(); }
	});

	$('#d-color-use-neg').on('ifToggled', function(e) {
		var checked = $(this).is(':checked');
		$('#d-threshold').prop('disabled', !checked);
		$('#d-color-neg').spectrum(checked ? 'enable' : 'disable');
	});

	<!-- IF {LANGUAGE} != "en" -->
	$.datepicker.setDefaults($.datepicker.regional['{LANGUAGE}']);
	<!-- ENDIF -->

	$("#from").datepicker({
		altField: '#fromdate',
		altFormat: 'mm/dd/yy',
		maxDate: 0,
		showButtonPanel: true,
		showWeek: true,
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ) {
			$("#to").datepicker( "option", "minDate", selectedDate );
			$("#to").datepicker( "option", "maxDate", 0 );
		}
	});

	$("#to").datepicker({
		altField: '#todate',
		altFormat: 'mm/dd/yy',
		maxDate: 0,
		showButtonPanel: true,
		showWeek: true,
		changeMonth: true,
		changeYear: true,
		onClose: function( selectedDate ) {
			$("#from").datepicker( "option", "maxDate", selectedDate );
		}
	});

	var ts = $.parseQueryString(), d = new Date();

	if (ts.date) {
		ts.from = ts.date;
		ts.to = ts.date;
	}
	$("#from").datepicker('setDate', ts.from ? new Date(ts.from) : d);
	$("#to").datepicker('setDate', ts.to ? new Date(ts.to) : d);

	Highcharts.setOptions({
		global: {
			alignTicks: false
		},
		lang: {
			thousandsSep: '{TSEP}',
			decimalPoint: '{DSEP}',
			resetZoom: '{{resetZoom}}',
			resetZoomTitle: '{{resetZoomTitle}}'
		}
	});

	/* Remember left padding of indenter */
	$('span.indenter').each(function(id, el) {
		el = $(el);
		el.data('indent', el.css('padding-left'));
	});

	<!-- IF {USER} -->
	$.ajaxSetup({
		beforeSend: function setHeader(xhr) {
			xhr.setRequestHeader('X-PVLng-Key', '{APIKEY}');
		}
	});
	<!-- ENDIF -->

	if ($('#loaddeleteview').val()) {
		ToggleTree(false);
		updateChart();
	}

	$('#d-type').change(function() {
		$('#d-style').prop('disabled', (this.value == 'bar' || this.value == 'scatter'));
	});

	$('input').iCheck('update');

	$('#btn-reset').button({
		icons: {
			primary: 'ui-icon-calendar'
		},
		text: false
	}).click(function(e) {
		var d = new Date;
		/* Reset max. date today */
		$('#from').datepicker( 'option', 'maxDate', d );
		$('#to').datepicker( 'option', 'maxDate', d );
		/* Set max. date today */
		$('#from').datepicker('setDate', d);
		$('#to').datepicker('setDate', d);
		updateChart();
		return false;
	});

	$('#btn-refresh').button({
		icons: {
			primary: 'ui-icon-refresh'
		},
		text: false
	}).click(function(e) {
		updateChart();
		return false;
	});

	$('#btn-permanent').button({
		icons: {
			primary: 'ui-icon-image'
		},
		text: false,
		disabled: ('{VIEW}' == '')
	});

	$('#btn-bookmark').button({
		icons: {
			primary: 'ui-icon-bookmark'
		},
		text: false,
		disabled: ('{VIEW}' == '')
	});

	$('#loaddeleteview').change(function() {
		var el = $('#btn-bookmark');
		el.button({
			label: 'PVLng | ' + this.value,
			disabled: (this.value == '')
		}).prop('href', el.data('url') + encodeURIComponent(this.value));
	});

	$('#togglewrapper').button({
		icons: {
			primary: 'ui-icon-carat-1-n'
		},
		text: false
	}).click(function() {
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
	});

	$('#delete-view').click(function() {
		/* Replace text, make bold red and unbind this click handler */
		$(this).val("{{Sure}}?").css('fontWeight','bold').css('color','red').unbind();
		return false;
	});

});

</script>
