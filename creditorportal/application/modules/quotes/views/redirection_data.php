<!DOCTYPE html>
<html>

<head>
    <title>Redirection NEW D2C API</title>
    <script src="/public/js_events/jquery-3.6.0.min.js"></script>
    <style>
        /* Style the buttons */
        .btn {
            border: none;
            outline: none;
            padding: 10px 16px;
            background-color: #f1f1f1;
            cursor: pointer;
            font-size: 18px;
        }

        /* Style the active class, and buttons on mouse-over */
        .active, .btn:hover {
            background-color: #666;
            color: white;
        }
    </style>
</head>

<body>
<form action="/quotes/generateToken" method="post" name="frmGen" id="frmGen">
    <h2 id="top">Genrate Token:</h2>
    <textarea rows="10" cols="70" name="json_data">
{
"username":"sm.user",
"password":123456
}
   </textarea> <br><br>
    <input class="btn active" type="submit" name="generate_redirection_checksum" id="generate_redirection_checksum" value="GenerateToken"/>
<p>Token :  <?php echo $res1['utoken']; ?></p>
<p>UserId :  <?php echo $res1['user_id']; ?></p>
</form>
<form action="/quotes/PolicyIssueApiNew" method="post" name="frmGenPipe" id="frmGenPipe">
    <input type="hidden" name="checksum" value="<?php echo $checksum;?>"/>
    <div style="float:right;"><a class="scrollTo" id="gotobottom" href="#bottom">Go to bottom</a></div>
    <h2 id="top">Fill the detail to get Certificate of Issueance:</h2>
    <textarea rows="35" cols="70" name="json_cust_data">{
    "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjIiLCJpYXQiOjE2NzAwNTc4NDYsImV4cCI6MTY3MDA3NTg0Nn0.rT2Eqqt0Qq1vjiU2sokxg7j5XU1pGtyM1q0vMpOsass",
  "ClientCreation": {
    "partner": "Fyntune Insurance",
	"plan": "TestingNew",
	"salutation": "Mrs",
	"first_name": "Pooja",
	"middle_name": "Alpesh",
	"last_name": "Disawle",
	"gender": "Female",
	"dob": "03-11-2004",
	"email_id": "poojalote123@gmail.com",
	"mobile_number": "8793164535",
	"tenure": "1",
	"is_coapplicant": "No",
	"coapplicant_no": "",
    "userId":"46",
	"sm_location": "Mumbai",
    "alternateMobileNo": null,
    "homeAddressLine1": "Om hrad ntalatiof, holOmshre, talathioficeeachol, OmshreeSada9talathi",
    "homeAddressLine2": null,
    "homeAddressLine3": null
   
  },
  "QuoteRequest": {
    
    "adult_count": "1",
    "child_count": "0",
    "SumInsuredData":
   [{"PlanCode":"4211","SumInsured":"500000","Shortcode":"GHI"},
    {"PlanCode":"4212","SumInsured":"500000","Shortcode":"GPA"},
     {"PlanCode":"4213","SumInsured":"500000","Shortcode":"GCI"}
    ]
    
      },
  "MemObj": {
    "Member": [
      {
        "MemberNo": 1,
        "Salutation": "Mr",
        "First_Name": "praghghkash",
        "Middle_Name": null,
        "Last_Name": "k abhi axis",
        "Gender": "M",
        "DateOfBirth": "1991-06-15",
        "Relation_Code": "1"
      }
    ]
  },
  "ReceiptCreation": {
    "modeOfEntry": "Direct",
    "PaymentMode": "1",
    "bankName": "Axis Bank Limited",
    "branchName": "",
    "bankLocation": null,
    "chequeType": null,
    "ifscCode": null
  
  },
  "Nominee_Detail":{
      "Nominee_First_Name": "gfgh",
        "Nominee_Last_Name": "gfg",
        "Nominee_Contact_Number": "8793164535",
        "Nominee_Home_Address": null,
        "Nominee_gender": "M",
        "Nominee_Salutation": "Mr",
        "Nominee_Email": "pooja@gmail.com",
        "Nominee_Relationship_Code": "R002"
  },
 "PolicyCreationRequest": {
 
    "TransactionNumber": "Pay_kbToSSUXXtt",
    "TransactionRcvdDate": "2011-11-10",
    "PaymentMode": "CD Balance"
  
  }

}</textarea> <br><br>
    <input class="btn active" type="submit" name="generate_redirection_checksum" id="generate_redirection_checksum" value="POST"/>
</form>


<br><br>
-----------------------------------------------------------------------------------------------
<script type="text/javascript">


    $(document).ready(function () {
        $(".scrollTo").on('click', function (e) {
            e.preventDefault();
            var target = $(this).attr('href');
            console.log(target);
            $('html, body').animate({
                scrollTop: ($(target).offset().top)
            }, 500);
        });


        if (typeof($('#frmPost').find('input[name="checksum"]').val()) != 'undefined') {
            if($('#frmPost').find('input[name="checksum"]').val() == "2"){
                var target = '#bottom';
                setPagePosition(target);
                $('#frmGenPipe').find('input[type="submit"]').removeClass('active');
                $('#frmEnc').find('input[type="submit"]').removeClass('active');
                $('#frmPost').find('input[type="submit"]').addClass('active');
            }
        }else if (typeof($('#frmEnc').find('input[name="checksum"]').val()) != 'undefined') {

            if($('#frmEnc').find('input[name="checksum"]').val() == "1"){
                var target = '#middle';
                setPagePosition(target);
                $('#frmGenPipe').find('input[type="submit"]').removeClass('active');
                $('#frmEnc').find('input[type="submit"]').addClass('active');
                $('#frmPost').find('input[type="submit"]').removeClass('active');

            }
        }else if (typeof($('#frmGenPipe').find('input[name="checksum"]').val()) != 'undefined') {
            if($('#frmGenPipe').find('input[name="checksum"]').val() == "0"){
                var target = '#top';
                setPagePosition(target);
                $('#frmGenPipe').find('input[type="submit"]').addClass('active');
                $('#frmEnc').find('input[type="submit"]').removeClass('active');
                $('#frmPost').find('input[type="submit"]').removeClass('active');
            }
        }
    });

    function setPagePosition(target){
        $('html, body').animate({
            scrollTop: ($(target).offset().top)
        }, 500);
    }
</script>
</body>
</html>
