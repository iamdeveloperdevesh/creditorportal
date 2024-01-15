<style>
    .btn:hover{
        transform: scale(1.1);
    }
</style>
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Reports - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="creditor_name" class="col-form-label">Partner Name</label>
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
                        <label for="validationCustomUsername" class="col-form-label">From Date</label>
                        <div class="input-group">
                            <input id="from_date" name="sSearch_8" autocomplete="off" type="text" class="searchInput form-control datepicker" placeholder="DD-MM-YYYY" aria-describedby="inputGroupPrepend" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">To Date</label>
                        <div class="input-group">
                            <input id="to_date" name="sSearch_9" autocomplete="off" type="text" class="searchInput form-control datepicker" placeholder="DD-MM-YYYY" aria-describedby="inputGroupPrepend" readonly>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">date_range</span></span>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-2 col-6 txt-lr">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href="<?php echo base_url(); ?>customerleads"><button class="btn cnl-btn">Clear Search</button></a>
                    </div>

                    <div class="col-md-1 col-6 text-right">
                        <label style="visibility: hidden;" class="mt-1 col-md-12">For</label>
                        <a href="#" onclick="exportResults();"><button class="btn exp-button">Export <i class="ti-export"></i></button></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(".datepicker").datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:" + new Date('Y'),
        autoclose: true,

        maxDate: new Date()

    });


    function exportResults() {

        var creditor_name = $("#creditor_id").val();

        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();

        //alert("creditor_id: "+creditor_id);
        //return false;

        $.ajax({
            url: "<?php echo base_url() . $this->router->fetch_module(); ?>/exportexcel",
            async: false,
            type: "POST",
            dataType: 'json',
            data: {

                creditor_name: creditor_name,

                from_date: from_date,
                to_date: to_date
            },
            success: function(response) {
                if (response.success) {
                    displayMsg("success", response.msg);
                    window.open(response.Data, '_blank');
                } else {
                    displayMsg("error", response.msg);
                    return false;
                }
            }
        });


    }
    </script>