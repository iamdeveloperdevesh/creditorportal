$(document).ready(function() {

    setTimeout(function() {
        $("#policy_no").change();
    }, 2000);

    // $.ajax({
    // url: "/get_network_hospitals",
    // type: "POST",
    // async: false,
    // dataType: "json",
    // success: function (response) {
    // }
    // });  
    $.ajax({
        url: "/employer/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        success: function(response) {
            $('#policy_no').empty();
            $('#policy_no').append('<option value=""> Select policy type</option>');
            for (i = 0; i < response.length; i++) {
                var date = response[i].end_date.split("-");
                var date = new Date(Number(date[0]), Number(date[1]) - 1, Number(date[2]));
                var current_date = new Date();
                if (date > current_date) {
                    if (response[i].policy_sub_type_id == 1) {
                        $('#policy_no').append('<option selected value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
                    }
                }
            }
        }
    });

    // hospital name on policy
    $("#policy_no").change(function() {
        var availableTags = [];
        var value = $(this).val();
        $.ajax({
            type: 'POST',
            url: '/get_hospital_name',
            data: {
                policy_no: value
            },
            async: false,
            success: function(res) {
                //console.log(res); return false;
                var res_data = JSON.parse(res);
                $.each(res_data, function(index, value) {
                    $('#state_names').append('<option value="' + value['STATE_NAME'] + '">' + value['STATE_NAME'] + '</option>');
                });
            }
        });
		
		$.ajax({
            type: 'POST',
            url: '/get_hospital_name',
            data: {
                policy_no: value,
				all_hospitals: true
            },
            async: false,
            success: function(res) {
                //console.log(res); return false;
                var res_data = JSON.parse(res);
                $.each(res_data, function(index, value) {
                    availableTags.push(value['HOSPITAL_NAME']);
                });
                $("#search_hopsitals").autocomplete({
                    source: availableTags
                });
            }
        });
		
        /*   $.ajax({
             type: 'POST',
             url: '/get_states_from_policy_no',
             data : {policy_no:value},
             success:function(res){
                  var response = JSON.parse(res);
                $('#state_names').empty();
                $('#state_names').append('<option value=""> Select state </option>');
                 for (i = 0; i < response.length; i++) { 
                     $('#state_names').append('<option value="' + response[i].hospital_state + '">' + response[i].hospital_state + '</option>');
                 }
             }
         }); */
    });
    $('.show_map').html("");
    var availableTags = [];
    $('#state_names').on('change', function() {
        $.ajax({
            url: "/get_hospital_name",
            type: "POST",
            data: {
                "state_names": this.value,
                "policy_no": $("#policy_no").val()
            },
            async: false,
            dataType: "json",
            success: function(response) {

                $('#cities').empty();
                $('#cities').append('<option>Select City</option>');
                for (i = 0; i < response.length; i++) {
                    $('#cities').append('<option value="' + response[i].CITY_NAME + '">' + response[i].CITY_NAME + '</option>');
                }
            }
        });
    })
    $('#cities').on('change', function() {
        $.ajax({
            url: "/get_hospital_name",
            type: "POST",
            data: {
                "state_name": $("#state_names option:selected").text(),
                "city_names": $("#cities option:selected").text(),
                "policy_no": $("#policy_no").val()
            },
            async: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $('.show_map').empty();
                for (i = 0; i < response.length; i++) {
                    $('.show_map').append('<div class="col-md-12 mt-2 "> <div class="card"> <div class="card-body card-style"> <div class="row"> <div class="col-md-1"><img src="/public/assets/images/new-icons/meeting-point.png" style="width:25px !important;"></div> <div class="col-md-11"><span class="bold-font"  id=' + response[i].network_hospital_id + '>' + (response[i].HOSPITAL_NAME) + " " + (response[i].ADDRESS1) + '</span><br><span class = "bold-font" >Address2- ' + (response[i].ADDRESS2) + '<br class = bold-font>CITY- ' + (response[i].CITY_NAME) + '<br class = "bold-font">STATE -' + (response[i].STATE_NAME) + '</span><br><span class ="bold-font contact">Contact-no ' + response[i].PHONE_NO + '</span></div>  <div class="col-md-1"></div> <div class="col-md-3 mt-2"> <div class=""><button type="button" data-id=' + response[i].network_hospital_id + ' id="google_api" class="btn color-4"> Show On Map </button></div> </div> <div class="col-md-3 mt-2"> <div class=""><button  data-id=' + response[i].network_hospital_id + ' id = "sms_address" class="btn color-5 ">SMS Address</button></div> </div><div class="col-md-3 mt-2"> <div class=""><button id ="send_email" data-id=' + response[i].network_hospital_id + ' class="btn color-5 ">Email Address</button></div> </div> </div> </div> </div> </div>');
                }
            }
        });
    });
    //email
    $(document).on("click", "#send_email", function() {
        var id = $(this).attr("data-id");
        var hospital_address = $("#" + id).text();
        var contact_no = $("#" + id).siblings('.contact').html();

        $.ajax({
            url: "/set_network_hospital_mails",
            type: "POST",
            async: false,
            dataType: "json",
            data: {
                "hospital_address": hospital_address,
                "contact_no": contact_no
            },
            success: function(response) {

            }
        });
    });



    $("#search_hopsitals").on("keyup", function(e) {
        if (e.keyCode == 13) {
            $('#search_hopsitals_search').click();
        }

    });


    //
    $('#search_hopsitals_search').on('click', function() {
        $.ajax({
            url: "/employer/get_hospitals_by_name",
            type: "POST",
            data: {
                "hospital_name": $("#search_hopsitals").val()
            },
            async: false,
            dataType: "json",
            success: function(response) {
                $('.show_map').empty();
                for (i = 0; i < response.length; i++) {
                    $('.show_map').append('<div class="col-md-12 mt-2 "> <div class="card"> <div class="card-body card-style"> <div class="row"> <div class="col-md-1"><img src="/public/assets/images/new-icons/meeting-point.png" style="width:25px !important;"></div> <div class="col-md-11"><span class="bold-font"  id=' + response[i].network_hospital_id + '>' + (response[i].HOSPITAL_NAME) + " " + (response[i].ADDRESS1) + '</span><br><span class = "bold-font" >Address2- ' + (response[i].ADDRESS2) + '<br class = bold-font>CITY- ' + (response[i].CITY_NAME) + '<br class = "bold-font">STATE -' + (response[i].STATE_NAME) + '</span><br><span class ="bold-font contact">Contact-no ' + response[i].PHONE_NO + '</span></div>  <div class="col-md-1"></div> <div class="col-md-3 mt-2"> <div class=""><button type="button" data-id=' + response[i].network_hospital_id + ' id="google_api" class="btn color-4"> Show On Map </button></div> </div> <div class="col-md-3 mt-2"> <div class=""><button data-id=' + response[i].network_hospital_id + ' id="sms_address" class="btn color-5 ">SMS Address</button></div> </div><div class="col-md-3 mt-2"> <div class=""><button id ="send_email" class="btn color-5" data-id=' + response[i].network_hospital_id + '>Email Address</button></div> </div> </div> </div> </div> </div>');
                }
            }
        });
    })
    $(document).on("click", "#google_api", function() {
        var id = $(this).attr("data-id");
        var hospital_address = $("#" + id).text();




        var geocoder = new google.maps.Geocoder();
        var address = "new york";

        geocoder.geocode({
            'address': hospital_address
        }, function(results, status) {

            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                alert(latitude);
            }
        });
        return false;

        var output_from_api = "https://maps.googleapis.com/maps/api/geocode/json?address=" + hospital_address + "&key=AIzaSyBcXRASvfomduCvUgJV2JAhFWrw79hf0nc"
        console.log(output_from_api);
        $.get(output_from_api, function(e) {
            console.log(e['results'][0]['geometry']['location'].constructor());
            var lattitude = e['results'][0]['geometry']['location']['lat'];
            var longitude = e['results'][0]['geometry']['location']['lng'];
            sessionStorage.setItem("lattitude", lattitude);
            sessionStorage.setItem("longitude", longitude);
            var uluru = {
                lat: lattitude,
                lng: longitude
            };
            var map = new google.maps.Map(
                document.getElementById('google_map'), {
                    zoom: 5,
                    center: uluru
                });
            var marker = new google.maps.Marker({
                position: uluru,
                map: map
            });
        });
    });

    //sms
    $(document).on("click", "#sms_address", function() {

        var id = $(this).attr("data-id");
        var hospital_address = $("#" + id).text();

        //send sms
        $.ajax({
            url: "/employee/network_hospital_sms",
            type: "POST",
            data: {

                "address": hospital_address
            },
            async: false,
            dataType: "json",
            success: function(response) {


            }

        });




    })

});