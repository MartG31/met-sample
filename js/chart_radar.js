
$(document).ready(function() {

	var radarChart = $('#radar-chart');

	var options = {
        title: {
            display: false,
            text: ''
        },
        legend: {
            display: false
        },
        scale: {
            ticks: {
                beginAtZero: true,
                min: 0,
                max: 100,
                stepSize: 50
            },
            angleLines:{
                lineWidth: 2
            },
            pointLabels: {
                fontSize: 13
            }
        }
    };

	var data = {
		labels: ['Indice appYdex', 'Indice PageSpeed', 'Indice SpeedIndex WPT'],
		datasets: [
            {
                label: analyse.url,
                fill: true,
                backgroundColor: "#dc5b3011",
                borderColor: "#dc5b3066",
                radius: 5,
                pointRadius: 5,
                pointBorderWidth: 1,
                pointBackgroundColor: '#dc5b30',
                pointBorderColor: "#000",
                pointHoverRadius: 7,
                data: [analyse.note_appydex, analyse.score_pagespeed, analyse.si_wpt_bareme] 
           }
        ]
	};

	var rc = new Chart(radarChart, {
		type: 'radar',
		data: data,
		options: options
	});
});