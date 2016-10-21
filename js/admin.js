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
	
	//Quiz-Question
	initQQ();
	
	
		
	
	//hidden answers on form should not to be as required
//	$('.answers-group').data('custom',{
//		equals: function($el) {
//			var index = $el.data("index") // 1-8
//			if ($('#question_answercount').val() > index) {
//				return "Hey, that's not valid! It's gotta be " + index
//			}
//		}
//	})
	
}

function initQQ(){
	$('.btn-qq').click(function(){
		var act='rem';
		var btn=$(this);
		btn.removeClass('btn-danger');
		if(btn.hasClass('btn-default')) act='rem'; else act='add';
		toggle(btn);
        $.ajax({
             type: "POST",
             url: btn.data("path")+"?act="+act+"&id1="+btn.data("id1")+"&id2=" + btn.data("id2"), 
             success: function(data) {
            	 //toggle(btn);
             },
             error: function (r, s, err) {
            	 toggle(btn);
            	 btn.addClass('btn-danger');
            	 console.log(err); 
             }
         });
        return false;
    });
	
	function toggle(btn){
 		if(btn.hasClass('btn-default')) {
     		btn.removeClass('btn-default');
     		btn.addClass('btn-primary');
     		btn.text('Zuordnen');//Lesen
     	}else{
     		btn.removeClass('btn-primary');
     		btn.addClass('btn-default');
     		btn.text('Aus');//Unlesen
     	}
 	}
	
	//quiz-questions
	$('#btn-qq-all').on("click", function() { 
		$(this).addClass('active');
	    $(this).siblings().removeClass('active');
	    $('#panel-qq-new').fadeOut('slow').promise().done(function() {
	    	$('#panel-qq-all').fadeIn('slow');
	    	$('.btn-qq.btn-primary').parents("tr").fadeIn();
		    $('.btn-qq.btn-default').parents("tr").fadeIn();
	    });
	});
	$('#btn-qq-new').on("click", function() { 
		$(this).addClass('active');
	    $(this).siblings().removeClass('active');
	    $('#panel-qq-all').fadeOut('slow').promise().done(function() {
	    	$('#panel-qq-new').fadeIn('slow');
	    });
	});
	
	$("#btn-qq-filter-assigned").on("click", function() { 
		$('.btn-qq.btn-default').parents("tr").fadeOut();
		$('.btn-qq.btn-primary').parents("tr").fadeIn();
	})
	$("#btn-qq-filter-unassigned").on("click", function() { 
		$('.btn-qq.btn-default').parents("tr").fadeIn();
		$('.btn-qq.btn-primary').parents("tr").fadeOut();
	})
	
	//All assign
	$("#btn-qq-assign").on("click", function() {
		toggleAssign('.btn-qq.btn-qq.btn-primary','addall');
	})
	//All assign
	$("#btn-qq-unassign").on("click", function() {
		toggleAssign('.btn-qq.btn-qq.btn-default','remall');
	})
	
	function toggleAssign(styl,typ){
		if (!$(styl).length) return;
		var ids=$(styl).map(function() {
            return $(this).data('id2');
        }).get();
		$(styl).each(function() {
			toggle($(this));
        });
		$.ajax({
            type: "POST",
            url: $('#panel-qq-all').data("path")+"?act="+typ+"&id1="+$('#panel-qq-all').data("id1")+"&id2=" + ids.toString(), 
            success: function(data) {
           	 //toggle(btn);
            },
            error: function (r, s, err) {
            	$(styl).each(function() {
        			toggle($(this));
                });
            	console.log(err); 
            }
        });
	}
}


