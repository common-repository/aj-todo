jQuery(document).ready(function($){
	$.notifyDefaults({
		type : "gray",
		allow_dismiss: false,
		placement: {
			from: "bottom",
			align: "right"
	},
	template: '<div data-notify="container" id="ajtodo_alert" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
		'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
		'<span data-notify="icon"></span> ' +
		'<span data-notify="title">{1}</span> ' +
		'<span data-notify="message" class="ajtodo_alert_msg">{2}</span>' +
		'<div class="progress" data-notify="progressbar">' +
		'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
		'</div>' +
		'<a href="{3}" target="{4}" data-notify="url"></a>' +
	'</div>' 
	});
});

function onlyAlphaNumber(val){
	if (!/^[a-z0-9_]+$/i.test(val)) {
		return false;
	}
	return true;
}