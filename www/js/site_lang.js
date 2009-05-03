$(document).ready(function() {
    $('#site_language_code_select').change(function() {
	window.location.href = '/' + $('#site_language_code_select').val();
    });
});
