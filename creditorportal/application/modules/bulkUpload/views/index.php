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

    .dlbtn{
        position: relative;
        bottom: 13px;
        left: 19px;
    }

    .ulbtn{
        position: relative;
        left: 18px;
        background-color: #0182ff;
        border: none;
    }

    @media only screen and (min-width: 768px){
        .ulbtn-new{
            position: relative;
            top: 34px;
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
						<p>Bulk Upload - <i class="ti-user"></i></p>
                        <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myconfgModal">
                            Configure modal
                        </button>-->
					</div>
					<div class="col-md-2 col-2">
					</div>
				</div>
			</div>
            <div class="card-body">
                <label class="col-form-label">Marine API Data Upload</label>
                <form class="form-horizontal" id="form-bulkuploadMarine" method="post" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <div class="dataTables_filter input-group">
                                <input id="uploadfileMarine" name="uploadfileMarine" type="file" class=" form-control"  >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-md-2 col-12 txt-lr">
                            <button class="btn smt-btn" >Upload</button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-link">
                                <a href="../assets/marine insurance parameter.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="">Download Format </a>
                            </button>
                        </div> -->

                        <div>
                            <button class="btn smt-btn ulbtn" >Upload</button>
                        </div>
                        <div class="dlbtn">
                            <button class="btn btn-link">
                                <a href="../assets/marine insurance parameter.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="" style="background-color:#2cd44a;border:none;">Download Format </a>
                            </button>
                        </div>

                    </div>

                </form>
                <hr>
                <label class="col-form-label">COI Upload</label>
                <form class="form-horizontal" id="form-bulkupload" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="dataTables_filter input-group">
                           <select class="form-control" id="is_sftp" name="is_sftp">
                               <option id="0">Non-Sftp</option>
                               <option id="1">Sftp</option>
                           </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dataTables_filter input-group">
                            <input id="uploadfile" name="uploadfile" type="file" class=" form-control"  >
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-md-2 col-12 txt-lr">
                        <button class="btn smt-btn" >Upload</button>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-link">
                            <a href="../assets/Sample format Bulk Upload.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="">Download Format </a>
                        </button>
                    </div> -->

                    <div>
                        <button class="btn smt-btn ulbtn" >Upload</button>
                    </div>
                    <div class="dlbtn">
                        <button class="btn btn-link">
                            <a href="../assets/Sample format Bulk Upload.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="" style="background-color:#2cd44a;border:none;">Download Format </a>
                        </button>
                    </div>

                </div>

                    
                </div>

                </form>
                <hr>
                <label class="col-form-label">COI document Upload</label>
                <form class="form-horizontal" id="form-coidoc" method="post" enctype="multipart/form-data">
                    <div class="row">
                      <!--  <div class="col-md-3 mb-3">
                            <label class="col-form-label">Proposal Number</label>
                            <div class="dataTables_filter input-group">

                                <input id="proposal_number" name="proposal_number" type="text" class=" form-control"  >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="col-form-label">Certificate Number</label>
                            <div class="dataTables_filter input-group">

                                <input id="certificate_number" name="certificate_number" type="text" class=" form-control"  >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-md-3 mb-3">
                            <label class="col-form-label">PDF document</label>
                            <div class="dataTables_filter input-group">
                                <input id="uploadfile" name="coiuploadfile[]" multiple accept="application/pdf" type="file" class=" form-control"  >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-2 col-12 txt-lr mt-4">

                            <button class="btn smt-btn" >Upload</button>
                        </div> -->

                        <div class="ulbtn-new">

                            <button class="btn smt-btn ml-3" style="background-color:#0182ff;border:none;">Upload</button>
                        </div>

                    </div>

                </form>
            </div>

		</div>
	</div>


</div>

</div>
<!-- Center Section End-->
<script type="text/javascript">
$( document ).ready(function() {
	
});
$("#form-bulkupload").validate({
    submitHandler: function(form) {
        var act = "<?php echo base_url(); ?>bulkUpload/AddBulkFile";
        $("#form-bulkupload").ajaxSubmit({
            url: act,
            type: 'post',
            dataType: 'json',
            cache: false,
            clearForm: false,
            success: function(response) {
                if (response.success) {
                    displayMsg("success", response.msg);
                } else {
                    displayMsg("error", response.msg);
                    return false;
                }
            }
        });
    }
})
$("#form-bulkuploadMarine").validate({
    submitHandler: function(form) {
        var act = "<?php echo base_url(); ?>GadgetInsurance/AddBulkFileMarine";
        $("#form-bulkuploadMarine").ajaxSubmit({
            url: act,
            type: 'post',
            dataType: 'json',
            cache: false,
            clearForm: false,
            success: function(response) {
                if (response.success) {
                    displayMsg("success", response.msg);
                } else {
                    displayMsg("error", response.msg);
                    return false;
                }
            }
        });
    }
})
$("#form-coidoc").validate({
    submitHandler: function(form) {
        var act = "<?php echo base_url(); ?>bulkUpload/addProposaldocument";
        $("#form-coidoc").ajaxSubmit({
            url: act,
            type: 'post',
            dataType: 'json',
            cache: false,
            clearForm: false,
            success: function(response) {
                if (response.success) {
                    displayMsg("success", response.msg);
                } else {
                    displayMsg("error", response.msg);
                    return false;
                }
            }
        });
    }
})


document.title = "Bulk Upload";
</script>