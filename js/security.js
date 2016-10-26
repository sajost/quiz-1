/**
 * 
 */

$(document).ready(function() {
	initS();
})

function initS(){
	//init tooltips
	//$('[data-toggle="tooltip"]').tooltip();
	$('#btn-activate').on("click", function() { 
		$('#f-security-activate').fadeToggle('slow');
	});
	
	//resend activation dialog
	$('#modal-resend').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var username = button.data('username') // Extract info from data-* attributes
	  var modal = $(this)
	  modal.find('.modal-title').text('Resend Aktivierungscode' + username)
	  modal.find('.modal-body input').val(username)
	})
	
	$('#modal-btn-resend').click(function(){
		var btn=$(this);
		btn.removeClass('btn-danger');
		btn.removeClass('btn-success');
        $.ajax({
             type: "POST",
             url: btn.data("path")+"?u="+$('#modal-username').val(), 
             success: function(data) {
            	 btn.addClass('btn-success');
            	 $('#modal-resend').modal('hide');
             },
             error: function (r, s, err) {
            	 btn.addClass('btn-danger');
            	 console.log(r.responseText); 
            	 $("#modal-resend-alert span.text").text(htmlDecode(r.responseText));
            	 $("#modal-resend-alert").fadeIn();
             }
         });
        return false;
    });
	
	
	//modal clicks - password
	$("#modal-ok-password").click(function(){
		//$( "#f-password" ).submit();
		//$( "#f-password" ).trigger('submit', [ 'u', $('#u').val() ]);
		//$('#modal-password').modal('hide');
		window.location.href = $( "#m-f-password" ).attr('action')+"?u="+$('#u').val();
	})
	$("#u").keyup(function(e) {
	     if (e.keyCode == 13) { // enter
	    	 $("#modal-ok-password").click();
	    }
	});
}

