(function($) {

	'use strict';

	var signatures = [];

	// Main function to initialize signature fields
	$.fn.wpcf7InitSignatures = function() {

		var submit = this.find('input.wpcf7-submit')[0];

		this.find('.wpcf7-form-control-signature-global-wrap').each(function(i){
			var $canvas = $(this).find('canvas'), 
				$clear = $(this).find('input[type=button]'),
				id = $(this).data('field-id'),
				input = document.getElementById("wpcf7_input_" + id),
				canvas = $canvas[0];

			sigFieldRatio(canvas);

			// Options
			var options = [];
			options['penColor'] = $canvas.data("color");
			options['backgroundColor'] = $canvas.data("background");

			// Canvas init
			var signature = new SignaturePad(canvas, options);

			// Push field elements into global variable
			signatures[i] = {
				signature: signature, 
				input: input, 
				canvas: canvas, 
				options: options
			};

			sigFieldSetValue(i);

			// Clear event listener
			$clear.on("click", function(){
				sigFieldClear(i);
			});

			// Trigger change event on input field when signature changed
			$clear.on("mouseup", function(){
				sigFieldChange(i);
			});

			// Submit Event Listener
			if (submit != null && typeof(submit) != 'undefined'){
				submit.addEventListener("click", function(){
					sigFieldBeforeSubmit(i);
				}, false);
			}

		});

		return this;
	}


	// Resize canvas fields
	$.fn.sigFieldsResize = function(){

		$(".wpcf7-form-control-signature-input-wrap").each(function(i){
			sigFieldResize(i, true);
		});

		return this;
	}

	// Globally clear fields (on form submit for exemple)
	$.fn.sigFieldsClear = function(){
		
		$(".wpcf7-form-control-signature-input-wrap").each(function(i){
			sigFieldClear(i);
		});

		return this;
	}

	// Set field ratio
    function sigFieldRatio( canvas ) {

    	var ratio =  Math.max(window.devicePixelRatio || 1, 1);
	    canvas.width = canvas.offsetWidth * ratio;
	    canvas.height = canvas.offsetHeight * ratio;
	    canvas.getContext("2d").scale(ratio, ratio);
    };

	// Set Canvas value if needed
	function sigFieldSetValue( index ){
		
		if(signatures[index].input.value != ''){
			signatures[index].signature.fromDataURL(signatures[index].input.value);
		}
	}

	// Trigger Change event
	function sigFieldChange( index ){

		sigFieldBeforeSubmit(index);
		
		if (document.createEvent) {
			var changeEvent = document.createEvent("HTMLEvents");
		    changeEvent.initEvent("change", false, true);
		    signatures[index].input.dispatchEvent(changeEvent);
		} else {
			signatures[index].input.fireEvent("onchange");
		}
	}

	// Copy sig value to input field
	function sigFieldBeforeSubmit( index ){

		if (!signatures[index].signature.isEmpty()){
			signatures[index].input.value = signatures[index].signature.toDataURL();
		}else{
			signatures[index].input.value = "";
		}
	}

	// Clear a single signature field
	function sigFieldClear( index ){

		signatures[index].signature.clear();
		signatures[index].input.value= "";
	}

	// Dealing with window size and device ratio
	function sigFieldResize( index, clear ){

		var canvas = signatures[index].canvas;

		sigFieldRatio(canvas);

	    if (clear){
	    	sigFieldClear(index);
	    }
	}

	$(function() {
		$('div.wpcf7 > form').wpcf7InitSignatures();
	});

})(jQuery);
