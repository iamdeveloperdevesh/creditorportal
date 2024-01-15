<?php

//header("Content-type: text/css; charset: UTF-8");
//print_r($_SESSION['background_color']);die;
$background_color=$_SESSION['background_color'];
if(!isset($background_color) && empty($background_color)){
    $background_color='#E6F9FF';
}
$primary_color=$_SESSION['primary_color'];
if(!isset($primary_color) && empty($primary_color)){
    $primary_color='#1bb2dd';
}
$secondary_color=$_SESSION['secondary_color'];
if(!isset($secondary_color) && empty($secondary_color)){
    $secondary_color='#107591';
}
$text_color=$_SESSION['text_color'];
if(!isset($text_color) && empty($text_color)){
    $text_color='#080808';
}
$cta_color=$_SESSION['cta_color'];
if(!isset($cta_color) && empty($cta_color)){
    $cta_color='#F2581B';
}
?>

<style>
    .img-pro-2 {
        border-radius: 75px !important;
        border: 1px dashed <?php echo $secondary_color;?>;
        padding: 1px 9px;
        display:  inline-flex;
    }
    .tab-part button.active {
        border-radius: 100px;
        border: 1px dashed <?php echo $secondary_color;?>;
        padding: 7px 13px;
        background: <?php echo $secondary_color;?>;
        color: #fff;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight:  600;
    }
    #input {
        position: relative;
        margin-top: -4px;
    }

    @media screen and (min-width:768px){
        #input{

        background-image: linear-gradient(to left, #fff 64%, <?php echo $background_color;?> 30%) !important;


        }
    }
    .p_input_title{
        background-image: linear-gradient(to right, <?php echo $background_color;?> , #fff);
    }
    #progressbar li.active:before{
        color: <?php echo $secondary_color;?>;
    }
    #progressbar li.active:before, #progressbar li.active:after {
        border: <?php echo $secondary_color;?>;
    }
    .input_place_css_r:hover {
        border: 2px solid <?php echo $secondary_color;?> !important;
    }
    .group_input input[type=checkbox]:checked+label, .group_input input[type=radio]:checked+label {

        border: 1px solid <?php echo $secondary_color;?>;
    }
    .group_input input[type=radio]:checked+label:before {
        background: <?php echo $background_color;?>;
        color: <?php echo $secondary_color;?>;
    }
    .action-button {
        background-color: <?php echo $cta_color;?> !important;
    }
    .color_five {
        color: <?php echo $secondary_color;?>;
    }
    #progressbar li:after {
        content: '';
        width: 100%;
        height: 2px;
        border: 1px solid #f7f7f7;
        position: absolute;
        left: -50%;
        top: 18px;
        z-index: -1;
    }
    .checkbox-tools:checked+label {
        color:  <?php echo $secondary_color;?>;
        border: 1px solid  <?php echo $secondary_color;?>;
    }
    .checkbox-tools:not(:checked)+label:hover {
        background-color: #fff;
        color: <?php echo $secondary_color;?>;
        border: 1px solid <?php echo $secondary_color;?>;
    }
    .cbx span:first-child {
        border: 1px solid <?php echo $secondary_color;?>;
    }
    .cbx span:first-child svg {
        stroke: <?php echo $secondary_color;?>;
    }
    .cbx:hover span:first-child {
        border-color:  <?php echo $secondary_color;?>;
    }
    .inp-cbx:checked+.cbx span:first-child{
        border-color:  <?php echo $secondary_color;?>;
    }
    .inp-cbx:checked+.cbx span:first-child {
        background: #fff;
        border-color: <?php echo $secondary_color;?>;
        animation: wave 0.4s ease;
    }
    .i_add {
        color: <?php echo $secondary_color;?> !important;
    }
    .pcss3t-theme-1>input:checked+label {

        border: 1px solid <?php echo $secondary_color;?>;
        font-size: 15px;
        padding: 17px 25px;
        border-radius: 12px;
        width: 82%;
        background: <?php echo $background_color;?>;
        color: <?php echo $secondary_color;?>;
    }
    .card-product {
        background-color:  <?php echo $background_color;?> !important;
    }
    .card-si {
        background:<?php echo $background_color;?> !important;

        border: 1px dashed <?php echo $secondary_color;?> !important;
    }
    .fam-btn {
        background: <?php echo $background_color;?> ;
        border: 1px dashed <?php echo $secondary_color;?> !important;
    }
    .btn-premium {
        border: 1px dashed <?php echo $secondary_color;?>  !important;
        color: <?php echo $secondary_color;?> ;
    }
    .btn-buynow {
        background-color: <?php echo $cta_color;?> !important;
    }
    .head-tittle {
        font-size: 18px;
        font-weight: 600;
        color: <?php echo $secondary_color;?>;
        margin-top: -5px;
        letter-spacing: 1px;
        margin-bottom:10px;
        font-family: 'Titillium', sans-serif;
    }
    .tab-part button.active {
        border-radius: 100px;
        border: 1px dashed <?php echo $secondary_color;?>;
        padding: 7px 13px;
        background: <?php echo $secondary_color;?>;;
        color: #fff;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: 600;
    }
    .premium-top {
        font-size: 18px;
        letter-spacing: 1px;
        font-weight: 600;
        color: <?php echo $secondary_color;?>;
        text-align: right;
        cursor: pointer;
    }
    .del-ht {
        font-size: 15px;
        letter-spacing: 1px;
        font-weight: 600;
        color: <?php echo $secondary_color;?>;
        margin-left: 4px;
    }
    .sum-del {
        padding: 7px 10px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 1px;
        color: <?php echo $secondary_color;?>;
        border-radius: 5px;
        border: 1px dashed <?php echo $secondary_color;?>;
        background:  <?php echo $background_color;?> ;
        line-height: 19px;
    }
    .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
        background:   <?php echo $secondary_color;?> !important;
    }
    .familyFloatBtn {
        background: <?php echo $secondary_color;?> !important;
    }
    .btn-submit{
        background: <?php echo $secondary_color;?> !important;
    }
    .generate-footer {
        background:  <?php echo $cta_color;?>;
        border-radius: 0px 0px 20px 20px;
        padding: 10px 10px;
        position: absolute;
        bottom:0;
        width: 100%;
        left: 0;
    }
    .form-control-age:hover {
        display: block;
        width: 100%;
        font-size: 18px;
        line-height: 3.5;
        background-size: 19px 20px;
        border: none;
        border-radius: 8px;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        font-weight: normal;
        border: 1px solid <?php echo $secondary_color;?>;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .signUp-page form .input-group input:hover {
        width: 100%;
        border: 1px solid <?php echo $secondary_color;?>;
        border-radius: 5px;
        background: transparent;
        font-size: 18px;
        color: #454545;
        position: relative;
        padding: 0 20px;
        /* box-shadow: 0 2px 12px 5px rgba(35,61,99,.11);*/
    }
    .go_back_prposal_p {
        position: absolute;
        left: 52px;
        color: <?php echo $secondary_color;?>;
    }
    .theme-tab-basic.theme-tab .tabs-menu li.z-active a {
        color:  <?php echo $secondary_color;?> !important;
        font-size: 19px;
        font-weight: 900;
        text-align: left;
    }
    .custom-checkbox .custom-control-label::before {
        border: 1px solid <?php echo $secondary_color;?>;
        cursor: pointer;
    }
    .color_cover_p_r_b {
        color: <?php echo $secondary_color;?> !important;
        font-family: 'PFEncoreSansPromed';
        margin-bottom: 5px;
        font-size: 16px;
    }
    .total_premium_btn_addon_r_p_f {
        color: <?php echo $secondary_color;?> !important;
        font-size: 23px !important;
        background: transparent;
        padding: 5px 0px !important;
        border-radius: 8px;
        height: 46px;
        line-height: 40px !important;
        margin-right: 11px;
    }
    .form-control-age_nominee:hover {
        display: block;
        width: 100%;
        /*padding: .375rem .75rem;*/
        font-size: 18px;
        line-height: 3.5;
        background-size: 19px 20px;
        border: none;
        border-radius: 8px;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        font-weight: normal;
        /*box-shadow: 0 2px 12px 5px rgba(35,61,99,.11);*/
        border: 1px solid <?php echo $secondary_color;?>;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none
    }
    .memProposerBtn{
        background: <?php echo $secondary_color;?>;
    }
    .btn_start_proposal_back{
        color: <?php echo $secondary_color;?>;
    }
    .card-header{
        background-color: <?php echo $secondary_color;?> !important;
    }
    .btn-none{
        color: <?php echo $secondary_color;?> !important;
    }
    .total_premium_btn_proposal_summary{
        background: <?php echo $cta_color;?>;
    }
    .btn-color-all{
        background: <?php echo $cta_color;?>;

    }
    .total_premium_btn_addon_r_s{
        background: <?php echo $cta_color;?>;
    }

    /*Admin Theme configuaration*/
    .welcmpage_Wrapper .infoDiv {
        padding: 21px 21px;
        background: <?php echo $primary_color;?>;
    }
    .cardtabsWrapper .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: <?php echo $primary_color;?>;;
        border-radius: 30px;
        color: #fff;
        padding: 6px 18px !important;
        margin-right: 5px;
        font-size: 13px;
    }
    .cardtabsWrapper .nav-pills .nav-link {
        color: <?php echo $primary_color;?>;
        padding: 6px 18px !important;
        margin-right: 5px;
        font-size: 13px;
    }
    .moreFilter .label1 {
        color: <?php echo $secondary_color;?>;
        font-weight: 600;
        font-size: 12px;
    }
    span.zxo2 {
        position: relative;
        z-index: 1;
        color: <?php echo $primary_color;?>;
        font-weight: 700;
    }
    .tabconatiner_wrapper .cardBody::-webkit-scrollbar {
        width: 4px;
        background: <?php echo $primary_color;?>;;
        height: 6px;
    }
    .tabconatiner_wrapper .cardBody::-webkit-scrollbar {
        width: 4px;
        background: <?php echo $primary_color;?>;;
        height: 6px;
    }
    .tabconatiner_wrapper .cardBody::-webkit-scrollbar-thumb {
        background: <?php echo $primary_color;?>;;
        border-radius: 30px;
    }
    .dateFltrIcon input {
        border: none;
        font-size: 11px !important;
        color: <?php echo $secondary_color;?> !important;
        font-weight: 600;
        font-family: inherit;
    }
    .status_tables .theadbg {
        background-color: <?php echo $primary_color;?>;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        background: <?php echo $primary_color;?> !important;
    }
    table.display thead tr {
        /* background: #da8089 !important; */
        background: <?php echo $primary_color;?> !important;
        color: #fff;
        letter-spacing: 0.3px;
    }
    .cnl-btn {
        background: <?php echo $secondary_color;?>;

        border: 3px solid  <?php echo $secondary_color;?>;
    }
    .smt-btn {
        background: <?php echo $cta_color;?>;
        border: 3px solid  <?php echo $cta_color;?>;
    }
    .fl-right {
        color: <?php echo $secondary_color;?>;
    }
    .add-btn {
        background: <?php echo $background_color;?>;;
        border: 1px dotted <?php echo $background_color;?>;;
        color: #107591;
    }
</style>