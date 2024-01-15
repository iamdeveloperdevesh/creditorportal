<!-- Center Section -->
<style>
    .configure_header {
        background: #107591;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .configure_body {
        padding: 10px 10px;
    }

    .configure_header p {
        color: #fff;
        padding: 10px 10px;
        font-weight: 800;
    }
    .configure_btns {
        margin-top: 12px;
    }

    .configure_header button.close {
        opacity: 1;
        color: #fff;
        margin-right: 11px;
        font-weight: 100;
    }

    .configure_body form .form-group label {
        font-weight: 800;
        color: #000;
    }
    .configure_body form .form-group label span {
        color: red;
    }

    .configure_btns .smt-btn{
        margin-bottom: 0px;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default .modal-header{
        flex-direction: row-reverse;
        background: #107591;
        color: #fff;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default .modal-header{
        flex-direction: row-reverse;
        background: #107591;
        color: #fff;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default .modal-header .close {
        opacity: 1;
        color: #fff;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default .btn-primary.disabled, .btn-primary:disabled {
        color: #fff;
        background-color: #107591;
        border-color: #107591;
        opacity: 1;
    }


    .summernote_wrapper .note-editor.note-frame.panel.panel-default .btn-primary:hover{
        background: #107591 !important;
        border: none !important;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default input[type=file]::file-selector-button {
        border: 2px solid #107591;
        padding: .2em .4em;
        border-radius: .2em;
        background-color: #107591;
        transition: 1s;
        color: #fff;
    }

    .summernote_wrapper .note-editor.note-frame.panel.panel-default .modal-content{
        border-radius: 7px;
    }

    .panel-heading.note-toolbar .btn {
        padding: 4px 13px;
        background: none;
    }

    @media (max-width:425px){
        .dataTables_paginate .paginate_button.next{
            position: absolute;
        }

        .dataTables_paginate .paginate_button.last{
            position: absolute;
            margin-left: 56px;
        }
    }

    @media (max-width:768px){
        .partner{
            position: relative;
            right: 8px;
        }

        .plus-icon{
            position: absolute;
            right: 7px;
        }
    }
    
</style>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<div class="col-md-10">
	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-10 col-10">
						<p>Partners - <i class="ti-user"></i></p>
                        <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myconfgModal">
                            Configure modal
                        </button>-->
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					
					<div class="col-md-3 mb-3">
						<label for="validationCustomUsername" class="col-form-label">Partner Name</label>
						<div class="dataTables_filter input-group">
							<input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
							<div class="input-group-prepend">
								<span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
							</div>
						</div>
					</div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Staus</label>
                        <div class="dataTables_filter input-group">
                            <select id="sSearch_1" name="sSearch_1" class="searchInput form-control" aria-describedby="inputGroupPrepend" >
                                <option value="1">Active</option>
                                <option value="0">In-active</option>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
					
					<div class="col-md-2 col-12 txt-lr">
						<label style="visibility: hidden;" class="mt-1">For Space</label>
						<a href="<?php echo base_url();?>creditors"><button class="btn cnl-btn">Clear Search</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="content-section mt-3">
		<div class="card">
			<div class="cre-head">
				<div class="row">
					<div class="col-md-8 col-6">
						<p>Details</p>
					</div>
					<?php if(in_array('CreditorAdd',$this->RolePermission)){?>
						<div class="col-md-4 col-6">
							<a href="<?php echo base_url();?>creditors/addEdit">
								<button class="btn btn-sec add-btn fl-right"><span class="partner">Add Partner</span> 
                                    <span class="display-none-sm">
                                        <span class="material-icons spn-icon plus-icon">add_circle_outline</span>
                                    </span>
                                </button>
							</a>
						</div>
					<?php }?>
			</div>
			</div>
			<div class="card-body">
				<div class="col-md-12 table-responsive scroll-table" style="height:300px;">
					<table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
						<thead class="tbl-cre">
							<tr>
								<th>Partner Name</th>
								<th>Partner Code</th>
								<th>Email</th>
								<th>Mobile</th>
								<th>Phone</th>
								<th>Pancard</th>
								<th>GST No.</th>
								<th>Status</th>
								<th class="text-center">Actions</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="myconfgModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="configure_Wrapper">
                <div class="configure_info">
                    <div class="configure_header">
                        <p> Broker Template </p>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="configure_body">
                        <form action="/action_page.php">
                            <div class="form-group">
                                <label for="email">PDF Type <span> * </span> </label>
                                <select class="select form-control" id="pdf_type" name="pdf_type" onchange="openModal()">
                                    <option value="1">Comparison PDF</option>
                                    <option value="2">COI PDF</option>
                                </select>
                                <input type="hidden" class="form-control" name="creditor_id" id="creditor_id">
                            </div>
                           <!-- <div class="row">
                             <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="pwd">Partner <span> * </span> </label>
                                        <input type="password" class="form-control" id="pwd">
                                    </div>
                                </div>-->
                             <!--   <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="pwd"> Type <span> * </span> </label>
                                        <select class="select form-control">
                                            <option value="1">Mail</option>
                                        </select>
                                    </div>
                                </div> -->
                            <div class="col-sm-12 col-md-6 col-lg-6 policytypediv" style="display: none">
                                    <div class="form-group">
                                        <label for="pwd">Policy Type <span> * </span> </label>
                                        <select class="select form-control" id="policy_type" name="policy_type" onchange="fetchData()">
                                            <?php
                                            $option='';
                                            foreach ($policy_type as $policy){
                                                $option .='<option value="'.$policy->policy_type_id.'">'.$policy->policy_type_name.'</option>';
                                            }
                                            echo $option;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="summernote_wrapper">
                                <textarea id="summernote"></textarea>
                                <script>
                                    $(document).ready(function() {
                                        $('#summernote').summernote();
                                    });
                                </script>
                                <script>
                                    $('#summernote').summernote({
                                        placeholder: 'Enter detail',
                                        tabsize: 2,
                                        height: 120,
                                        toolbar: [
                                            ['style', ['style']],
                                            ['font', ['bold', 'underline', 'clear']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['table', ['table']],
                                            ['insert', ['link', 'picture', 'video']],
                                            ['view', ['fullscreen', 'codeview', 'help']]
                                        ]
                                    });
                                </script>
                            </div>
                            <div class="configure_btns">
                                <button type="button" onclick="savePDF()" class="btn smt-btn btn-primary">Update</button>
                                 <!-- <button type="Back" class="btn cnl-btn">Back</button> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Center Section End-->
<script type="text/javascript">
$( document ).ready(function() {
	
});

function deleteData(id)
{
	var r=confirm("Are you sure you want to delete this record?");
	if (r==true)
	{
		$.ajax({
			url: "<?php echo base_url().$this->router->fetch_module();?>/creditors/delRecord/"+id,
			async: false,
			type: "POST",
			success: function(data2){
				data2 = $.trim(data2);
				if(data2 == "1")
				{
					displayMsg("success","Record has been Deleted!");
					setTimeout("location.reload(true);",1000);
					
				}
				else
				{
					displayMsg("error","Oops something went wrong!");
					setTimeout("location.reload(true);",1000);
				}
			}
		});
	}
}
function openModal(id=''){
    $(".policytypediv").hide();
    $('#myconfgModal').modal('show');
    if(id != ''){
        $("#creditor_id").val(id);
    }
    var pdf_type=$("#pdf_type").val();
    var creditor_id= $("#creditor_id").val();
    if(pdf_type == 2){
        $(".policytypediv").show();
    }
    getPdfData(creditor_id,pdf_type);
}
function fetchData() {
    var pdf_type=$("#pdf_type").val();
    var creditor_id= $("#creditor_id").val();
    getPdfData(creditor_id,pdf_type);
}
function savePDF(){
    //var data=$('#summernote').html();
   var creditor_id= $("#creditor_id").val();
   var pdf_type= $("#pdf_type").val();
   var policy_type= $("#policy_type").val();
    var textareaValue = $('#summernote').summernote('code');
    $.ajax({
        url: "<?php echo base_url()?>/creditors/savepdfdata",
        async: false,
        type: "POST",
        data:{creditor_id,textareaValue,pdf_type,policy_type},
        datatype:'json',
        success: function(data2){
                if(data2){
                    displayMsg("success","Record has been Added!");
                }else{
                    displayMsg("error","Oops something went wrong!");
                }
        }
    });
}
function getPdfData(creditor_id,pdf_type){
    var policy_type= $("#policy_type").val();
    if(pdf_type == 2 && policy_type==""){
        alert("please select policy type...");
        return;
    }
    $('#summernote').summernote('code', '');

    $.ajax({
        url: "<?php echo base_url()?>/creditors/getPdfData",
        async: false,
        type: "POST",
        data:{creditor_id,pdf_type,policy_type},
        dataType:'json',
        success: function(response){
           // console.log(response['data']['title_pdf']);
            if(response != false){
                //displayMsg("success","Record has been Added!");
              //  $("#email").val(response['data']['title_pdf']);
                //response['data']['pdf_html']
                if(pdf_type == 1){
                    $("#summernote").summernote("code",response['data']['pdf_html']);
                }else{
                    $("#summernote").summernote("code",response['data']['coi_html']);
                }

            }else{
                //displayMsg("error","Oops something went wrong!");
            }
        }
    });
}

document.title = "Partners";
</script>