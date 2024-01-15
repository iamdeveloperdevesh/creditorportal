<!DOCTYPE html>
<html>

<head>
    <title>Redirection API</title>
    <script src="/public/js_events/jquery-3.5.1.js"></script>
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
<form action="/customerportal/save_Proposal_api" method="post" name="frmGenPipe" id="frmGenPipe">

    <div style="float:right;"><a class="scrollTo" id="gotobottom" href="#bottom">Go to bottom</a></div>
    <h2 id="top">Change the data according to use & Submit:</h2>
    <!-- <textarea rows="35" cols="70" name="json_cust_data">{
"salutation": "MR",
"customer_first_name": "Demo",
"customer_last_name": "Person",
"email_address": "demoperson@xyz.com",
"address": "11-BAVA SAMPLE LANE",
"city": "Jaipur",
"pin_code": "302019",
"state": "Rajasthan",
"dob": "1974-02-20",
"gender": "M",
"mobile_number": "9992222551",
"pan_number": "ALXPS0000L"
}</textarea> <br><br> -->
    <textarea rows="35" cols="70" name="json_cust_data">{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjQ2IiwiaWF0IjoxNjg0OTI2OTg1LCJleHAiOjE2ODQ5NDQ5ODV9.Dld4c4jMsQ3yrD-nR0983OgNy_uhrv7fdHfv1vZspMk",
    "ClientCreation": {
        "partner": "TRAKTION SOLUTIONS PRIVATE LIMITED",
        "plan": "MCHI STU",
        "salutation": "Mr",
        "first_name": "Amihtreyrtjt",
        "middle_name": "",
        "last_name": "Mattryani",
        "gender": "Male",
        "dob": "03-11-2004",
        "email_id": "poojalote123@gmail.com",
        "mobile_number": "8787164535",
        "tenure": "1",
        "is_coapplicant": "No",
        "coapplicant_no": "",
        "userId": "46",
        "sm_location": "Mumbai",
        "alternateMobileNo": null,
        "homeAddressLine1": "Om hrad ntalatiof, holOmshre, talathioficeeachol, OmshreeSada9talathi",
        "homeAddressLine2": null,
        "homeAddressLine3": null,
        "pincode": "410209"
    },
    "QuoteRequest": {
        "NoOfLives": "1",
        "adult_count": "1",
        "child_count": "0",
        "LoanDetails": {
            "LoanDisbursementDate": "03-04-2023",
            "LoanAmount": "3000",
            "LoanAccountNo": "898989898",
            "LoanTenure": "1"
        },
        "SumInsuredData": [
            {
                "PlanCode": 4215,
                "SumInsured": "2000000",
                "Shortcode": "TOPUP",
                "Premium": "4504"
            }
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
                "DateOfBirth": "23-11-1991",
                "Relation_Code": "1"
            }
        ]
    },
    "ReceiptCreation": {
        "modeOfEntry": "Direct",
        "PaymentMode": "1",
        "bankName": "",
        "branchName": "",
        "bankLocation": "",
        "chequeType": "",
        "ifscCode": ""
    },
    "Nominee_Detail": {
        "Nominee_First_Name": "gfgh",
        "Nominee_Last_Name": "gfg",
        "Nominee_Contact_Number": "8793164535",
        "Nominee_Home_Address": null,
        "Nominee_gender": "M",
        "Nominee_dob": "03-04-2023",
        "Nominee_Salutation": "Mr",
        "Nominee_Email": "pooja@gmail.com",
        "Nominee_Relationship_Code": "1"
    },
    "PolicyCreationRequest": {
        "IsPolicyIssuance" :"0",
        "TransactionNumber": "Pay_kbToSSUXXtt",
        "TransactionRcvdDate": "23-11-2023",
        "CollectionAmount": "4504",
        "PaymentMode": "Payment Gateway"
    }
}






</textarea> <br><br>
    <input class="btn active" type="submit" name="submit_abml_json" id="submit_abml_json" value="Submit JSON"/>
</form>
<script type="text/javascript">

</script>
</body>
</html>
