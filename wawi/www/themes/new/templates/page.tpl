<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=1200, user-scalable=yes" />
<title>[HTMLTITLE]</title>



<!--[if IE 6]>
<style type="text/css">
.box li {
background:none;
filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='./themes/new/images/menue_arrow.png', sizingMethod='scale');
}
</style>
<![endif]-->

<script type="text/javascript" src="./js/ajax_001.js"></script>


<script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>


<script type="text/javascript" src="./js/jquery.tablehover.min.js"></script>

<script type="text/javascript" src="./js/jquery.jeditable.js" ></script>
<script type="text/javascript" src="./js/jquery.cookie.js" ></script>

<script type="text/javascript" src="./js/jquery.zclip.min.js" ></script>


<script type="text/javascript" src="./js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="./js/jquery.inputhighlight.min.js"></script>
<script type="text/javascript" src="./js/grider.js" ></script>
<script type="text/javascript" src="./js/jqclock_201.js"></script>

<link href="./themes/[THEME]/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="screen">
<link href="./themes/[THEME]/css/demo_table.css" rel="stylesheet" type="text/css" media="screen">
<script type="text/javascript" src="./js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="./js/tinymceinstances.js"></script>
<script type="text/javascript" src="./js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="./plugins/datatables/dataTables.tableTools.min.js"></script>
<link href="./plugins/datatables/dataTables.tableTools.min.css" rel="stylesheet" type="text/css" media="screen">


[CSSLINKS]

<link href="./themes/[THEME]/css/wiki.css" rel="stylesheet" type="text/css" />
<link href="./themes/[THEME]/css/colorPicker.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="./themes/[THEME]/js/jquery-ui-1.10.3.custom.min.js"></script>
<link type="text/css" href="./themes/[THEME]/css/start/jquery-ui-1.10.3.custom.css" rel="Stylesheet" /> 


<script type="text/javascript" src="./js/jquery.ui.touch-punch.js"></script>


<script type="text/javascript" src="./js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="./js/jquery.colorPicker.js"></script>

<!--<script type="text/javascript" src="./js/jquery.qtip.js"></script>-->

<script type="text/JavaScript" language="javascript">
[JAVASCRIPT]



function setVisibility(rowName) {
        // Tabellenzelle ermitteln
        [HAUPTMENUJS]

        if(rowName!="hidden")
        {
        document.getElementById(rowName).style.visibility = "visible";
        document.getElementById(rowName).style.display = "block";
        }
}

$(document).ready(function() {

var util = { };

document.addEventListener('keydown', function(e){

    var key = util.key[e.which];
    if( key ){
        e.preventDefault();
    }

    if( key === 'F2' ){        
      // do stuff
			document.getElementById("direktzugriff").focus();
    }
 
		
})

util.key = { 
  //112: "F1",
  113: "F2",
  /*114: "F3",
  115: "F4",
  116: "F5",
  117: "F6",
  118: "F7",
  119: "F8",
  120: "F9",
  121: "F10",
  122: "F11",
  123: "F12"*/
}


$(window).scroll(function () {
    if ($(window).scrollTop() > 80) {
        $('#scroller').css('top',  $(window).scrollTop()-70);
        $('#scroller2').css('top',  $(window).scrollTop()-80);
        $('#scroller2').css('z-index', '2');
    }
		else
		{
        $('#scroller').css('top',  0);
        $('#scroller2').css('top',  0);
        $('#scroller2').css('z-index', '0');
		}
}
);

  [DATATABLES]

  [SPERRMELDUNG]
 
  [AUTOCOMPLETE]

  [JQUERY]

servertime = parseFloat( $("#servertime").val() ) * 1000;
$("#clock").clock({"timestamp":servertime,"format":"24","langSet":"de"});

   // Make sure to only match links to wikipedia with a rel tag
   $('a[tooltip]').each(function()
   {
      // We make use of the .each() loop to gain access to each element via the "this" keyword...
      $(this).qtip(
      {
         content: {
            // Set the text to an image HTML string with the correct src URL to the loading image you want to use
            text: '<img class="throbber" src="/projects/qtip/images/throbber.gif" alt="Loading..." />',
            ajax: {
               url: 'index.php?module=ajax&action=tooltipsuche&term=' + $(this).attr('tooltip') // Use the rel attribute of each element for the url to load
            },
            title: {
               text: 'Suche - ' + $(this).text(), // Give the tooltip a title using each elements text
               button: true
            }
         },
         position: {
            at: 'bottom center', // Position the tooltip above the link
            my: 'top center',
            adjust: { screen: true } // Keep the tooltip on-screen at all times
         },
         show: {
            event: 'click',
            solo: true // Only show one tooltip at a time
         },
         hide: 'unfocus',
         style: {
            classes: 'ui-tooltip-ajax ui-tooltip-light ui-tooltip-shadow', // Use the default light style
         }
      })
 // Make sure it doesn't follow the link when we click it
      .click(function() { return false; });
   });



 $('a.popup').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var horizontalPadding = 30;
            var verticalPadding = 30;
            $('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
                title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
                autoOpen: true,
                width: [POPUPWIDTH],
                height: [POPUPHEIGHT], 
                modal: true,
                resizable: true
            }).width([POPUPWIDTH] - horizontalPadding).height([POPUPHEIGHT] - verticalPadding);            
        });

