Pace.options = {
    ajax: false
}
function no_photo (object,url) {
	object.src = url;
	object.onerror = "";
	return true;
}