function plotgraph() {

    [VARIABLEN]

   var stack = 0,
			bars = true,
			lines = false,
			steps = false;
 
    function plotWithOptions() {
        $.plot($("#placeholder"), [
[PLOTLEGENDS]
    ], {
            series: {
                stack: stack,
                lines: { show: lines,
						steps: steps },
                bars: { show: bars, barWidth:0.6, lineWidth:0, fill:1}
            },

	    yaxis: {
            min: 1
	    },

	    xaxis: {
            ticks: [DATUM]
	    }

        });

var data = [
    [UMSATZPIE]
  ];

    // GRAPH 1
  $.plot($("#umsatzpie"), data, 
  {
series: {
 pie: {
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 2/3,
                    formatter: function(label, series){
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                    },
                    threshold: 0.01
                }
            }
        }
  });
 
var data2 = [
    [GUTSCHRIFTUMSATZPIE]
  ];

    // GRAPH 1
  $.plot($("#umsatzpie2"), data2, 
  {
series: {
            pie: {
                show: true
            }
        }
  });
 
    }
    plotWithOptions();
};