$(document).ready(function() {
     $('.editable').editable('index.php?module=[MODULE]&action=editable', {
                                        indicator : 'Speichere...',
                                        tooltip : 'zum Bearbeiten anklicken...'

  });



 });



  [JQUERYREADY]

});

/*
$(".info").hide().first().show('slow');
setTimeout(showNotifications, 3000);
function showNotifications(){
    $(".info:visible").hide('slow', function(){
        $(this).remove();
        $(".info").first().show('slow');
        if($(".info").length > 0){
           setTimeout(showNotifications, 3000);
        }
    });
}

$(".error2").hide().first().show('slow');
setTimeout(showNotificationsError2, 3000);
function showNotificationsError2(){
    $(".error2:visible").hide('slow', function(){
        $(this).remove();
        $(".error2").first().show('slow');
        if($(".error2").length > 0){
           setTimeout(showNotificationsError2, 3000);
        }
    });
}
*/

var timeout    = 500;
var closetimer = 0;
var ddmenuitem = 0;

function jsddm_open()
{  jsddm_canceltimer();
   jsddm_close();

   $('#scroller2').css('z-index', '9');
   ddmenuitem = $(this).find('ul').css('visibility', 'visible');
}

function jsddm_close()
{  
	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

	$('#scroller2').css('z-index', '9');
	if(isAndroid) {

	} else {
	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');
	}
}

function jsddm_timer()
{  closetimer = window.setTimeout(jsddm_close, timeout);}

function jsddm_canceltimer()
{  if(closetimer)
   {  window.clearTimeout(closetimer);
      closetimer = null;}}
$(document).ready(function()
{  $('#jsddm > li').bind('mouseover', jsddm_open)
   $('#jsddm > li').bind('mouseout',  jsddm_timer)});

document.onclick = jsddm_close;
 

</script>
<script>
	$(function() {
			$( "a", ".tabsbutton" ).button();
			});
</script>
							
  <script>
  $(function() {

[BEFORETABS]
$( "#tabs" ).tabs({
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 0,
				load: 0
			}
		});


    // here we are looking for our tab header
    hash = window.location.hash;
    elements = $('a[href="' + hash + '"]');
    if (elements.length === 0) {
        $("ul.tabs li:first").addClass("active").show(); //Activate first tab
    } else {
        elements.click();
    }


  });
  </script>
[ADDITIONALJAVASCRIPT]
<style>
  .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
  input.ui-autocomplete-input { background-color:#D5ECF2; }
  .ui-autocomplete { font-size: 8pt; }
	.ui-widget-header {border:0px;}
	.ui-dialog { z-index: 10000 !important ;}

[YUICSS]
</style>
</head>

<body  class="ex_highlight_row">
[SPERRMELDUNGNACHRICHT]
    <div class="container_6">
    
<div id="header" class="grid_6">
<table width="100%" border="0"><tr valign="bottom"><td>
             <a href="index.php"><img src="[TPLLOGOFIRMA]" style="padding-left:20px; padding-top:8px;" height="50" align="left" border="0"/></a>
</td><td align="">
            <!-- end topnav -->

[THEMEHEADER]
</td><td align="right">


<div style="padding-right:5px; color: [TPLNAVIGATIONFARBESCHRIFT];" align="right"><!--<a href="index.php?module=welcome&action=logout">Logout</a>-->
<table cellpadding="0" cellspacing="0"><tr><td>[FIRMENNAME]&nbsp;<!--[VORGAENGELINK]-->|&nbsp;</td><td><div id="clock"></div><input id='servertime' type='hidden' value='[TIMESTAMP]' /></td></tr></table>
Benutzer: [BENUTZER] | KW [CALENDERWEEK] von [CALENDERWEEKMAX] 

<br>[STECHUHR]

</div>
</td></tr></table>
<div id="scroller" style="position: absolute; width:100%; top:0px; z-index:200;">
  <ul id="jsddm" style="background-color:[TPLSYSTEMBASE]; z-index:200; width:100%;">
<li style="background-color:[TPLFIRMENFARBEGANZDUNKEL]; height:41px; border-right: 1px solid #888a89;">
<img src="./themes/new/images/module/Icon_Dashboard_64.gif" height="25" style="margin: 7px 5px 3px 5px" onclick="window.location.href='index.php?module=welcome&action=start'"></li>
[NAV]
</ul>
</div>
           <!-- end logo -->
		</div>
		<!-- end header -->
		<div class="clear"></div>
        
		<!-- end NAV -->
<!--
<div id="header2" class="grid_6">
		<div class="clear"></div>

<b>[UEBERSCHRIFT]</b>
</div>-->
<!--
<div id="header2" class="grid_6">
<span id="headertoolbar">[TOPHEADING]</span>&nbsp;&nbsp;&nbsp;<span id="headertoolbar2">[KURZUEBERSCHRIFT] [KURZUEBERSCHRIFT2]</span>
</div>-->
       
 <div class="grid_6 bgstyle">
<table width="100%"><tr valign="top">
[ICONBAR]
<td>
 <style>
.ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default  {
color:#fff;/*[TPLFIRMENFARBEHELL];*/
background-color:[TPLFIRMENFARBEHELL];
}

.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
    border: 1px solid [TPLFIRMENFARBEGANZDUNKEL];
		background:none;
    background-color: #E5E4E2;
    color: [TPLFIRMENFARBEGANZDUNKEL];
}

