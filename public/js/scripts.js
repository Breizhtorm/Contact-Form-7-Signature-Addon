var signatures = [];

(function($) {

	'use strict';

	// Main function to initialize signature fields
	$.fn.wpcf7InitSignatures = function() {

		signatures = [];

		return this.each(function(i, form){

			$(form).find('.wpcf7-form-control-signature-global-wrap').each(function(j, wrapper){
				var $canvas = $(wrapper).find('canvas'), 
					$clear = $(wrapper).find('input[type=button]'),
					id = $(wrapper).data('field-id'),
					input = document.getElementById("wpcf7_input_" + id),
					canvas = $canvas[0];

				sigFieldRatio(canvas);

				// Options
				var options = [];
				options['penColor'] = $canvas.data("color");
				options['backgroundColor'] = $canvas.data("background");

				// Extra options
				var extras = $canvas.data("extras");
				if (extras != null && typeof(extras) != 'undefined'){
					for (var key in extras) {
						if (extras.hasOwnProperty(key)) {
							options[key] = extras[key];
						}
					}
				}

				// Canvas init
				var signature = new SignaturePad(canvas, options);

				// Push field elements into global variable
				var sigObj = new Wpcf7Signature(signature, canvas, input, options);
				signatures.push(sigObj);

				sigObj.setValue();

				// Clear event listener
				$clear.on("click", function(){
					sigObj.clear();
				});

				// Trigger change event on input field when signature changed
				$clear.on("mouseup", function(){
					sigObj.change();
				});

				// Submit Event Listener
				$(form).on('submit', function(){
					sigObj.beforeSubmit();
				});

			});

		});
	}

	// Resize canvas fields
	$.fn.wpcf7ResizeSignatures = function(){

		$(".wpcf7-form-control-signature-input-wrap").each(function(i){
			signatures[i].resize();
		});

		return this;
	}

	// Globally clear fields (on form submit for exemple)
	$.fn.wpcf7ClearSignatures = function(){
		
		$(".wpcf7-form-control-signature-input-wrap").each(function(i){
			signatures[i].clear();
		});

		return this;
	}

	$(function() {
		$('div.wpcf7 > form').wpcf7InitSignatures();
	});

	// CF7 v4.9 callbacks
	$(document).on( 'wpcf7mailsent', function( event ) {

		// Clearing signatures
		$('div.wpcf7 > form').wpcf7ClearSignatures();
	});

})(jQuery);


var Wpcf7Signature = (function() {
	var signature, canvas, input, options;

	function Wpcf7Signature(signature, canvas, input, options){
		this.signature = signature;
		this.canvas = canvas;
		this.input = input;
		this.options = options;
	}

	// Set Canvas value if needed
	Wpcf7Signature.prototype.setValue = function() {
		
		if(this.input.value != ''){
			this.signature.fromDataURL(this.input.value);
		}
	}

	// Trigger Change event
	Wpcf7Signature.prototype.change = function() {

		this.beforeSubmit();
		
		if (document.createEvent) {
			var changeEvent = document.createEvent("HTMLEvents");
		    changeEvent.initEvent("change", false, true);
		    this.input.dispatchEvent(changeEvent);
		} else {
			this.input.fireEvent("onchange");
		}
	}

	// Copy sig value to input field
	Wpcf7Signature.prototype.beforeSubmit = function() {
		if (!this.signature.isEmpty()){
			this.input.value = this.signature.toDataURL();
		}else{
			this.input.value = "";
		}
	};

	// Clear a single signature field
	Wpcf7Signature.prototype.clear = function() {
		this.signature.clear();
		this.input.value = "";
	};

	// Dealing with window size and device ratio
	Wpcf7Signature.prototype.resize = function(clear) {
		sigFieldRatio(this.canvas);

		if (clear){
			this.sigFieldClear();
		}
	};

	return Wpcf7Signature;
})();

// Set field ratio
function sigFieldRatio( canvas ) {

	var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
};