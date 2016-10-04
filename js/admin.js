/**
 * 
 */

$(document).ready(function() {
	init();
})

function init(){
	//init tooltips
	//$('[data-toggle="tooltip"]').tooltip();
	//CROPPER & AJAX UPLOADER for avatar only
	initUserImgUp("avatar", {aspectRatio:1/1, minContainerWidth:200, minContainerHeight:200, minCropBoxWidth:10});
	//answers count toggle
	$('.answers-count').on("click", function() { 
		$(this).addClass('active');
	    $(this).siblings().removeClass('active');
		$('.answers-group > .list-group-item:gt(-5)').hide()
		$('.answers-group > .list-group-item:lt('+$(this).text()+')').show(); 
		$('#question_answercount').val($(this).text());
	});
	
}


function initUserImgUp(typ,ar) { 
	if (!$('#user_'+typ+'_f').length) return; //nothing to do, if not on the page of profile-edit
	if ($('#user_'+typ+'').val()!='') $('#user_'+typ+'').val(''); //by same without avatar change, dont need to resize and cropt the image 
	//CROPPER & AJAX UPLOADER for selfi only
	if( !$('#ctrl-up-'+typ+'').is(':visible') ) {
		$("#change-"+typ+"-1").click(function(){
    		$('#user_'+typ+'_f').focus().trigger('click');
    	});
	}
	$("#but-up-"+typ+"").click(function(){
		if ($('#user_username').val()==''){
			feImagesTip['text']='Geben Sie bitte erstmal Benutzername!';
			$('#but-up-'+typ+'').qtip(qtImg).qtip('show');
			return false;
		}
		$('#user_'+typ+'_f').focus().trigger('click');
	});
	//input file onchange
	$("#ctrl-up-"+typ+" input:file").change(function (){
		// submit the form 
		if ($.inArray($(this).val().split('.').pop().toLowerCase(), feImages) == -1) {
			feImagesTip['text']='Es ist nur Bildern erlaubt!';
			$('#change-'+typ+'-1').qtip(qtImg).qtip('show');
			return false;
		}
		//$('#user_username').prop("readonly",true);
		var formdata = new FormData();
		formdata.append($(this).attr('name'), $(this)[0].files[0]);
		formdata.append($('#user_username').attr('id'), $('#user_username').val());
		upboxImg.url = $("#images-avatar").data("path");
		upboxImg.data = formdata;
	    $.ajax(upboxImg);//send formData to server-side, only file  
	});
	//click remove image if exists
	$("#rem-"+typ+"-1").click(function(){
		$("#images-"+typ+"").empty();
		$("#ctrl-up-"+typ+"").show();
		$("#user_"+typ+"").val('');
	});
	
	// file-upload-ajax settings
    var upboxImg = { 
        type : 'post',
        dataType : 'json',
        async : true,
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        xhr: function()
        {
          var xhr = new window.XMLHttpRequest();
          //Upload progress
          xhr.upload.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
              var percentComplete = evt.loaded / evt.total;
              $('.progress-avatar > .progress-bar').css('width', percentComplete+'%').attr('aria-valuenow', percentComplete);
              //Do something with upload progress
              console.log(percentComplete);
            }
          }, false);
          return xhr;
        },
        beforeSend: function() {
        	feImagesTip['text']='';
        	$('.progress-avatar > .progress-bar').removeClass('progress-bar-danger').css('width', 0+'%').attr('aria-valuenow', 0).parent().fadeIn(300);
        }, // success identifies the function to invoke when the server response has been received 
        success: function(data) {
        	//server-side error @see -> error:
        	//if(!data.jerr) console.log("Server side error");
        	//some errors returning by server response
			if (data.jerr!='ok'){
				feImagesTip['text']+='<b>'+data.fncl+'</b> – '+data.jerr+'<br>';
				//add tip for errors from server
				if ($('#ctrl-up-'+typ+'').is(':visible'))
					$('#ctrl-up-'+typ+'').qtip(qtImg).qtip('show');
				else
					 $("#change-"+typ+"-1").qtip(qtImg).qtip('show');
				$('.progress-avatar > .progress-bar').addClass('progress-bar-danger').css('width', 100+'%').attr('aria-valuenow', 100).parent().slideUp(4000);
				return;
			}else{
				if ($('#ctrl-up-'+typ+'').is(':visible'))
					$('#ctrl-up-'+typ+'').qtip(qtImg).qtip('hide');
				else
					 $("#change-"+typ+"-1").qtip(qtImg).qtip('hide');
				$("#ctrl-up-"+typ+"").hide();
				$("#images-"+typ+"").empty();
				$(data.html).appendTo($('#images-'+typ+''));
			}
			//modal position
			$('#modal-'+typ).on('show.bs.modal', function () {
				$(this).find('.modal-body').css({
		              width:'auto', //probably not needed
		              height:'auto', //probably not needed 
		              'max-height':'100%'
				});
			});
			$('#modal-'+typ).on('shown.bs.modal', function () {
				$("#user_"+typ+"_x").attr('v',$("#user_"+typ+"_x").val());
				$("#user_"+typ+"_y").attr('v',$("#user_"+typ+"_y").val());
				$("#user_"+typ+"_h").attr('v',$("#user_"+typ+"_h").val());
				$("#user_"+typ+"_w").attr('v',$("#user_"+typ+"_w").val());
			});
    		$("#change-"+typ+"-1").click(function(){
        		$('#user_'+typ+'_f').focus().trigger('click');
        	});
    		$("#rem-"+typ+"-1").click(function(){
    			$("#images-"+typ+"").empty();
    			$("#ctrl-up-"+typ+"").show();
    			$("#user_"+typ+"").val('');
        	});
    		//save the name of image in the hidden input field, which is property in db
    		$("#user_"+typ+"").val(data.fnor);
			var cropBoxDataFull = null;
    		$('#img-crop-'+typ+'-1').cropper({
    			rotatable: false,
    			zoomable: false,
    			aspectRatio: ar['aspectRatio'],
    			//preview: ".item-"+typ+"",
    			autoCropArea: 0.9,
    			strict: false,
    			guides: false,
    			highlight: false,
    			dragCrop: true,
    			cropBoxMovable: true,
    			cropBoxResizable: true,
    			minContainerWidth: ar['minContainerWidth'],
    			minContainerHeight: ar['minContainerHeight'],
    			minCropBoxWidth: ar['minCropBoxWidth'],
    			responsive: false,//onresize window, hides the image in preview and in the cropper, bug?
    			built: function () {
					if(cropBoxDataFull==null) cropBoxDataFull=$('#img-crop-'+typ+'-1').cropper('getCropBoxData');
					$('.cropper-crop-box').on('dblclick',function(){
						$('#img-crop-'+typ+'-1').cropper('setCropBoxData', cropBoxDataFull)
					})
    			},
    			crop: function(d) {
    				$("#user_"+typ+"_x").val(Math.round(d.x));
    			    $("#user_"+typ+"_y").val(Math.round(d.y));
    			    $("#user_"+typ+"_h").val(Math.round(d.height));
    			    $("#user_"+typ+"_w").val(Math.round(d.width)); 
    			  }
    		});
    		//modal clicks
    		$("#modal-ok-"+typ+"").click(function(){
    			cropBoxData = $('#img-crop-'+typ+'-1').data('cropper');
			    var imageData = $('#img-crop-'+typ+'-1').cropper('getCroppedCanvas').toDataURL();
			    $('#avatar img').attr('src', imageData);
			    $('#modal-'+typ).modal('hide');
    		})
    		$("#modal-cancel-"+typ+"").click(function(){
    			$("#user_"+typ+"_x").val($("#user_"+typ+"_x").attr('v'));
				$("#user_"+typ+"_y").val($("#user_"+typ+"_y").attr('v'));
				$("#user_"+typ+"_h").val($("#user_"+typ+"_h").attr('v'));
				$("#user_"+typ+"_w").val($("#user_"+typ+"_w").attr('v'));
				$('#modal-'+typ).modal('hide');
    		});
    		$('.progress-avatar > .progress-bar').css('width', 100+'%').attr('aria-valuenow', 100).parent().slideUp(2000);
        },
		error: function (xhr, ao, e){   
			$('.progress-avatar > .progress-bar').css('width', 100+'%').attr('aria-valuenow', 100).parent().fadeOut(2000);
	        console.log(xhr.status+', Fehler: '+e+'');
        	//some errors returning by server-side
			feImagesTip['text']+='<b>'+xhr.status+'</b> – Fehler: '+e+'<br>';//+ $(xhr.responseText).find(".block-exception");
			//add tip for errors from server
			if ($('#ctrl-up-'+typ+'').is(':visible'))
				$('#ctrl-up-'+typ+'').qtip(qtImg).qtip('show');
			else
				 $("#change-"+typ+"-1").qtip(qtImg).qtip('show');
	    }
    }; 
}