.ui-state-hover a,
.ui-state-hover a:hover,
.ui-state-hover a:link,
.ui-state-hover a:visited {
  color: [TPLFIRMENFARBEGANZDUNKEL];
  text-decoration: none;
}

.ui-state-hover,
.ui-widget-content .ui-state-hover,
.ui-widget-header .ui-state-hover,
.ui-state-focus,
.ui-widget-content .ui-state-focus,
.ui-widget-header .ui-state-focus {
  border: 1px solid #448dae;
  font-weight: normal;
  color: [TPLFIRMENFARBEGANZDUNKEL];
}


.ui-tabs-nav {
background: [TPLFIRMENFARBEHELL];
}

.ui-widget-content {
    border-top: 1px solid [TPLFIRMENFARBEHELL];
    border-left: 1px solid [TPLFIRMENFARBEHELL];
    border-right: 1px solid [TPLFIRMENFARBEHELL];
}

.ui-state-default, .ui-widget-header .ui-state-default {
    border: 0px solid none;
}

.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
    border: 0px solid [TPLFIRMENFARBEHELL];
}

.ui-widget-content .ui-state-default a, .ui-widget-header .ui-state-default a, .ui-button-text {
font-size:8pt;
font-weight:bold;
border: 0px;
}


.ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active  {
color:[TPLFIRMENFARBEGANZDUNKEL];
}

.ui-widget-content .ui-state-active a, .ui-widget-header .ui-state-active a {
color:[TPLFIRMENFARBEGANZDUNKEL];
font-weight:bold;
font-size:8pt;
background-color:[TPLFIRMENFARBEHELL];
border: 0px;
}

ul.ui-tabs-nav {
  background: [TPLFIRMENFARBEHELL];
  padding:2px;
}
.ui-widget-header {
  background: [TPLFIRMENFARBEHELL];
}
.ui-button-icon-primary.ui-icon.ui-icon-closethick
{
background-color:[TPLFIRMENFARBEDUNKEL];
color:white;
}


#toolbar {
padding: 4px;
display: inline-block;
}
/* support: IE7 */
*+html #toolbar {
display: inline;
}
</style>
<div id="scroller2" style="margin-top:3px; padding:0px; width:[CSSSMALL1];border: 0px solid rgb(166, 201, 226);
background-color:[TPLFIRMENFARBEGANZDUNKEL];position:relative; height:73px;">

<div width="100%" style="height:10px; background-color:[TPLSYSTEMBASE];"></div>
<div width="100%" style="height:8px; background-color:[TPLFIRMENFARBEGANZDUNKEL];"></div>
<span id="headertoolbar">[KURZUEBERSCHRIFT]</span><span id="headertoolbar1">[KURZUEBERSCHRIFT1]</span>&nbsp;&nbsp;&nbsp;<span id="headertoolbar2">[KURZUEBERSCHRIFT2]</span>
<div width="100%" style="height:8px; background-color:[TPLFIRMENFARBEGANZDUNKEL];"></div>
 <table cellpadding="2"><tr><td></td><td><a href="[TABSBACK]"><img src="./themes/new/images/back.jpg"></a></td><td>[TABSADD]</td><td>
<div style="position:absolute;top:51px;">[TABS]</div></td></tr></table>
</div>
<div style="width:[CSSSMALL2]; border: 0px solid rgb(166, 201, 226); border-right:8px solid [TPLFIRMENFARBEGANZDUNKEL];border-left:8px solid [TPLFIRMENFARBEGANZDUNKEL];
background-color:white; min-height:400px; border-bottom:8px solid [TPLFIRMENFARBEGANZDUNKEL];">
[PAGE]
</div>
</td></tr></table>

<div class="clear"></div>
	    </div>
		<!-- end CONTENT -->
        
		<!-- end RIGHT -->
        
        <div id="footer" class="grid_6">
          <ul>
          	<li><a href="http://handbuch.wawision.de" target="_blank">Handbuch</a></li>
          	<li><a href="http://www.wawision.de" target="_blank">www.wawision.de</a></li>
    		<li>&copy; [YEAR] embedded projects GmbH | waWision &reg; |
		Versionsnummer: [REVISION]  | <a href="index.php?module=welcome&action=info">[VERSION]</a></li>
		  </ul>
		</div>
        <!-- end FOOTER -->
		<div class="clear"></div>

    </div>

</body>

</html>
