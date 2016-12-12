/**
 *
 */

$(document).ready(function() {
	initA();
})

function initA(){
	$.fn.validator.Constructor.INPUT_SELECTOR = ':input:not([type="hidden"], [type="submit"], [type="reset"], button, .select2-search__field)';
	//init radio / checkbox
	$.fn.bootstrapSwitch.defaults.onText = '.';
	$.fn.bootstrapSwitch.defaults.offText = '.';
	$.fn.bootstrapSwitch.defaults.onColor = 'success';
	$.fn.bootstrapSwitch.defaults.offColor = 'default';
	$.fn.bootstrapSwitch.defaults.size = 'mini';
	$(":radio, :checkbox").bootstrapSwitch();

	//range change
	$('.rangeslider').on('change', function() {
		$('#'+$(this).attr('id')+'Info').html($(this).val());
	})

	//init select
	$('.selectpicker').selectpicker({
		style: 'btn btn-default',
		size: 10,
	});

	//init datetime picker
	$('.datetimepicker').datetimepicker({
			locale: 'de',
			format: 'DD.MM.YYYY HH:mm'
	});
	$('.datepicker').datetimepicker({
			locale: 'de',
			format: 'DD.MM.YYYY'
	});
	$('.timepicker').datetimepicker({
			locale: 'de',
			format: 'HH:mm'
	});
	//init select2
	$(".select2").select2({
		tags: true,
		tokenSeparators: [',']
	});

	$('.select2').on("select2:unselect", function(e) {
		$(e.params.data).show();
		if (!e.params.originalEvent) {
			return
		}
		$('.select2-search__field').on('focusout', function(e){
		e.stopPropagation();
		})
		e.params.originalEvent.stopPropagation();
	});

	$('.select2').on("select2:open", function(e) {
		$('.select2-results li[aria-selected="true"]').hide();
	});

	//init share
	if($('input[id="quiz_sharehs_0"]').is(':checked') || $('input[id="quiz_shareanalysis_0"]').is(':checked') || $('input[id="quiz_sharereward_0"]').is(':checked')) {
		//$('input[name="quiz[share]"]').bootstrapSwitch('state', true, true);
		$('.form-group-share').slideDown();
	}else{
		//$('input[name="quiz[share]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[sharehs]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[shareanalysis]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[sharereward]"]').bootstrapSwitch('state', true, false);
		$('.form-group-share').slideUp();
	}
	$('input[id="quiz_share_1"]').on('switchChange.bootstrapSwitch', function(event, state) {
		if (state){
			$('input[name="quiz[sharehs]"]').bootstrapSwitch('state', true, false);
			$('input[name="quiz[shareanalysis]"]').bootstrapSwitch('state', true, false);
			$('input[name="quiz[sharereward]"]').bootstrapSwitch('state', true, false);
			$('.form-group-share').slideUp();
		}

	})
	$('input[id="quiz_share_0"]').on('switchChange.bootstrapSwitch', function(event, state) {
		if (state){
			$('.form-group-share').slideDown();
		}
	})
	//init joker
	if($('input[id="quiz_joker5050_0"]').is(':checked') || $('input[id="quiz_jokerpause_0"]').is(':checked') || $('input[id="quiz_jokerskip_0"]').is(':checked')) {
		//$('input[name="quiz[joker]"]').bootstrapSwitch('state', true, true);
		$('.form-group-joker').slideDown();
	}else{
		//$('input[name="quiz[joker]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[joker5050]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[jokerpause]"]').bootstrapSwitch('state', true, false);
		$('input[name="quiz[jokerskip]"]').bootstrapSwitch('state', true, false);
		$('.form-group-joker').slideUp();
	}
	$('input[id="quiz_joker_1"]').on('switchChange.bootstrapSwitch', function(event, state) {
		if (state){
			$('input[name="quiz[joker5050]"]').bootstrapSwitch('state', true, false);
			$('input[name="quiz[jokerpause]"]').bootstrapSwitch('state', true, false);
			$('input[name="quiz[jokerskip]"]').bootstrapSwitch('state', true, false);
			$('.form-group-joker').slideUp();
		}

	})
	$('input[id="quiz_joker_0"]').on('switchChange.bootstrapSwitch', function(event, state) {
		if (state){
			$('.form-group-joker').slideDown();
		}
	})


	//init button clicks in the view
	$('.btn-del').on('click', function(){
		var thiz=$(this);
		bootbox.confirm({
			message: "Bist du sicher, dass du dieses Element wirklich löschen willst: NEIN | JA?",
			buttons: {
				confirm: {
					label: 'Ja',
					className: 'btn-success'
				},
				cancel: {
					label: 'Nein',
					className: 'btn-danger'
				}
			},
			size:'small',
			callback: function (result) {
				if (result){
					location.href = thiz.data('href');
				}
			}
		});
	})


	//init tooltips
	//$('[data-toggle="tooltip"]').tooltip();
	//CROPPER & AJAX UPLOADER for avatar in user only
	initUserImgUp("avatar", {aspectRatio:1/1, minContainerWidth:200, minContainerHeight:200, minCropBoxWidth:10});

	//CROPPER & AJAX UPLOADER for avatar in question only
	initQuestionImgUp("avatar", {aspectRatio:NaN, minContainerWidth:200, minContainerHeight:200, minCropBoxWidth:10});

	//Quiz-Question
	initQQ();


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
	$('#modal-password').on('show.bs.modal', function (event) {
		var b = $(event.relatedTarget) // Button that triggered the modal
		$(this).find('#u').val(b.data('username'))
	})


	//ANSWERS VALIDATION
	//$('.answers-group > .list-group-item input').removeAttr('required');
	$('.answers-group > .list-group-item:visible textarea').prop('required','required');
	//answers count toggle
	$('.answers-count').on("click", function() {
		$(this).addClass('active');
		$(this).siblings().removeClass('active');
		$('.answers-group > .list-group-item:gt(-7)').hide()
		$('.answers-group > .list-group-item:gt(-7) textarea').removeAttr('required')
		$('.answers-group > .list-group-item:lt('+$(this).text()+')').show();
		$('.answers-group > .list-group-item:lt('+$(this).text()+') textarea').prop('required','required');
		$('#question_answercount').val($(this).text());
		$('form').validator('update');
	});

	//hidden answers on form should not to be as required
	$('#f-admin-question').validator({
		custom: {
			'rightone': function($el) {
				var count = $el.val(); // 1-8
				var brightone = false;
				for ( var i = 0; i < count; i++ ) {
					//if($('input[name="question[answers]['+i+'][status]"]').bootstrapSwitch('state')) return false;
					if($('input[id="question_answers_'+i+'_status_0"]').is(':checked')) return false;
				}
				return true;
			}
		}
	});

	for ( var i = 0; i < 8; i++ ) {
		$('input[name="question[answers]['+i+'][status]').on('switchChange.bootstrapSwitch', function(event, state) {
			$('form').validator('update');
			$('form').validator('validate');
		})
	}

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
				btn.addClass('btn-success');
				btn.text('Zuordnen');//Lesen
			}else{
				btn.removeClass('btn-success');
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
			$('.btn-qq.btn-success').parents("tr").fadeIn();
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
		$('.btn-qq.btn-success').parents("tr").fadeIn();
	})
	$("#btn-qq-filter-unassigned").on("click", function() {
		$('.btn-qq.btn-default').parents("tr").fadeIn();
		$('.btn-qq.btn-success').parents("tr").fadeOut();
	})

	//All assign
	$("#btn-qq-assign").on("click", function() {
		toggleAssign('.btn-qq.btn-qq.btn-success','addall');
	})
	//All assign
	$("#btn-qq-unassign").on("click", function() {
		toggleAssign('.btn-qq.btn-qq.btn-default','remall');
	})

	function toggleAssign(styl,typ){
		if (!$(styl).length) return;
		var ids=$(styl)
				.map(function() {
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


	//Quicksearch for all by typing
	if ($('.btn-search').length){
		var list = $('.table');
		$(".form-search")
			.change( function () {
			var filter = $(this).val();
			if(filter) {
				$(list).find(".filter:not(:contains(" + filter + "))").closest("tr").hide();
				$(list).find(".filter:contains(" + filter + ")").closest("tr").show();
			} else {
				$(list).find("tr").show();
			}
			return false;
			})
			.keyup( function () {
			$(this).change();
		});
	}

	//Quicksearch for question by select
	$('.search-quiz').on('changed.bs.select', function (e) {
		var list = $('.table');
		var filters = $('option:selected', this).data('cats').split(",");
		if(filters && filters[0]!=='') {
			$(list).find("tr").hide();
			$.each(filters,function(i,v){
				$(list).find(".filter:equals(" + v + ")").closest("tr").show();
			});
		} else {
			$(list).find("tr").show();
		}
		var question_count = $(list).find("tr:visible"); 
		$('#question_count_all').text(question_count.length + ' ingesamt');
		$('#question_count_active').text($(question_count).find('span:equals("aktiv")').length + ' aktiv');
	});
}


