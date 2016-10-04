/**
 * 
 */

var feImages = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
var feImagesTip =  {
		text: 'Es ist nur Bildern erlaubt!',
		title: 'Bildervalidator',
		button: true,
	};
//qtip define for image upload 
var qtImg = { 
	content: 	feImagesTip,
	hide: 		{event: false},
	position:  	{my: 'bottom middle', at: 'top middle'},
	style: 		{classes: 'qtip-rounded qtip-bootstrap qtip-green'},
	events: 	{hide: function(e, a) {$(".qtip").remove();}}
}


$(document).ready(function(){
	initQ();
});


function initQ(){
	
	//validation form
	initValid()
}

	
	
/**
 * UI validation only, check if user's input is correct 
 * @returns
 */
function initValid(){
	//if validate is not loaded
	if(!jQuery().validate) {
	     return;
	 }
	
	//check and show qtip if by typing of new username/email they are in the db already
	$('#user_username, #user_email').on("keyup", function() {
		var typ="username";
		var rr=$('#'+$(this).attr('id')+'-error');
		rr.hide();
		rr.text('');
		var v = $(this).val();
		if (v.indexOf("@")>-1){
			if (v.length<6) return;
			//v=v.substr(0,v.indexOf("@"));
			typ='email'
		}
		if (v.length<2) return;
		
		if (typeof $('#aj-user-is').data('path') === 'undefined') return;
		$.ajax({
			type: "GET",
			url: $('#aj-user-is').data('path')+'?typ='+typ+'&new='+v,
			beforeSend: function( xhr ) {
				rr.removeClass('result-ok');
				rr.removeClass('result-err');
				//rr.addClass('result-loading');
				rr.html('');
				rr.show();
			},
			success: function(data) {
				if (data.jerr!=''){
					rr.removeClass('result-loading');
					rr.removeClass('result-ok');
            		//rr.addClass('result-err');
            		rr.text(data.jerr);
            		rr.show();
            	}else{
            		rr.removeClass('result-loading');
            		//rr.addClass('result-ok');
            		rr.removeClass('result-err');
            		rr.html(data.html);
            		rr.show();
            	}
			}
		});

	});
	
	//reg-user
	$("#f-admin-user").validate({
        // Specify the validation rules
        rules: {
        	 'user[username]': {
                 required: true,
                 minlength: 2
             },
            'user[email]': {
                required: true,
                email: true,
                minlength: 3
            },
            'user[password]': {
                required: true,
                minlength: 5
            },
        },
        // Specify the validation error messages
        messages: {
        	'user[username]': {
                required: "Benutzername darf nich leer sein",
                minlength: "Geben Sie bitte 2 Buchtaben"
            },
        	'user[email]': {
                required: "E-mail darf nich leer sein",
                email: 'E-mail ist falsch eingegeben',
                minlength: "Geben Sie bitte 3 Buchtaben"
            },
            'user[password]': {
                required: "Password darf nich leer sein",
                minlength: "Geben Sie bitte 5 Buchtaben"
            },
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
	
	
}	
