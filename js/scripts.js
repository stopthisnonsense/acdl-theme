//Place your JavaScript of jQuery snippets here. Remember to remove the <script> tags
 jQuery( document ).ready(function() {
		var fontSize = restore() || 16;
			jQuery('.letter-small').click(function () {
				fontSize = 18;
				setFontSize(fontSize);
			});

			jQuery('.letter-medium').click(function () {
				fontSize = 20;
				setFontSize(fontSize);
			});

			jQuery('.letter-large').click(function () {
				fontSize = 22;
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
				jQuery('p').css('fontSize', fontSize + 'px');
				save(fontSize);
			}
});