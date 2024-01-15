<style>
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

    @media(max-width:425px){
		.paging_full_numbers span .paginate_button:nth-child(4){
			position: absolute;
    		right: -27px;

		}

		.paging_full_numbers span .paginate_button:nth-child(5){
			position: absolute;
    		right: -67px;

		}

		/* .paging_full_numbers span .paginate_button:nth-child(7){
			position: absolute;
    		right: -122px;
		} */

		.paging_full_numbers .next{
			position: absolute;
    		right: -134px;
		}

		.paging_full_numbers .last{
			position: absolute;
    		right: -196px;
		}

	}
</style>
<!-- Center Section -->
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Raise Bulk Upload - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Name</label>
                        <div class="dataTables_filter input-group"style="float: none;">
                            <input id="sSearch_0" name="sSearch_0" type="text" class="searchInput form-control" placeholder="Name" aria-describedby="inputGroupPrepend" >
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-2 col-12 text-center">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href="<?php echo base_url();?>claim/lodgeclaims"><button class="btn cnl-btn fl-right">Clear Search</button></a>
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
                    <?php if(in_array('RaiseBulkClaims',$this->RolePermission)){?>
                        <div class="col-md-4 col-6">
                            <a href="<?php echo base_url();?>Raisebulkupload/addupload">
                                <button class="btn btn-sec add-btn fl-right">
                                <span class="partner">Upload Excel</span> 
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
                <div class="col-md-12 table-responsive scroll-table" style="height:500px;">
                    <table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
                        <thead>
                        <tr  class="tbl-cre">
                            <th>Partner Name</th>
                            <th>Mzapp- Alliance File Reference No.</th>
                            <th>Policy Year</th>
                            <th>Client name</th>
                            <th>Insured cont no.</th>
                            <th>Mail ID</th>
                            <th>Claim No.</th>
                            <th>Policy No</th>
                            <th>Policy Period</th>
                           <th> Name of insurer</th>
                            <th>Insurer cont no.
                            </th>
                            <th>Type of policy</th>
                            <th>Location of loss
                            </th>
                            <th>Cause of loss</th>
                            <th>Extent of loss</th>
                            <th>Transit From To</th>
                            <th>Invoice & LR No.</th>
                            <th>Month</th>
                            <th>Date of loss
                                DD/MM/YY</th>
                            <th>Date of intimation from insured
                                DD/MM/YY</th>
                            <th>Date of intimation to insurer
                                DD/MM/YY</th>
                            <th>Estimate of loss
                            </th>
                            <th>Settled Amount
                            </th>
                            <th>Final Settlement Date
                            </th>
                            <th>UTR No.
                            </th>
                            <th>Surveyor/ Investigator name & cont no.
                            </th>

                            <th>Claim handler
                            </th>
                            <th>Status
                            </th>
                            <th>Action lies
                            </th>
                            <th>Remarks
                            </th>




                            <!--<th>URL(Gadget insurance)</th>-->

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

<!-- Center Section End-->
<script type="text/javascript">
    $( document ).ready(function() {

    });
    document.title = "Track Claims";
</script>