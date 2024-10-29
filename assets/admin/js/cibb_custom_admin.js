function copyToClipboard(text) {

	var textArea = document.createElement( "textarea" );
	textArea.value = text;
	document.body.appendChild( textArea );       
	textArea.select();
 
	try {
	   var successful = document.execCommand( 'copy' );
	   var msg = successful ? 'successful' : 'unsuccessful';
	   console.log('Copying text command was ' + msg);
	} catch (err) {
	   console.log('Oops, unable to copy',err);
	}    
	document.body.removeChild( textArea );
 }
 
 jQuery(document).ready(function($){
	 jQuery( '.bb_copy_btn' ).click( function(e) {
		 e.preventDefault();
		 var clipboardText = "";
		 var currentBtn = $(this);
		 $(".bb_shortcode_text p").removeClass("active");
		 console.log( $(this).parents("tr").find("p") );
		 setTimeout(function(){
			 currentBtn.parents("tr").find("input").focus();
			 currentBtn.parents("tr").find("p").addClass('active');
			 currentBtn.parents("tr").find("input").select();			
		 },100);
		 clipboardText = $(this).parents("tr").find("input.bb_input").val();
		 console.log($(this).parents("tr").find("input.bb_input"));
		 copyToClipboard( clipboardText );
		 $(this).text("Copied");
		 setTimeout(function(){
			 console.log(currentBtn);
			 currentBtn.text("Copy");
		 },1500);
	  });
	 jQuery(".bb_shortcode_text input").focus(function(){
		 $(this).select();
	 });
	 $('.bb_color_picker').wpColorPicker();
	 $(".bb_default_color").wpColorPicker({
		change: function (event, ui) {
			var element = event.target;
			var color = ui.color.toString();
			$(".bb_default_color_txt").html(color);
    	},
	});
 })