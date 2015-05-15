canvas = document.getElementById("fitnessStat");
ctx = canvas.getContext("2d");



max = 0;
for (var i = 0; i < 13; i ++)
{
	//alert(statData[i]);
	if (statData[i] > max)
	{
		//alert("max: " + max + " neu:" + statData[i]);
		//alert(typeof statData[i]);
		max = statData[i];
	}
}


// translate Height To Canvas
function tH(pH)
{
	return (h-30)-(pH/max*(h-30)) + 15;
}

function redraw()
{
	w = ctx.canvas.clientWidth;
	h = ctx.canvas.clientHeight;
	ctx.canvas.width  = w;
	ctx.canvas.height = h;
	//ctx.clear(0,0,w,h);
	var grd = ctx.createLinearGradient(0, 0, 0, h);
	grd.addColorStop(0,"#fafafa"); //9C6363
	grd.addColorStop(1,"#ddd"); //D2DFD3

	// Fill with gradient
	ctx.fillStyle = grd;
	ctx.fillRect(0, 0, w, h);
	
	
	part = w / 12;
	partH = h / 5;

	
	// Hintergrundlinien einzeichen
	ctx.beginPath();
	ctx.strokeStyle = "#aaa";
	ctx.fillStyle = "#aaa";
	ctx.lineWidth = 1;
	for (var i = 1; i < 5; i ++)
	{
		ctx.font = "20px Verdana";
		ctx.fillText(Math.round((max/6)*(6-i)),10,partH * i + 8);
		ctx.moveTo(60,partH * i);
		ctx.lineTo(w-60,partH * i);
		ctx.fillText(Math.round((max/6)*(6-i)),w-50,partH * i + 8);
		ctx.stroke();
	}
	
	ctx.beginPath();
	ctx.strokeStyle = "#297ACC";
	ctx.lineWidth = 4;
	for (var i = 1; i < 13; i ++)
	{
		ctx.moveTo(part*(i-1),tH(statData[i-1] - 5));
		ctx.lineTo(part*i,tH(statData[i] - 5));
		ctx.stroke();	
	}
	for (var i = 0; i < 13; i ++)
	{
		ctx.beginPath();
		ctx.arc(part*i,tH(statData[i] - 5),10,0,2*Math.PI);
		ctx.fillStyle = '#99CCFF';
		ctx.fill();
		ctx.strokeStyle = '#297ACC';
		ctx.stroke();
	}
}


window.onresize = function(event) {
	redraw();
};

redraw();