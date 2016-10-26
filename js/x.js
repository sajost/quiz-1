/**
 *
 */

$(document).ready(function() {
	initX();
})

function initX(){
	//init radio / checkbox
	$.fn.bootstrapSwitch.defaults.onText = '.';
	$.fn.bootstrapSwitch.defaults.offText = '.';
	$.fn.bootstrapSwitch.defaults.onColor = 'success';
	$.fn.bootstrapSwitch.defaults.offColor = 'default';
	$.fn.bootstrapSwitch.defaults.size = 'mini';
	$(":radio, :checkbox").bootstrapSwitch();

	$('input[name="form[answers]').on('switchChange.bootstrapSwitch', function(event, state) {
		$('form').validator('update');
		$('form').validator('validate');
		$('#form_answers').fadeOut(1000, function() {
			$('#spinner-next').fadeIn();
			$('form#f-question').submit();
		});
	})
}
