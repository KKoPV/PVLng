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

	if (chart.yAxis.length == 2) {return;
		var i = 15;
		while (chart.yAxis[1].translate(0) != chart.yAxis[0].translate(0) && i > 0) {
			chart.yAxis[0].setExtremes(chart.yAxis[0].getExtremes().min -
																 chart.yAxis[0].translate(chart.yAxis[1].translate(0), true));
			i--;
			/* console.log(chart.yAxis[1].translate(0), chart.yAxis[0].translate(0), i); */
		}
	}
}

/**
 *
 */
function trimDecimal( value ) {
	var factor;

	switch (true) {
		case (Math.abs(value) <	 10):	factor = 1000;	break;
		case (Math.abs(value) <	100):	factor =  100;	break;
		case (Math.abs(value) < 1000):	factor =   10;	break;
		default:                        factor =    1;	break;
	}

	return Math.round(value * factor) / factor;
}

/**
 *
 */
function setMinMax( serie, setMin, setMax ) {

	var
		min = { id: null, x: null, y:	Number.MAX_VALUE },
		max = { id: null, x: null, y: -Number.MAX_VALUE };

	/* search min. and max. values */
	$.each(serie.data, function(i, point) {
		if (setMin && (point[1] < min.y)) {
			min = { id: i, x: point[0], y: point[1] }
		} else if (setMax && (point[1] > max.y)) {
			max = { id: i, x: point[0], y: point[1] }
		}
	});

	if (min.id != null) {

		serie.data[min.id] = {
			marker: {
				enabled: true,
				symbol: 'circle',
				fillColor: serie.color
			},
			dataLabels: {
				enabled: true,
				color: serie.color,
				style: { fontWeight: 'bold' },
				align: 'center',
				y: 7,
				borderRadius: 3,
				backgroundColor: 'rgba(252, 255, 197, 0.7)',
				borderWidth: 1,
				borderColor: '#AAA'
			},
			x: min.x,
			y: trimDecimal(min.y)
		};
	}

	if (max.id != null) {

		serie.data[max.id] = {
			marker: {
				enabled: true,
				symbol: 'circle',
				fillColor: serie.color
			},
			dataLabels: {
				enabled: true,
				color: serie.color,
				style: { fontWeight: 'bold' },
				align: 'center',
				y: -7,
				borderRadius: 3,
				backgroundColor: 'rgba(252, 255, 197, 0.7)',
				borderWidth: 1,
				borderColor: '#AAA'
			},
			x: max.x,
			y: trimDecimal(max.y)
		};
	}

	return serie;
}