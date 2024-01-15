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
                <label class="col-form-label">Raise Bulk Upload</label>
                <form class="form-horizontal" id="bulkuploadraise" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3 mb-3">

                        <select class="form-control" name="creditor_id" id="creditor_id">
                            <option value="">Select Partner</option>
                            <?php foreach ($getCreditorDetails as $creditor) {
                                $selected = "";
                                if($getDetails[0]['creditor_id'] == $creditor['creditor_id']){
                                    $selected = "selected";
                                }?>
                                <option value="<?php echo $creditor['creditor_id']; ?>" <?php echo $selected?>><?php echo $creditor['creaditor_name']; ?></option>
                            <?php } ?>
                        </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="dataTables_filter input-group">
                                <input id="uploadfilebulk" name="uploadfilebulk" type="file" class=" form-control"  >
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-12 txt-lr">
                            <button class="btn smt-btn" >Upload</button>
                        </div>
                        <div class="col-md-2 col-12" style="
    margin-top: -8px;
    margin-left: -73px;
">
                            <button class="btn btn-link">
                                <a href="../assets/no_broker_excel.xlsx" type="button" class="btn btn-danger btn-lg download-btn" download="">Download Format </a>
                            </button>
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
    $("#bulkuploadraise").validate({
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>Raisebulkupload/AddRaiseBulkFile";
            $("#bulkuploadraise").ajaxSubmit({
                url: act,
                type: 'post',
                dataType: 'json',
                cache: false,
                clearForm: false,
                success: function(response) {
                    if (response.success) {
                        displayMsg("success", response.msg);
                        setTimeout(function() {
                            window.location = "<?php echo base_url(); ?>Raisebulkupload";
                        }, 2000);
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