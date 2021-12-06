//Place your JavaScript of jQuery snippets here. Remember to remove the <script> tags
jQuery(document).ready(function ($) {
	var fontSize = restore() || 16;
	jQuery('.letter-small').click(function () {
		fontSize = 18;
		jQuery('.font-size-btn--active').toggleClass('font-size-btn--active');
		jQuery(this).toggleClass('font-size-btn--active');
		setFontSize(fontSize);
	});

	jQuery('.letter-medium').click(function () {
		fontSize = 20;
		jQuery('.font-size-btn--active').toggleClass('font-size-btn--active');
		jQuery(this).toggleClass('font-size-btn--active');
		setFontSize(fontSize);
	});

	jQuery('.letter-large').click(function () {
		fontSize = 22;
		jQuery('.font-size-btn--active').toggleClass('font-size-btn--active');
		jQuery(this).toggleClass('font-size-btn--active');
		setFontSize(fontSize);
	});

	function save(fontSize) {
		sessionStorage.setItem('fontSize', fontSize);
	}

	function restore() {
		fontSize = parseInt(sessionStorage.getItem('fontSize'));
		setFontSize(fontSize);
		return fontSize;
	}

	function setFontSize(fontSize) {
		jQuery('body').css('fontSize', fontSize + 'px');
		jQuery('html').css('fontSize', fontSize + 'px');
		save(fontSize);
	}
});
