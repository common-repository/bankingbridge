var type = bb_js_object.type;
var bbid = bb_js_object.bbid;
var bbtexthovercolor = bb_js_object.button_text_color;

var popuptype = 'standalone';
if( type == 'popup' ){
	popuptype = 'api';
}

jQuery(document).ready(function($){	
	//console.log(bb_js_object);
	//console.log(bbtexthovercolor);
	$('[id^="bkbg"]').click( function() {
		//console.log("current color click bkge");
		var current_circle_color = $(this).attr('data-circle-color');
		//console.log("current color click "+current_circle_color);
		$(".bankingbridge-modal-loader__circle").css( 'fill', current_circle_color );
	})
	jQuery('#'+bbid+'-buttons').append("<style type='text/css'>"+
       "#"+bbid+"-buttons .bkbg_buttons .buttons__item:hover .buttons__icon {"+
		 "border-color: "+bbtexthovercolor+";"+
       "} #"+bbid+"-buttons .bkbg_buttons .buttons__item:hover .accent-color{"+
         "color: "+bbtexthovercolor+";"+
       "} #"+bbid+"-buttons .buttons__icon .accent-color{"+
         "color: "+bbtexthovercolor+";"+
       "}"
   );
	//$("iframe").contents().find(".buttons__item.is-alt-btns.is-checked  ").css('display', 'none');
})

/*jQuery('#bb-24b16 iframe').
    contents().find("head").append("<style type='text/css'>"+
       "button.buttons__item.is-last.is-alt-btns.is-checked {"+
         "display: none;"+
       "} "
   );*/


window.addEventListener('DOMContentLoaded', function() {BB.init(bb_js_object.app_key, document.getElementById(bbid),{type:popuptype});})
function main(purpose){
  BB.api.workflowInit({loan_purpose: purpose})
  BB.api.openModal("leadWorkflow")
}

/*setTimeout(function() {
		
		console.log('start load');
		
		var newIframe = document.getElementsByTagName("iframe")[0];
		
		console.log(newIframe);
        // create content inside iframe
        var iframeDoc = newIframe.contentDocument || newIframe.contentWindow.document;
        
		var iframeBody = iframeDoc.body;
		console.log(iframeBody);
		        
		var link = iframeDoc.createElement('link');
		
		
		// Set the attributes 
		// for link element  
		link.href = "iframeCss.css";
		link.rel = "stylesheet";
		link.type = "text/css";
		  
		iframeBody.appendChild(link);
		
}, 500);*/