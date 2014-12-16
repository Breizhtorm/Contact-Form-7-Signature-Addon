document.addEventListener('DOMContentLoaded', function(){

    var canvas = document.querySelector("#wpcf7_signature");
    var signaturePad = new SignaturePad(canvas);

    document.getElementById("wpcf7_signature_clear").addEventListener("click", function(){
        signaturePad.clear();
    })

    var input_id = canvas.getAttribute("class");
    var input = document.getElementById(input_id);
    var form = input.form;

    var submit = document.querySelector("input.wpcf7-submit");
    submit.onclick = function(){
        if (!signaturePad.isEmpty()){
            input.value = signaturePad.toDataURL();
        }
        else{
            input.value = "";
        }
    }

});


