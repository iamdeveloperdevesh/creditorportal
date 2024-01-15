<!-- Center Section -->
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
</style>
<div class="main_Wrapper">
    <!-- Loader -->
    <!-- <div class="sk-circle-wrapper">
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
    </div> -->
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
                    <p> Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                        Impedit temp um dolor, sit amet consectet. The dolor, sit amet consectetur adipisicing elit.
                        Impedit temp um dolor, sit amet consectet. Impedit temp um dolor, sit amet consectet.
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
                <div class="selectformflex">
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
        <div class="container-fluid">
            <div class="cardtabsWrapper ">
                <div class="row zxo1">
                    <div class="col-lg-9 col-md-9 col-sm-12">
                        <div class="cardDiv">
                            <div class="navTabContainerFlex">
                                <ul class="nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill" href="#home">Insurance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" href="#menu1">Gadget Insurance</a>
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
                                        <div class="cardBody">
                                            <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/cycle_insurance.jpeg">
                                                    <h2>
                                                        Cycle Insurance </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> 1200 </label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data">100</label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>
                                            <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/mobile_insurance.jpeg">
                                                    <h2>
                                                        Mobile Insurance </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> 1200 </label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data">100</label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>
                                            <div class="userInfo_card card_bgColor mobile_row" style="">
                                                <div class="cardHeader">
                                                    <img src="../assets/images/Laptop_insurance.jpeg">
                                                    <h2>
                                                        Laptop Insurance </h1>
                                                </div>
                                                <ul>
                                                    <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> 1200 </label> </span> </li>
                                                    <li> <span> Policies : </span> <span>  <label class="mobile_search_data">100</label> </span> </li>
                                                    <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">0</label> </span> </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="mapDiv"><img class="zoomerModalbtn" data-toggle="modal" data-target="#mapModal" src="../assets/images/zoomer.png">
                            <div class="demo-theme-dark row">
                                <div class="col-md-12 demo-block rounded padding demo-background margin-bottom-big">
                                    <div id="chartdiv" style="height: 500px;">

                                    </div>
                                </div>
                            </div>
                        </div>
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
                                <img src="../assets/images/filter01.png">
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
                                    <img src="../assets/images/filter01.png">
                                </div>
                            </div>
                            <!-- <div class="mobile_cardWrapView01">
                                <div class="PerformanceStatus_Mbl desktopVisible">
                                    <div class="performanceCard">
                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                    </div>

                                    <div class="performanceCard">
                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                    </div>

                                    <div class="performanceCard">
                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                    </div>

                                    <div class="performanceCard">
                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                    </div>

                                    <div class="performanceCard">
                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                        <ul>
                                        <li> <span> Premium :</span> <span>  <label class="mobile_search_data"> <i class="fa fa-rupee"></i> <span id="ghipremium">293933.64</span> </label> </span> </li>
                                        <li> <span> Policies : </span> <span>  <label class="mobile_search_data"><span id="ghipolicycount">58</span></label> </span> </li>
                                        <li> <span> Average Ticket Size : </span> <span>  <label class="mobile_search_data">789</label> </span> </li>
                                        </ul>

                                    </div>
                                </div>
                                <div class="viewMore">
                                    <span> View More <i class="fa fa-angle-down"> </i> </span>
                                </div>
                            </div>	 -->
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
                            <select>
                                <option>HTML</option>
                                <option>HTML CSS3</option>
                                <option active>HTML CSS3 SASS</option>
                                <option>HTML CSS3 SASS LESS </option>
                                <option>HTML CSS3 SASS LESS  STYLUS</option>
                                <option>JQUERY</option>
                                <option>BOOTSTRAP</option>
                                <option>MATERIAL</option>
                                <option>REACT</option>
                                <option>ANGULAR</option>
                            </select>
                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Plan Name</p>
                            <select>
                                <option>HTML</option>
                                <option>HTML CSS3</option>
                                <option active>HTML CSS3 SASS</option>
                                <option>HTML CSS3 SASS LESS </option>
                                <option>HTML CSS3 SASS LESS  STYLUS</option>
                                <option>JQUERY</option>
                                <option>BOOTSTRAP</option>
                                <option>MATERIAL</option>
                                <option>REACT</option>
                                <option>ANGULAR</option>
                            </select>
                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Cover Type</p>
                            <select  id="cover_type2" name="cover_type[]"
                                     onchange="getDataoverType(this.value)" multiple="" class="label ui selection fluid dropdown">
                                <option value="">All</option>
                                <option value="1">Change Methodology</option>
                                <option value="2">Cognitive Computing & AI</option>
                                <option value="3">Connectivity & Collaboration</option>
                                <option value="4">Culture in Action</option>
                                <option value="5">Future of Work</option>
                                <option value="6">HR Transformation</option>
                                <option value="7">Human-centered Design</option>
                                <option value="8">Machine Learning & AI</option>
                                <option value="9">Operational Effectiveness</option>
                                <option value="10">Operational Excellence</option>
                                <option value="11">Organizational Change</option>
                            </select>

                        </div>

                        <div class="quickWrap">
                            <p class="singleselect_subTitle">Date </p>
                            <div class="dateFltrIcon">
                                <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" tabindex="36">
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

    <script type='text/javascript' src='https://www.amcharts.com/wp-includes/js/jquery/jquery.js' id='jquery-js'></script>

    <script type='text/javascript' src='https://cdn.amcharts.com/lib/3/ammap.js?ver=20221018-cf1' id='-lib-3-ammap-js-js'></script>
    <script type='text/javascript' src='https://cdn.amcharts.com/lib/3/themes/dark.js?ver=20221018-cf1' id='-lib-3-themes-dark-js-js'></script>
    <script>

        window.onload = function () {
            getGraphDetails();
            getClientName();
            gecoverType();
        }
        function getClientName() { //client_dropdown
            $.ajax({
                url: "/home/getClientData",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                    var     html ='<option selected disabled>Select</option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                        html += '<option value="'+response.data[i].creditor_id+'">'+response.data[i].creaditor_name+'</option>';
                    });
                    $('#client_dropdown').html(html);
                    $('#client_dropdown').select2();

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
                    $('#cover_type').select2();
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
                    $('#cover_type2').html(html);
                    $('#cover_type2').select2();
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
        function getpartnerData() {
            var partner_id=$('#client_dropdown').val();
            var plan_name=$('#plan_name').val();
            var cover_type=$('#cover_type').val();
            $.ajax({
                url: "/home/getPartenrWiseData",
                type: "POST",
                dataType: 'json',
                data:{partner_id,plan_name,cover_type},
                success: function(response) {
                    $("#ghipremium").html(0);
                    $("#gpapremium").html(0);
                    $("#gcipremium").html(0);
                    $("#ghipolicycount").html(0);
                    $("#gpapolicycount").html(0);
                    $("#gcipolicycount").html(0);
                    $.each(response.data, function (i) {
                        if(response.data[i].policy_sub_type_id == 1){
                            $("#ghipremium").html(response.data[i].gross_premium);
                            $("#ghipolicycount").html(response.data[i].certificate_number);
                        }else   if(response.data[i].policy_sub_type_id == 2){
                            $("#gpapremium").html(response.data[i].gross_premium);
                            $("#gpapolicycount").html(response.data[i].certificate_number);
                        }else   if(response.data[i].policy_sub_type_id == 4){
                            $("#gcipremium").html(response.data[i].gross_premium);
                            $("#gcipolicycount").html(response.data[i].certificate_number);
                        }
                    });
                }
            });

        }
        function getPlanName() {
            $('#plan_name').val('');
            var partner_id=$('#client_dropdown').val();

            $.ajax({
                url: "/home/getPlanName",
                type: "POST",
                dataType: 'json',
                data:{partner_id},
                success: function(response) {
                    //<a class="dropdown-item" href="#">Booking Pending</a>
                    var     html ='<option selected disabled>Select</option>';
                    $.each(response.data, function (i) {
                        //    console.log(response.data[i].creaditor_name);
                        html += '<option value="'+response.data[i].plan_id+'">'+response.data[i].plan_name+'</option>';
                    });
                    $('#plan_name').html(html);
                    $('#plan_name').select2();
                }
            });
        }
        function getGraphDetails(partner_id=0){
            var plan_name=$('#plan_name').val();
            if(partner_id == 0){
                var partner_id=$('#client_dropdown').val();
            }
            $.ajax({
                url: "/home/getgraphdetails",
                type: "POST",
                dataType: 'json',
                data:{partner_id,plan_name},
                success: function(response) {

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
                            dataPoints: response.y_axis
                        }]


                    });
                    chart.render();
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
        var map1=   AmCharts.makeChart("chartdiv", {
            "type": "map",
            "theme": "dark",
            "dataProvider" : {
                "mapURL": "https://www.amcharts.com/lib/3/maps/svg/indiaHigh.svg",
                "getAreasFromMap": true
            },
            "areasSettings": {
                //  "autoZoom": true,
                selectable: true,
                "selectedColor": "#CC0000"
            }

        });
        map1.addListener("clickMapObject", function(event){
            var stateName=event.mapObject.enTitle;
            getDataStatewise(stateName);
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
                        'margin-left': '15rem',
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
    <!-- Center Section End-->