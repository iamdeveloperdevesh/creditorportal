<!-- Center Section -->
<?php
$primary_color=$_SESSION['primary_color'];
if(!isset($primary_color) && empty($primary_color)){
    $primary_color='#1bb2dd';
}
?>
<style>
    .select2-container-multi .select2-choices .select2-search-field input {
        /* padding: 5px; */
        margin: 1px 0;
        font-family: sans-serif;
        font-size: 11px !important;
        color: #666;
        outline: 0;
        border: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
        background: transparent !important;
    }
    .select2-container-multi .select2-choices .select2-search-field input {
        font-weight: 600;
        width: 147px !important;
        color: #808080 !important;
        /* color: #000 !important; */
    }

    @media(max-width:425px){
        .drp-buttons .cancelBtn{
            margin-bottom: 5px;
        }
    }
    
</style>
<!-- <style>
.welcmpage_Wrapper .infoDiv {
    display: none;
}
</style> -->
<div class="main_Wrapper">
    <!-- Loader -->
    <div class="sk-circle-wrapper" style="display: none">
        <div class="sk-circle">
        <div class="sk-circle1 sk-child"></div>
        <div class="sk-circle2 sk-child"></div>
        <div class="sk-circle3 sk-child"></div>
        <div class="sk-circle4 sk-child"></div>
        <div class="sk-circle5 sk-child"></div>
        <div class="sk-circle6 sk-child"></div>
        <div class="sk-circle7 sk-child"></div>
        <div class="sk-circle8 sk-child"></div>
        <div class="sk-circle9 sk-child"></div>
        <div class="sk-circle10 sk-child"></div>
        <div class="sk-circle11 sk-child"></div>
        <div class="sk-circle12 sk-child"></div>
        </div>
    </div>
    <!-- -->
    <!-- <style>
    .col-lg-3.col-md-3.col-sm-12{
        display:none;
    } </style> -->
    <div class="welcmpage_Wrapper">
        <div class="infoDiv">
            <div class="row">
                <div class="col-md-12">
                    <h4> Dashboard </h4>
                    <p>
                </div>
                <!-- <div class="col-md-4">
                   <div class="absoluteimg">
                      <img src="../assets/images/Visualdatacuate.png">
                   </div>
                </div> -->
            </div>
        </div>
        <div class="singleSeclectDiv">
            <div class="singleS_dd container">
                <div class="title_performer">
                    <div class="titleTxt">
                        <span class="zxo2" onclick="myFunction()"> Hide Filters <i class="fa fa-minus filtrtgl"> </i>  </span>
                        <span class="zxo3">
						<img src="../assets/images/Rectanglegrdnt.png">
					</span>
                    </div>
                </div>
                <div id="mySelectDiv">
                    <div class="selectformflex" >
                        <div class="dropdown singleselector_Card">
                            <p class="singleselect_subTitle">Client</p>
                            <select class="form-control" id="client_dropdown" onchange="getPlanName(this.value);getpartnerData(); getGraphDetails(this.value);" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;"></select>
                            <!--<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Select
                            </button>-->
                            <!--<div class="dropdown-menu" id="client_dropdown" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;">

                            </div>-->

                        </div>
                        <div class="dropdown singleselector_Card">
                            <p class="singleselect_subTitle">Plan Name</p>
                            <select class="form-control" id="plan_name" onchange="getpartnerData();getGraphDetails()"  style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <option selected disabled>Select</option>
                            </select>
                        </div>

                        <div class="dropdown singleselector_Card">
                            <p class="singleselect_subTitle">Cover Type</p>
                            <select class="form-control" id="cover_type" name="cover_type[]" onchange="getDataoverType(this.value)" multiple=""  style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;"></select>
                        </div>
                        <div class="dropdown singleselector_Card">
                            <p class="singleselect_subTitle">Insurer Filter</p>
                            <select class="form-control" id="plan_name" onchange="getpartnerData();getGraphDetails()"  style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <option selected disabled>Select</option>
                            </select>
                        </div>
                        <form class="singleselector_Card dateR_picker" action="/action_page.php">
                            <div class="form-group">
                                <p for="sel1" class="singleselect_subTitle">Date</p>
                                <div class="date_picker form-control"> <input type="text" name="daterange" id="daterange" value="Select" readonly=""> <i class="fa fa-calendar" id="date_range_img" aria-hidden="true"></i> </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="cardtabsWrapper ">
                <div class="row zxo1">
                    <div class="col-lg-9 col-md-9 col-sm-12">
                        <div class="cardDiv">
                            <div class="navTabContainerFlex">
                                <ul class="nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active " id="lin1" data-itemtype="1" data-toggle="pill" data-itemtype="1" href="#home">Health Insurance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link "   id="lin2" data-itemtype="3" data-toggle="pill" data-itemtype="3"  href="#menu1">Transit Insurance</a>
                                    </li>
                                </ul>

                                <div class="moreFilter morefilterDesktop" data-toggle="modal" data-target="#FilterModal" aria-expanded="true">
                                    <span class="label1"> More Filter </span>
                                    <span class="dropdown">
									<img src="../assets/images/filter01.png">
								</span>
                                </div>

                                <div class="QuickFilter quickfilterMobile" data-toggle="modal" data-target="#QuickFilterModal" aria-expanded="true">
                                    <span class="label1"> Quick Filters </span>
                                    <span class="dropdown">
									<img src="../assets/images/filter01.png">
								</span>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div id="home" class="tab-pane active">
                                    <div class="tabconatiner_wrapper">
                                        <div class="cardBody" id="cardBody">
                                           <!-- <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/healthcareNew.png">
                                                    <h2>
                                                        Health Insurance </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium"><?php /*echo $data[0]->gross_premium; */?></span> </label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount"><?php /*echo $data[0]->certificate_number; */?></span></label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>
                                            <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/healthcare03.png">
                                                    <h2>
                                                        Personal Accident </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i><span id="gpapremium"> <?php /*echo $data[1]->gross_premium; */?> </span></label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="gpapolicycount"><?php /*echo $data[1]->certificate_number; */?></span></label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>
                                            <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/healthcarenew01.png">
                                                    <h2>
                                                        Cancer </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i><span id="gcipremium"> <?php /*echo $data[3]->gross_premium; */?> </span></label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="gcipolicycount"><?php /*echo $data[3]->certificate_number; */?></span></label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>-->

                                        </div>
                                    </div>
                                </div>
                                <div id="menu1" class="tab-pane fade">
                                    <div class="tabconatiner_wrapper">
                                        <div class="cardBody" id="cardBody2">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                       <!-- <div class="mapDiv"><img class="zoomerModalbtn" data-toggle="modal" data-target="#mapModal" src="../assets/images/zoomer.png">
                            <div class="demo-theme-dark row">
                                <div class="col-md-12 demo-block rounded padding demo-background margin-bottom-big">
                                    <div id="chartdiv" style="height: 500px;">

                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <div id="containermap" style="    height: 350px;width: 300px;border-radius:20px"></div>
                    </div>
                </div>
            </div>
            <div class="performance_Wrapper container">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 canvas_wrapper">
                        <div class="title_performer">
                            <div class="titleTxt"> <span class="zxo2"> Client Performance  </span>  <span class="zxo3"> <img src="../assets/images/Rectanglegrdnt.png"> </span> </div>
                            <div class="dateFltrIcon">
                                <input type="text" class="daterange" name="daterangeGraph" id="daterangeGraph" onchange="getGraphDetails()" value="" />
                                <img src="../assets/images/calendar_blue.jpeg">
                            </div>
                        </div>
                        <div  id="chartContainer2" style="height: 325px; max-width: auto; margin: 0px auto">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 table_wrapper">
                        <div class="statusTable_wrapper card_bgColor">


                            <div class="title_performer">
                                <div class="titleTxt"> <span class="zxo2"> Client Performance  </span>  <span class="zxo3"> <img src="../assets/images/Rectanglegrdnt.png"> </span> </div>
                                <div class="dateFltrIcon">
                                    <input type="text" class="daterange" name="daterangeTable"  id="daterangeTable" onchange="getTableData(this.value)"/>
                                    <img src="../assets/images/calendar_blue.jpeg">
                                </div>
                            </div>

                            <div class="status_tables mobileVisibile">
                                <div class="container-fluid">
                                    <div class="table-responsive-sm table-responsive">
                                        <table class="table tableStatus_info" id="help_list_table">
                                            <thead class="theadbg">
                                            <tr>
                                                <th> <span> Client </span> </th>
                                                <!--<th><span> Gross </span></th>-->
                                                <th><span> Net </span></th>
                                                <th><span> Policies </span></th>
                                                <th><span> Rank </span></th>
                                            </tr>
                                            </thead>
                                            <tbody id="help_list">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="pageFooterSingle container-fluid">
                                <!--<ul class="pagination justify-content-end">
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);"><i class="fa fa-angle-left" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link active" href="javascript:void(0);">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);"><i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                </ul>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal  -->

    <div class="modal fade filterOption" id="FilterModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Apply Filter</h4>
                </div>
                <div class="modal-body">
                    <!-- <p> <input type="checkbox">  Insurer </p> -->
                    <div class="selectformflex">

                        <div class="dropdown singleselector_Card">
                            <p class="singleselect_subTitle">Client</p>
                            <select class="form-control" id="client_dropdown" onchange="getPlanName(this.value);getpartnerData(); getGraphDetails(this.value);" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;"></select>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Apply</button>
                </div>
            </div>
        </div>
    </div>


    <!-- -->

    <div class="modal fade filterOption" id="QuickFilterModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="QuicktitleTxt">
                        <span class="zxo2"> Quick Filter  </span>
                        <span class="zxo3"> <img src="../assets/images/Rectanglegrdnt.png"> </span>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- <p> <input type="checkbox">  Insurer </p> -->
                    <div class="Quickselectformflex">
                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Client</p>
                            <select id="client_dropdown" onchange="getPlanName(this.value);getpartnerData(); getGraphDetails(this.value);" >

                            </select>
                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Plan Name</p>
                            <select id="plan_name" onchange="getpartnerData();getGraphDetails()">

                            </select>
                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Cover Type</p>
                            <select  d="cover_type" name="cover_type[]" onchange="getDataoverType(this.value)" multiple=""  class="label ui selection fluid dropdown">

                            </select>

                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Date </p>
                            <div class="dateFltrIcon">
                                <input type="text" name="daterange" id="daterange" value="Select" onchange="getpartnerData();getGraphDetails()" readonly="" tabindex="36">
                                <i class="fa fa-calendar"> </i>
                            </div>
                        </div>


                        <!-- <div class="quickWrap">
                        <p class="singleselect_subTitle">Client</p>
                            <div class="dropdown singleselector_Card">
                                <div class="select2-container form-control" id="s2id_client_dropdown" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-1">&nbsp;</span><abbr class="select2-search-choice-close"></abbr>   <span class="select2-arrow" role="presentation"><b role="presentation"></b></span></a><label for="s2id_autogen1" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" role="button" aria-labelledby="select2-chosen-1" id="s2id_autogen1" tabindex="29"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen1_search" class="select2-offscreen"></label>       <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-1" id="s2id_autogen1_search" placeholder="">   </div>   <ul class="select2-results" role="listbox" id="select2-results-1">   </ul></div></div><select class="form-control select2-offscreen" id="client_dropdown" onchange="getPlanName(this.value);getpartnerData(); getGraphDetails(this.value);" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;" tabindex="-1" title=""><option selected="" disabled="">Select</option><option value="29">Fyntune Insurance</option><option value="32">Hero Cycle</option><option value="33">Demo</option><option value="34">HDFC Ergo</option><option value="35">Godrej Housing Finance</option><option value="36">Shivkrupa</option><option value="37">DEMOs</option><option value="38">Poonawalla</option><option value="39">Piramal Capital and HFL</option><option value="40">reliance money</option><option value="41">Liberty </option><option value="43">Purple MSME</option><option value="44">Purple SME</option><option value="45">Test Fyntune</option><option value="46">Demo Feb</option><option value="47">Afzal demo </option><option value="48">Bajaj finance</option><option value="49">Capri Global Gold loan</option><option value="50">Orix </option><option value="51">GC</option><option value="52">Test partner</option><option value="53">GCPL</option><option value="54">CD balance Checking INC</option><option value="55">Afzal Health INC</option></select>

                            </div>
                        </div>
                        <div class="quickWrap">
                        <p class="singleselect_subTitle">Plan Name</p>
                            <div class="dropdown singleselector_Card">
                                <select class="form-control" id="plan_name" onchange="getpartnerData();getGraphDetails()" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;" tabindex="30">
                                    <option selected="" disabled="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="quickWrap">
                        <p class="singleselect_subTitle">Cover Type</p>
                            <div class="dropdown singleselector_Card">
                                <div class="select2-container select2-container-multi form-control" id="s2id_cover_type" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <ul class="select2-choices">
                                        <li class="select2-search-field">
                                            <label for="s2id_autogen2" class="select2-offscreen"></label>
                                            <input type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="select2-input" id="s2id_autogen2" tabindex="31" placeholder="" style="width: 20px;">
                                        </li>
                                    </ul>
                                    div class="select2-drop select2-drop-multi select2-display-none">
                                    <ul class="select2-results">
                                        <li class="select2-no-results">No matches found</li>
                                    </ul></div></div><select class="form-control select2-offscreen" id="cover_type" name="cover_type[]" onchange="getDataoverType(this.value)" multiple="" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;" tabindex="-1"><option selected="" disabled="">Select</option><option value="1">GHI</option><option value="2">GPA</option><option value="4">GCS</option></select>
                            </div>
                        </div>

                        <div class="quickWrap">
                        <p class="singleselect_subTitle">Insurer Filter</p>
                            <div class="dropdown singleselector_Card">
                                <select class="form-control" id="plan_name" onchange="getpartnerData();getGraphDetails()" style="position: absolute; transform: translate3d(0px, 41px, 0px); top: 0px; left: 0px; will-change: transform;" tabindex="32">
                                    <option selected="" disabled="">Select</option>
                                </select>
                            </div>
                        </div> -->



                        <!-- <div class="quickWrap">
                            <p for="sel1" class="singleselect_subTitle">Date</p>
                            <form class="singleselector_Card dateR_picker" action="/action_page.php">
                            <div class="form-group">

                                <div class="date_picker form-control"> <input type="text" name="daterange" id="daterange" value="Select" readonly="" tabindex="33"> <i class="fa fa-calendar" id="date_range_img" aria-hidden="true"></i> </div>
                            </div>
                            </form>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- -->






    <!-- MAP MODAL -->
    <div class="modal fade mapModalBox" id="mapModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">MAP MODAL</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div id="chartdiv01" style="height: 500px;">
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <!--  -->
    <script src="https://code.highcharts.com/maps/highmaps.js"></script>
    <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
    <script src="<?php echo base_url(); ?>assets/highchartcode.js"></script>
    <script type='text/javascript' src='https://www.amcharts.com/wp-includes/js/jquery/jquery.js' id='jquery-js'></script>

    <script type='text/javascript' src='https://cdn.amcharts.com/lib/3/ammap.js?ver=20221018-cf1' id='-lib-3-ammap-js-js'></script>
    <script type='text/javascript' src='https://cdn.amcharts.com/lib/3/themes/dark.js?ver=20221018-cf1' id='-lib-3-themes-dark-js-js'></script>
    <script>
    let stateNameNew='';
    let itemType='';
    $('#lin1').click(function(){
        itemType = $('#lin1').data('itemtype');
        getpartnerData(1);
        getTableData('',1);
        getGraphDetails(0,1);
    });
    $('#lin2').click(function(){
        itemType = $('#lin2').data('itemtype');
        getpartnerData(3);
        getTableData('',3);
        getGraphDetails(0,3);
    });
        window.onload = function () {
            $('.daterange').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
            $('#daterange').val('');
            $('.daterange').val('');
            $('#daterange').attr("placeholder","Select Date");
            $('.daterange').attr("placeholder","Select Date");

            getGraphDetails();
            getpartnerData();
            getClientName();
            gecoverType();
            getInsurerName();
            getTableData();
            $('#lin1').click();
        }
        function getClientName() { //client_dropdown
            $.ajax({
                url: "/home/getClientData",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                    var     html ='<option  ></option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                        html += '<option value="'+response.data[i].creditor_id+'">'+response.data[i].creaditor_name+'</option>';
                    });
                    $('#client_dropdown').html(html);
                    $('#client_dropdown').select2({
                        placeholder: "Select"
                    });
                   // alert();


                }
            });
        }
    function getInsurerName() { //client_dropdown
            $.ajax({
            url: "/home/getInsurerName",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                var     html ='<option selected value="0">Select</option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                    html += '<option value="'+response.data[i].insurer_id+'">'+response.data[i].insurer_name+'</option>';
                    });
                $('#insurer_name').html(html);
                $('#insurer_name').select2();
                $('#insurer_name').val(0).trigger('change.select2');

                }
            });
        }

        function gecoverType(){
            $.ajax({
                url: "/home/getCoverType",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                    var     html ='<option selected disabled>Select</option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                        html += '<option value="'+response.data[i].policy_sub_type_id+'">'+response.data[i].code+'</option>';
                    });
                    $('#cover_type').html(html);
                    $("#cover_type").select2({
                        placeholder: "Select"
                    });
                }
            });
        }

        function getDataoverType(id) {
            var cover_type=$('#cover_type').val();
            var partner_id=$('#client_dropdown').val();

            if(partner_id == null){
                alert('Please Select Partner ID.');
                $("#cover_type").attr("selected",false);
            }else{
                getpartnerData();
            }

        }
        function getpartnerData(type_id=1) {
            var partner_id=$('#client_dropdown').val();
            var plan_name=$('#plan_name').val();
            var cover_type=$('#cover_type').val();
            var daterange=$('#daterange').val();
            var insurer_name=$('#insurer_name').val();
            var type_id=type_id;
            $('.sk-circle-wrapper').show();
            $.ajax({
                url: "/home/getPartenrWiseData",
                type: "POST",
                dataType: 'json',
           data:{partner_id,plan_name,cover_type,stateNameNew,insurer_name,daterange,type_id},
                success: function(response) {
                    $("#ghipremium").html(0);
                    $("#gpapremium").html(0);
                    $("#gcipremium").html(0);
                    $("#ghipolicycount").html(0);
                    $("#gpapolicycount").html(0);
                    $("#gcipolicycount").html(0);
                    var html='';
                    var html2='';
                    var cnt1=0;
                    var cnt2=0;
                    $.each(response.data, function (i) {
                        console.log(response.data[i].logo);
                        $('.sk-circle-wrapper').hide();
                        //policy_type_id
                        if(response.data[i].policy_type_id == 1){
                            html += '  <div class="userInfo_card card_bgColor mobile_row" style="">\n' +
                                '                                                <div class="cardHeader">\n' +
                                '                                                    <img src="'+response.data[i].logo+'">\n' +
                                '                                                    <h2>\n' +
                                '                                                        '+response.data[i].policy_sub_type_name+' </h1>\n' +
                                '                                                </div>\n' +
                                '                                                <ul>\n' +
                                '                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">'+numDifferentiation(response.data[i].gross_premium)+'</span> </label> </span> </li>\n' +
                                '                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">'+response.data[i].certificate_number+'</span></label> </span> </li>\n' +
                                '                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">'+((response.data[i].gross_premium)/(response.data[i].certificate_number)).toFixed(2)+'</label> </span> </li>\n' +
                                '                                                </ul>\n' +
                                '                                            </div>';
                            cnt1++;
                        }else{
                            html2 += '  <div class="userInfo_card card_bgColor mobile_row" style="">\n' +
                                '                                                <div class="cardHeader">\n' +
                                '                                                    <img src="'+response.data[i].logo+'">\n' +
                                '                                                    <h2>\n' +
                                '                                                        '+response.data[i].policy_sub_type_name+' </h1>\n' +
                                '                                                </div>\n' +
                                '                                                <ul>\n' +
                                '                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">'+numDifferentiation(response.data[i].gross_premium)+'</span> </label> </span> </li>\n' +
                                '                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">'+response.data[i].certificate_number+'</span></label> </span> </li>\n' +
                                '                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">'+((response.data[i].gross_premium)/(response.data[i].certificate_number)).toFixed(2)+'</label> </span> </li>\n' +
                                '                                                </ul>\n' +
                                '                                            </div>';
                            cnt2++;
                        }



                    });
                    var hh = '  <div class="userInfo_card card_bgColor mobile_row" style=""><div class="cardHeader mt-3" style="    text-align: center;"> <h2>  No data Found..</h1></div> <ul>  </ul> </div>';
                    if(cnt1 > 0){
                        $('#cardBody').html(html);
                    }else{

                        $('#cardBody').html(hh);
                    }
                    if(cnt2 > 0){
                        $('#cardBody2').html(html2);
                    }else{
                        $('#cardBody2').html(hh);
                        //$('#cardBody2').html("<span class='ml-3'>No data Found..</span>");
                    }
                }
            });

        }
    function numDifferentiation(value) {
        var val = Math.abs(value)
        if (val >= 10000000) {
            val = (val / 10000000).toFixed(2) + ' Cr';
        } else if (val >= 100000) {
            val = (val / 100000).toFixed(2) + ' Lac';
        }
        return val;
        }
        function getPlanName() {
            $('#plan_name').val('');
            var partner_id=$('#client_dropdown').val();
            $('.sk-circle-wrapper').show();
            $.ajax({
                url: "/home/getPlanName",
                type: "POST",
                dataType: 'json',
                data:{partner_id},
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                    var     html ='<option value="0" selected >Select</option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                        html += '<option value="'+response.data[i].plan_id+'">'+response.data[i].plan_name+'</option>';
                    });
                    $('#plan_name').html(html);
                    $('#plan_name').select2();
                    $('#plan_name').val(0).trigger('change.select2');
                    $('.sk-circle-wrapper').hide();
                }
            });
        }
        function getGraphDetails(partner_id=0,policy_type=1){
            var plan_name=$('#plan_name').val();
            var insurer_name=$('#insurer_name').val();
            var daterange=$('#daterange').val();
            var daterangeGraph=$('#daterangeGraph').val();
            if(partner_id == 0){
                var partner_id=$('#client_dropdown').val();
            }
            policy_type=itemType;
            console.log("PP"+policy_type);

            $('.sk-circle-wrapper').show();
            $.ajax({
                url: "/home/getgraphdetails",
                type: "POST",
                dataType: 'json',
            data:{partner_id,plan_name,stateNameNew,insurer_name,daterange,daterangeGraph,policy_type},
                success: function(response) {
                    primary_color = '<?php echo $primary_color ;?>';
                    $('.sk-circle-wrapper').hide();
                    var chart = new CanvasJS.Chart("chartContainer2", {
                        animationEnabled: true,
                        theme: "light2",
                        axisX:{
                            interval: 1
                        },
//    title:{
//    	text: "Simple Line Chart"
//    },
                        data: [{
                            type: "line",
                            indexLabelFontSize: 16,
                            lineColor: primary_color,
                            dataPoints: response.y_axis
                        }]


                    });
                    chart.render();


                }
            });
        }
        function getTableData(date='',policy_type=1){
            policy_type=itemType;
            var date=$('#daterangeTable').val();
            $.ajax({
                url: "/home/getTableData",
                type: "POST",
                dataType: 'json',
                data:{date,policy_type},
                success: function(response) {
                    $('#help_list_table').DataTable().destroy();
                    if(response.datahtml != ""){
                        $('#help_list').html(response.datahtml);
                        $('#help_list_table').dataTable({
                            "searching": false,
                            "bLengthChange": false,
                        });

                    }

                }
            });

        }


    </script>
    <script>
        if ( screen.width >= 768 && screen.width <= 1136 ) {
            var ratio = Math.round( screen.width / 13 ) / 100;
            document.getElementById( "viewport" ).setAttribute( "content", "width=1300, initial-scale=" + ratio );
        }
    </script>
    <script>
        var amcharts2_policy_version = 2018052201;
    </script>
    <script>

      var data = [
          ['madhya pradesh', 0],
          ['uttar pradesh', 0],
          ['karnataka', 0],
          ['nagaland', 0],
          ['bihar', 0],
          ['lakshadweep', 0],
          ['andaman and nicobar', 0],
          ['assam', 0],
          ['west bengal', 0],
          ['puducherry', 0],
          ['daman and diu', 0],
          ['gujarat', 0],
          ['rajasthan', 0],
          ['dadara and nagar havelli', 0],
          ['chhattisgarh', 0],
          ['tamil nadu', 0],
          ['chandigarh', 0],
          ['punjab', 0],
          ['haryana', 0],
          ['andhra pradesh', 0],
          ['maharashtra', 0],
          ['himachal pradesh', 0],
          ['meghalaya', 0],
          ['kerala', 0],
          ['telangana', 0],
          ['mizoram', 0],
          ['tripura', 0],
          ['manipur', 0],
          ['arunanchal pradesh', 0],
          ['jharkhand', 0],
          ['goa', 0],
          ['nct of delhi', 0],
          ['odisha', 0],
          ['jammu and kashmir', 0],
          ['sikkim', 0],
          ['uttarakhand', 0],
          ['ladakh', 0]
      ];

      // Create the chart
      Highcharts.mapChart('containermap', {
          chart: {
              map: 'countries/in/custom/in-all-disputed',
              backgroundColor:'#E0E0E0'
          },

          title: {
              text: ''
          },

          subtitle: {
           //   text: 'Source map: <a href="http://code.highcharts.com/mapdata/countries/in/custom/in-all-disputed.js">India with disputed territories</a>'
          },

          mapNavigation: {
              enabled: true,
              buttonOptions: {
                  verticalAlign: 'bottom'
              }
          },

          colorAxis: {
              stops: [
                  [0.1, '#dceff5'],
                  [0.3, '#87abd6'],
                  [0.5, '#5c81b5'],
                  [0.6, '#5470b3'],
                  [0.9, '#2843b8'],
                  [1, '#0f11a6']
              ]
          },
          //colors:['#088395'],
          legend: {
              enabled: false,

          },
          series: [{

              data:  data,
              colorByPoint: true,
         //      color: 'red',
              allowPointSelect: true,
              states: {
                 // color: 'red',
                  hover: {
                      color: '#0A4D68'
                  },
                  select: {
                      color: '#394867',
                  }
              },
              dataLabels: {
                  enabled: false,
                  format: '{point.name}'
              },
              point: {
                  events: {
                      click: function() {
                          stateNameNew=this.name;
                          getpartnerData();
                          getGraphDetails();

                      }
                  }
              }

          }]
      });


    </script>

    <script>
        var map=  AmCharts.makeChart("chartdiv01", {
            "type": "map",
            "theme": "dark",
            "dataProvider" : {
                "mapURL": "https://www.amcharts.com/lib/3/maps/svg/indiaHigh.svg",
                "getAreasFromMap": true
            },
            "areasSettings": {
                "selectedColor": "#CC0000"
            },


        });

    </script>

    <script>
        if (document.body.offsetWidth > 600) {
            $('.navbar-toggler').click(function() {
                if($(".navbar-collapse").hasClass("show"))	{
                    $('.welcmpage_Wrapper').css({
                        'margin-left': '0rem',
                        'transition' : 'all .1s ease 0.7s'
                    });
                }
                else{
                    $('.welcmpage_Wrapper').css({
                        'margin-left': '16rem',
                        'transition' : 'all .1s ease 0.3s'
                        // 'transition' : 'all 0s ease 0.3s'
                    });
                }
            });
        }
    </script>


    <script async src="https://www.googletagmanager.com/gtag/js?id=G-N414QWTXT4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-N414QWTXT4');
    </script>

<script>
    $(document).ready(function() {
        // Initialize the date range picker
        $('#daterangeGraph').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false, // Prevent automatic input update
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

        // Update the input field with the desired format when a date is selected
        $('#daterangeGraph').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
    });
</script>
    <!-- Center Section End-->