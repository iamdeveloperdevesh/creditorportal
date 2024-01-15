

<style>
    label {
        display: inline-block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
</style>

<div class="container my-5">
    <div class="row">

        <div class="col-sm-5" >
            <label>Enter Lead ID</label>
            <input class="form-control" type="number" id="lead_id" name="lead_id" placeholder="Enter Lead ID...">

        </div>
        <div class="col-sm-5" >
            <label>Enter Trace ID </label>
            <input class="form-control" type="number" id="trace_id" name="trace_id" placeholder="Enter Trace ID...">

        </div>
        <div class="col-sm-2 mt-1">
           <button class="btn btn-success mt-4" id="searchBtn" onclick="SearchDetails()"  >Search</button>
        </div>
    </div>
    <div class="row" id="detailsData">

    </div>
    <!--<div class="">
        <img src="/assets/gadget/img/vector1.png" class="vector-img">
    </div>-->
</div>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    function SearchDetails() {
        $("#detailsData").html("");
        var lead_id=$("#lead_id").val();
        var trace_id=$("#trace_id").val();
        if(trace_id == "" || lead_id== "" ){
            $("#detailsData").html("<div class='col-md-12 mt-5'><span style='color:red'>Trace Id and Lead Id is Compulsory.</span></div>");
        }
        $.ajax({

            url: '/GadgetInsurance/SearchDetails',
            method: 'POST',
            data: {lead_id,trace_id},
            async: false,
            cache: false,
            dataType:'json',
            success: function(response) {
            if(response == 201){
                $("#detailsData").html("<div class='col-md-12 mt-5'><p>No Data Found.</p></div>");
                return;
            }
                var certificate_number=response.certificate_number;
              if(certificate_number == null){
                  certificate_number ='Not generated yet.'
              }else{
                  certificate_number=certificate_number;
              }
                var html= '<div class="col-md-6 mt-5"><p><label>Date :</label> '+response.created_at+'</p>' +
                    '<p><label>First Name :</label> '+response.first_name+'</p>' +
                    '<p><label>Last Name :</label> '+response.last_name+'</p>' +
                    '<p><label>Email_id :</label> '+response.email_id+'</p>' +
                    '<p><label>Proposal Number :</label> '+response.proposal_no+'</p>' +
                    '</div>' +
                    '<div class="col-md-6 mt-5"><p><label>Trace Id :</label> '+response.trace_id+'</p>' +
                    '<p><label>Status :</label> '+response.status+'</p>' +
                    '<p><label>Sum Insure :</label> '+response.cover+'</p>' +
                    '<p><label>Premium :</label> '+response.premium+'</p>' +
                    '<p><label>Policy Number :</label> '+certificate_number+'</p></div>';

                $("#detailsData").html(html);
            }
        });
    }
   </script>
</body>
</html>
