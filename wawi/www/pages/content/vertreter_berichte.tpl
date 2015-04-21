<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.stack.js"></script>
<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.pie.min.js"></script>
<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.selection.js"></script>


<div id="tabs">
<ul>   
        <li><a href="#tabs-1">Vertreter</a></li>
        <li><a href="#tabs-2">Diagramm</a></li>
 </ul>

<div id="tabs-1">
<div class="info">Letzte Berechnung am 01.12.2014.</div>
[VERTRETER]
</div>

<div id="tabs-2">

<script type="text/javascript">

        $(function() {
      var options = {
        legend:{        
            position:"nw",           
        },
axisLabels: {
            show: true
        },
grid: {
    borderWidth: {top: 0, right: 0, bottom: 0, left: 0},
    borderColor: {top: "#000",bottom:"#000", left: "#FFF"},
        tickColor: '#EEE',  // => color used for the ticks
hoverable: true,
        clickable: true
},
        yaxes: [{
            position: 'left',
        }, {
            position: 'right',
        }]

    };

  $("<div id='tooltip'></div>").css({
      position: "absolute",
      display: "none",
      border: "1px solid #fdd",
      padding: "2px",
      "background-color": "#fee",
      opacity: 0.80
    }).appendTo("body");

    $("#placeholder").bind("plothover", function (event, pos, item) {

        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
        $("#hoverdata").text(str);

        if (item) {
          var x = item.datapoint[0].toFixed(2),
            y = item.datapoint[1].toFixed(2);

          $("#tooltip").html(item.series.label + " of " + x + " = " + y)
            .css({top: item.pageY+5, left: item.pageX+5})
            .fadeIn(200);
        } else {
          $("#tooltip").hide();
        }
    });

                $.plot("#placeholder", [
                  [LABEL]
                ],   options
        );
        });1

        </script>
<style>
.demo-container {
        box-sizing: border-box;
        width: 620px;
        height: 400px;
}

.demo-placeholder {
        width: 100%;
        height: 100%;
        font-size: 14px;
        line-height: 1.2em;
}

.legend table {
    border-spacing: 5px;
    margin-left: 30px;
}
</style>

<table width="100%"><tr><td>
   <div class="demo-container">
                        <div id="placeholder" class="demo-placeholder" style="width:1100px; height:400px;"></div>
   </div>
</td></tr>
</table>
</div>




</div>
