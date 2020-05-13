
$(document).ready(function() {

	var options;
	var datasets;
	var data;



	// BUBBLE = appydex / pagespeed

	var bubbleChart = $('#bubble-chart');

	options = {
	    legend: {
	        display: false
	    },
	    title: {display: true, text: 'Rapport satisfaction / performance'},
	    scales: {
	        xAxes:
	                [
	                    {
	                        scaleLabel: {display: true, labelString: "Indice de performance : Page Speed"},
	                        ticks: {
	                        	// beginAtZero: true, 
	                        	// max: 100
	                        	suggestedMin: 0,
	                        	suggestedMax: 100
	                        }
	                    }
	                ],
	        yAxes:
	                [
	                    {
	                        scaleLabel: {display: true, labelString: "Indice de satisfaction : appYdex"},
	                        ticks: {
	                        	// beginAtZero: true, 
	                        	// max: 100
	                        	suggestedMin: 0,
	                        	suggestedMax: 100
	                        }
	                    }
	                ]
	    },
	    tooltips: {
	        callbacks: {
	            label: function (t, d) {
	                return d.datasets[t.datasetIndex].label +
	                        ' (appYdex:' + t.xLabel + ' ; PageSpeed:' + t.yLabel + ')';
	            }
	        }
	    }
	};

	datasets = [];

	datasets.push({
		label: analyse.url,
		borderColor: '#000',
		backgroundColor: '#dc5b30',
		borderWidth: 1,
		radius: 8,
		hoverBorderWidth: 2,
		hoverRadius: 3,
		data: [
            {
                x: analyse.score_pagespeed,
                y: analyse.note_appydex,
                r: 7
            }
        ]
	});

	$.each(tabs.score_pagespeed, function(i, tab) {

		if(tab.url != analyse.url) {

			datasets.push({
				label: tab.url,
				borderColor: 'rgba(0, 0, 0, 0.5)',
				backgroundColor: 'rgba(80, 80, 80, 1)',
				borderWidth: 1,
				radius: 8,
				hoverBorderWidth: 2,
				hoverRadius: 3,
				data: [
	                {
	                    x: tab.score_pagespeed,
	                    y: tab.note_appydex,
	                    r: 4
	                }
	            ]
			});
		}
	});

	data = {
		labels: [],
		datasets: datasets
	};

	var bc = new Chart(bubbleChart, {
		type: 'bubble',
		data: data,
		options: options
	});





	// BUBBLE = speed_index_wpt / fullyloaded_wpt

	var bubbleChartWPT = $('#bubble-chart-wpt');


	options = {
	    legend: {
	        display: false
	    },
	    title: {display: true, text: 'Vitesse de chargement'},
	    scales: {
	        xAxes:
	                [
	                    {
	                        scaleLabel: {
	                        	display: true, 
	                        	labelString: "Temps de chargement total (s)"
	                        },
	                        ticks: {
	                        	beginAtZero: true, 
	                        	// max: 60,

	                        	suggestedMin: 0,
	                        	suggestedMax: 30
	                        }
	                    }
	                ],
	        yAxes:
	                [
	                    {
	                        scaleLabel: {display: true, labelString: "Temps de chargement index (s)"},
	                        ticks: {
	                        	beginAtZero: true, 
	                        	// max: 100,

	                        	suggestedMin: 0,
	                        	suggestedMax: 10
	                        }
	                    }
	                ]
	    },
	    tooltips: {
	        callbacks: {
	            label: function (t, d) {
	                return d.datasets[t.datasetIndex].label +
	                        ' (speed_index:' + t.yLabel + ' ; fullyloaded:' + t.xLabel + ')';
	            }
	        }
	    }
	};



	datasets = [];

	datasets.push({
		label: analyse.url,
		borderColor: '#000',
		backgroundColor: '#dc5b30',
		borderWidth: 1,
		radius: 8,
		hoverBorderWidth: 2,
		hoverRadius: 3,
		data: [
            {
                x: analyse.fl_wpt_sec,
                y: analyse.si_wpt_sec,
                r: 7
            }
        ]
	});

	$.each(tabs.speed_index_wpt, function(i, tab) {

		if(tab.url != analyse.url) {

			datasets.push({
				label: tab.url,
				borderColor: 'rgba(0, 0, 0, 0.5)',
				backgroundColor: 'rgba(80, 80, 80, 1)',
				borderWidth: 1,
				radius: 8,
				hoverBorderWidth: 2,
				hoverRadius: 3,
				data: [
	                {
	                    x: tab.fl_wpt_sec,
	                    y: tab.si_wpt_sec,
	                    r: 4
	                }
	            ]
			});
		}
	});

	data = {
		labels: [],
		datasets: datasets
	};

	var bcw = new Chart(bubbleChartWPT, {
		type: 'bubble',
		data: data,
		options: options
	});
});