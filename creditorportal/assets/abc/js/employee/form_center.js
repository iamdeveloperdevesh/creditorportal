$(document).ready(function () {

    var document_type;
    $.ajax({
        url: "/employee/get_all_formcenter_files",
        type: "POST",
        dataType: "json",
        success: function (response) {
          
            // debugger;
			    $("#group_mediclaim").html("");
            $("#group_personal_accident").html("");
            $("#group_term_life").html("");
            $.each(response, function( index, value ) {
                // debugger;
                if(response){
                if(index == 1){
                    $("#1").show();
                    // $("#group_mediclaim").empty();
                   // var z = "group medilclaim";
                    for(i =0; i < value.length; i++){
                    //$("#show_pdf").empty();
                    
              $("#group_mediclaim").append('<div class="col-md-8 mt-2"> <div class="shadow-card form-center-card"> <div class="row"> <div class="col-md-9"> <h6 class="form-center-title"> '+value[i].name+' </h6> </div> <div class="col-md-3"> <a href= "'+value[i].src+'" target="_blank"><img class="image-responsive" src="/public/assets/images/new-icons/pdf (1).png"></a>  <div> </div> </div> </div> </div> </div>'); 
                }
                   // $("#group_mediclaim").prepend('<div>'+z+'<div>');
                    
                }else if(index == 2){
                     $("#2").show();
                     // $("#group_personal_accident").empty();
                    //var z = "group personal accident";
                    for(i =0; i < value.length; i++){
                     $("#group_personal_accident").append('<div class="col-md-8 mt-2"> <div class="shadow-card form-center-card"> <div class="row"> <div class="col-md-9"> <h6 class="form-center-title"> '+value[i].name+' </h6> </div> <div class="col-md-3"> <a href= "'+value[i].src+'" target="_blank"><img class="image-responsive" src="/public/assets/images/new-icons/pdf (1).png"></a>  <div> </div> </div> </div> </div> </div>'); 
                }
                    //$("#group_personal_accident").prepend('<div>'+z+'<div>');
                }else if(index == 3){
                     $("#3").show();
                     // $("#group_term_life").empty();
                     //var z = "group personal accident";
                     for(i =0; i < value.length; i++){
                     $("#group_term_life").append('<div class="col-md-8 mt-2"> <div class="shadow-card form-center-card"> <div class="row"> <div class="col-md-9"> <h6 class="form-center-title"> '+value[i].name+' </h6> </div> <div class="col-md-3"> <a href= "'+value[i].src+'" target="_blank"><img class="image-responsive" src="/public/assets/images/new-icons/pdf (1).png"></a>  <div> </div> </div> </div> </div> </div>'); 
                }
                   // $("#group_term_life").prepend('<div>'+z+'<div>');
                }
                }
          else{
               swal({
                                title: "Warning",
                                text: "No files available",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            })
              
          }
                
});
           // console.log(response);
         
        }
    });
    
});