var signatures = [];

//window.onresize = sigFieldsResize;

document.addEventListener("DOMContentLoaded", function(){

	var wrappers = document.querySelectorAll(".wpcf7-form-control-signature-global-wrap");
	Array.prototype.forEach.call(wrappers, function(wrapper, i){

		var canvas = wrapper.querySelector("canvas");
		var clear = wrapper.querySelector("input[type=button]");
		var submit = document.querySelector("input.wpcf7-submit");

		var id = wrapper.getAttribute("data-field-id");
		var input = document.getElementById("wpcf7_input_" + id);

		// Canvas init
		var signature = new SignaturePad(canvas);

		// Push field elements into global var
		signatures.push({signature: signature, input: input, canvas: canvas});

		// Clear event listener
		clear.addEventListener("click", function(){
			sigFieldClear(i);
		});

		// Submit Event Listener
		submit.addEventListener("click", function(){
			if (!signature.isEmpty()){
				input.value = signature.toDataURL();
			}else{
				input.value = "";
			}
		}, false);
		
		// Prepare for resize
		sigFieldResize(i);
	});
});

// Clear a single signature field
function sigFieldClear(index){

	signatures[index].signature.clear();
	signatures[index].input.value= "";
}

// Dealing with window size and device ratio
function sigFieldResize(index){

	var canvas = signatures[index].canvas;

	var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

    sigFieldClear(index);
}

// Global resize management
function sigFieldsResize(){

	var elements = document.querySelectorAll(".wpcf7-form-control-signature-input-wrap");
	Array.prototype.forEach.call(elements, function(el, i){

		sigFieldResize(i);

	});
}

// Globally clear fields on form submit
function sigFieldsClear(){
	
	var elements = document.querySelectorAll(".wpcf7-form-control-signature-input-wrap");
	Array.prototype.forEach.call(elements, function(el, i){
		
		sigFieldClear(i);

	});
}
