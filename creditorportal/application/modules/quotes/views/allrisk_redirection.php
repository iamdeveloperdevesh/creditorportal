
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
"password":123456,
 "type":3
}
   </textarea> <br><br>
    <input class="btn active" type="submit" name="generate_redirection_checksum" id="generate_redirection_checksum" value="GenerateToken"/>
    <p>Token :  <?php echo $res1['utoken']; ?></p>
<p>UserId :  <?php echo $res1['user_id']; ?></p>
</form>
<form action="/GadgetInsurance/allriskApi" method="post" name="frmGenPipe" id="frmGenPipe">
    <input type="hidden" name="checksum" value="<?php echo $checksum;?>"/>
    <div style="float:right;"><a class="scrollTo" id="gotobottom" href="#bottom">Go to bottom</a></div>
    <h2 id="top">Fill the detail to get Certificate of Issueance:</h2>
    <textarea rows="35" cols="70" name="json_cust_data">{
        "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjQ5IiwiaWF0IjoxNjkwNTQ2NTY3LCJleHAiOjE2OTA1NjQ1Njd9.3ftL-mPhnJUL04i_-Q1RSGBwQXi_XI86rO5WAZhg7tk",
   "userId":"46",
    "Plan_id":"948",
    "Customer_ID": "123456",
    "Insurer_Name": "HDFC",
    "Cust_Salutation":"",
    "Cust_First_Name":"Amit",
    "Cust_Middle_Name":"",
    "Cust_Last_Name":"Matani",
    "Cust_Mobile_No":"8850525214",
    "Cust_email_ID":"amit@gmail.com",
    "Cust_DOB":"",
    "Cust_Gender":"",
    "Cust_Pincode":"410210",
    "Cust_Add1": "abcd123-",
    "Cust_Add2": "",
    "Sum_Insured":"200000",
    "Invoice_Amount": "200000",
    "Policy_Start_Date": "27-09-2023",
    "Policy_End_Date": "26-09-2024",
    "Product_Name": "Jewellery",
    "Master_Policy_No": "25996555",
    "Invoice_No": "782656",
    "Invoice_Date": "27-09-2023" ,
    "Policy_Tenure":"",
    "Nominee_first_name":"",
    "Nominee_last_name": "",
    "Nominee_Relation": "" ,
    "Nominee_DOB":"",
    "Nominee_Mobile": "",
    "Trace_Id": "56545",
    "Lead_Id": "789864"

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
