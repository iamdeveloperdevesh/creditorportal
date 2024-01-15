<!DOCTYPE html>
<html>

<head>
    <title>Token Generation API</title>
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
<form action="/customerportal/generate_token" method="post" name="frmGenPipe" id="frmGenPipe">

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
    <textarea rows="10" cols="70" name="json_cust_data">
{ "username":"sm.chinmay", "password":123456, "type":1 }
</textarea> <br><br>
    <input class="btn active" type="submit" name="submit_abml_json" id="submit_abml_json" value="Submit JSON"/>
</form>
<script type="text/javascript">

</script>
</body>
</html>
