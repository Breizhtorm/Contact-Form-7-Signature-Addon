var signatures = [];

//window.onresize = sigFieldsResize;

document.addEventListener("DOMContentLoaded", function(){

	var forms = document.querySelectorAll("form.wpcf7-form");
	Array.prototype.forEach.call(forms, function(form, i){

		var wrappers = document.querySelectorAll(".wpcf7-form-control-signature-global-wrap");
		Array.prototype.forEach.call(wrappers, function(wrapper, i){

			var canvas = wrapper.querySelector("canvas");
			var clear = wrapper.querySelector("input[type=button]");
			var submit = form.querySelector("input.wpcf7-submit");

			var id = wrapper.getAttribute("data-field-id");
			var input = document.getElementById("wpcf7_input_" + id);

			// Canvas init
			var signature = new SignaturePad(canvas);

			// Push field elements into global var
			signatures.push({signature: signature, input: input, canvas: canvas});

			sigFieldInit(i);

			// Clear event listener
			clear.addEventListener("click", function(){
				sigFieldClear(i);
			});

			// Trigger change event on input field when signature changed
			canvas.addEventListener("mouseup", function(){
				sigFieldChange(i);
			});

			// Submit Event Listener
			submit.addEventListener("click", function(){
				sigFieldBeforeSubmit(i);
			}, false);

			// Resize field (for pixel ratio issues)
			sigFieldResize(i, false);
		});
	});
});

// Init Canvas value if needed
function sigFieldInit(index){
	
	if(signatures[index].input.value != ''){
		signatures[index].signature.fromDataURL(signatures[index].input.value);
	}
}

// Trigger Change event
function sigFieldChange(index){

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
function sigFieldBeforeSubmit(index){
	if (!signatures[index].signature.isEmpty()){
		signatures[index].input.value = signatures[index].signature.toDataURL();
	}else{
		signatures[index].input.value = "";
	}
}

// Clear a single signature field
function sigFieldClear(index){
	
	signatures[index].signature.clear();
	signatures[index].input.value= "";
}

// Dealing with window size and device ratio
function sigFieldResize(index, clear){

	var canvas = signatures[index].canvas;

	var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

    if (clear){
    	sigFieldClear(index);
    }
}

// Global resize management
function sigFieldsResize(){

	var elements = document.querySelectorAll(".wpcf7-form-control-signature-input-wrap");
	Array.prototype.forEach.call(elements, function(el, i){

		sigFieldResize(i, true);

	});
}

// Globally clear fields on form submit
function sigFieldsClear(){
	
	var elements = document.querySelectorAll(".wpcf7-form-control-signature-input-wrap");
	Array.prototype.forEach.call(elements, function(el, i){
		
		sigFieldClear(i);

	});
}
