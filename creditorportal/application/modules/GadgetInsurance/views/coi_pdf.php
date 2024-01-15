
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-size: 14px;
        }

        @media print {
            .card-lf {
                background-color: #CA2533;
            }
        }


        .card-lf {
            background-color: #CA2533;
            border-radius: 13px;
            background-color: rgb(202 37 51);
        }

        .pf-50 {
            padding-top: 10%;
        }

        .sp-txt {
            color: #fff;
            font-size: 11px;
        }

        .ad-text {
            position: absolute;
            right: 5%;
            color: rgb(202 37 51);
        ;
            font-weight: bold;
            font-size: 6px;
        }

        .yl-txt {
            color: #FBCD66;
        }

        .fl-td td {
            font-size: 7px !important;
            padding: 0px;
        }

        .wd-10 {
            width: 5% !important;
        }

        .pd-lf-40 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .fl-cd-22 {
            background: #107591;
            border-radius: 0px 0px 19px 19px;
        }

        .fl-12 {
            font-size: 6px;
            color: #fff;
            width: 70%;
            letter-spacing: 1px;
            padding: 5px;
        }

        .fl-22 {
            width: 30%;
            text-align: center;
            margin-top: 10px;
        }

        .tbl td {
            text-align: left !important;
        }

        .wh-tbl {
            border: 1px solid #fff !important;
            text-align: left;
            font-weight: 600;
        }

        .img-rad {
            border-radius: 0px 0px 20px 20px;
        }

        .gd-txt {
            border: 1px solid #fff !important;
            padding-left: 0px !important;
            padding-bottom: 0px !important;
        }

        .txt-premium {
            text-transform: uppercase;
            text-align: center !important;
        }

        .ft-10 td {
            font-size: 10px !important;
        }

        .fl-1 {
            width: 50%;
            font-size: 10px;
        }

        .fl-2 {
            width: 50%;
            font-size: 10px;
        }

        .fl-cd {
            border: 2px solid grey;
            border-radius: 20px 20px 0px 0px;
            padding: 9px;
        }

        .fl-cd p {
            line-height: 8px;
            margin: 2px;
            font-size: 7px;
        }

        .fl-card {
            width: 100%;
            display: flex;
        }

        .card-ft {
            font-size: 6px;
            line-height: 6px;
        }

        table,
        th,
        td {
            border: 1px solid #8e8e8e;
            border-collapse: collapse;
            padding: 5px;
            width: 22%;
            text-align: left;
            font-size: 13px !important;
        }

        /* .content_pdf {
             padding-left: 15px;
             padding-right: 15px;
         }
 */
        .content_pdf {
            padding-left: 0px;
            padding-right: 0px;
            border: 1px solid black;
        }

        .header_title {
            font-weight: 600;
            font-size: 18px;
            color: #000;
            margin: 0px;
            padding: 6px;
            font-size: 15px;
            text-align: left;
        }

        .table_content_space {
            padding-top: 0px;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
        }

        .text_left td {
            text-align: left;
        }

        .pdf_flex {
            display: flex;
        }

        .pdf_flex p {
            padding-right: 5%;
        }

        .pdf_flex3 p {
            font-size: 12px;
        }

        .pdf_flex3 {
            display: flex;
        }

        .card_health {
            border: 2px solid #696969;
            padding: 10px;
            margin-top: 15px;
            border-radius: 26px;
        }

        .card_1111 {
            width: 32%;
        }

        .new-page {
            page-break-before: always;
        }

        @media print {
            .new-page {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
<div class="container_pdf">

    <div class="content_pdf">

        <!--  <div class="pdf_header">
             <p class="header_title" style="background: #fff900;
                     color: #000; border-bottom: 1px solid #000;
                 font-size: 18px; text-align: center;">COI Format </p>
         </div> -->


        <div class="pdf_header">
            <p class="header_title" style="background: #9bc17f;
                        color: #000;
                    font-size: 18px; text-align: center;"><?php echo $creaditor_name.' - '.$plan_name ?> - Certificate of Insurance</p>
        </div>
        <div>
            <div class="table_content_space">
                <table>
                    <tr>
                        <td style="font-weight: bold;">Policy Issuing Office</td>
                        <td >10th Floor,
                            R-Tech Park, Nirlon Compound, Goregaon-East, Mumbai-400063</td>
                        <td style="font-weight: bold;">Policy Servicing Office </td>
                        <td >G B Rain Tree Place No-7 MC Nichols Road
                            ChetpetCHENNAITAMIL NADU600031</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" >Master Policy Number </td>
                        <td >  <?php echo $policy_number; ?>  </td>
                        <td style="font-weight: bold;">Certificate Number</td>
                        <td ><?php echo $certificate_number; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Master Policy Holder Name</td>
                        <td > <?php echo $cust_details; ?> </td>
                        <td style="font-weight: bold;">Member Id</td>
                        <td>HIN100011795</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Product Name</td>
                        <td ><?php echo $creaditor_name.' - '.$plan_name ?> </td>
                        <td style="font-weight: bold;">Unique Identification Number </td>
                        <td >LM00011795</td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold;">Plan Name</td>
                        <td> <?php echo $plan_name ?> </td>
                        <td style="font-weight: bold;" >Contact Details</td>
                        <td ><?php echo $mobile_number; ?>
                    </tr>

                </table>
            </div>
        </div>
        <div class="table_content_space">
            <table class="text_left" style="font-weight: bold; border: none;">
                <tr style="font-weight: bold; border: none;">
                    <td style="font-weight: bold; border: none;">
                    </td>
                </tr>
            </table>
        </div>
        <div class="table_content_space">
            <table class="text_left">
                <tr>
                    <td style="font-weight: bold;">
                        Start date & Time of Master Policy
                    </td>
                    <td style="font-weight: bold;">
                        00:01 hrs <?php echo date('d/m/Y',strtotime($start_date)); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" >Expiry Date & Time of Master Policy</td>
                    <td style="font-weight: bold;">23:59 on <?php echo date('d/m/Y',strtotime($end_date)); ?> </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Period of Insurance</td>
                    <td> -- </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Inception Date</td>
                    <td >00:01 hrs <?php echo date('d/m/Y',strtotime($start_date)); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">End Date</td>
                    <td >23:59 on <?php echo date('d/m/Y',strtotime($end_date)); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="table_content_space">
            <div class="pdf_header">
                <p class="header_title" style="font-weight: bold;">Insured Gadget Detail
                </p>
            </div>
        </div>

        <table>
            <tr style="background-color: #ffd400">
                <th>Insured Gadget</th>
                <th>Date of Purchase</th>
                <th>Make  </th>
                <th>Model  </th>
                <th>Sum Insured</th>
                <th>Premium</th>

            </tr>

            <tr>
                <td> <?php echo $policy_sub_type_name; ?></td>
                <td>  <?php echo $gadeget_purchase_date; ?></td>
                <td> <?php echo $make; ?></td>
                <td> <?php echo $unique_number; ?></td>
                <td> <?php echo $premium_details['sum_insured'];?> </td>
                <td> <?php echo $premium_details['premium'];?> </td>
            </tr>

        </table>





    </div>
</div>
</div>

</body>

</html>