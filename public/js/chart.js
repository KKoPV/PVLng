/**
 *
 */
var defaults = {
	/* Chart hint decimals */
	HintDecimals: 2,

	line: {
		/* normal line width */
		normal: 2,
		/* bold line width */
		bold: 4,
		/* line type */
		type: 'spline',
		/* line color */
		color: '#404040'
	}
};

/**
 *
 */
function presentation( data ) {
	/* set defaults */
	this.axis = 1;
	this.type = defaults.line.type;
	this.consumption = false;
	this.style = 'Solid';
	this.color = defaults.line.color;
	this.bold  = false;
	this.min   = false;
	this.max   = false;

	try { $.extend(this, JSON.parse(data));	} catch(e) {}

	this.toString = function() {
		return JSON.stringify(this);
	}
}

/**
 *
 */
function setExtremes() {
	$(chart.yAxis).each(function(id, axis) {
		var extremes = axis.getExtremes();
		if ($('#az').is(':checked')) {
			if (extremes.dataMin >= 0) axis.setExtremes(0);
		} else {
			/* reset axis ... */
			axis.setExtremes(null);
			/* ... and re-read */
			extremes = axis.getExtremes();
			if (extremes.min < 0 && extremes.dataMin >= 0) {
				axis.setExtremes(0);
			}
		}
	});
}

/**
 *
 */
function setMinMax( serie, channel ) {

	var
		ts  = { min: Number.MAX_VALUE, max: -Number.MAX_VALUE },
		min = { id: null, x: null, y:  Number.MAX_VALUE },
		max = { id: null, x: null, y: -Number.MAX_VALUE };

	/* search min. and max. values */
	$.each(serie.data, function(i, point) {
		ts.min = Math.min(ts.min, point[0]);
		ts.max = Math.max(ts.max, point[0]);

		if (channel.min && (point[1] < min.y)) {
			min = { id: i, x: point[0], y: point[1] }
		} else if (channel.max && (point[1] > max.y)) {
			max = { id: i, x: point[0], y: point[1] }
		}
	});

	if (min.id != null) {

		var left = ((ts.min + (ts.max - ts.min)/2 - min.x) > 0);

		serie.data[min.id] = {
			marker: {
				enabled: true,
				symbol: 'triangle',
				fillColor: serie.color
			},
			dataLabels: {
				enabled: true,
				formatter: function() {
					return Highcharts.numberFormat(+this.y, this.series.options.decimals)
				},
				color: serie.color,
				style: { fontWeight: 'bold' },
				align: left ? 'left' : 'right',
				y: 26,
				borderRadius: 3,
				backgroundColor: 'rgba(252, 255, 197, 0.7)',
				borderWidth: 1,
				borderColor: '#AAA'
			},
			x: min.x,
			y: min.y
		};

		$('#min'+serie.id).html(Highcharts.numberFormat(min.y, serie.decimals) + ' ' + serie.unit);
	}

	if (max.id != null) {

		var left = ((ts.min + (ts.max - ts.min)/2 - max.x) > 0);

		serie.data[max.id] = {
			marker: {
				enabled: true,
				symbol: 'triangle-down',
				fillColor: serie.color
			},
			dataLabels: {
				enabled: true,
				formatter: function() {
					return Highcharts.numberFormat(+this.y, this.series.options.decimals)
				},
				color: serie.color,
				style: { fontWeight: 'bold' },
				align: left ? 'left' : 'right',
				y: -7,
				borderRadius: 3,
				backgroundColor: 'rgba(252, 255, 197, 0.7)',
				borderWidth: 1,
				borderColor: '#AAA'
			},
			x: max.x,
			y: max.y
		};

		$('#max'+serie.id).html(Highcharts.numberFormat(max.y, serie.decimals) + ' ' + serie.unit);
	}

	return serie;
}
