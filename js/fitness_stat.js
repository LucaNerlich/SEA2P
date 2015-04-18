canvas = document.getElementById("fitnessStat");
ctx = canvas.getContext("2d");



max = 0;
for (var i = 0; i < 13; i ++)
{
	if (statData[i] > max)
	{
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

	

	ctx.beginPath();
	ctx.strokeStyle = "#aaa";
	ctx.lineWidth = 1;
	for (var i = 1; i < 5; i ++)
	{
		ctx.moveTo(0,partH * i);
		ctx.lineTo(w,partH * i);
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