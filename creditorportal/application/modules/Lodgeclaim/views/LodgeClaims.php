<style>
    	@media(max-width:425px){
		.paging_full_numbers .last{
			position: absolute;
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
                        <p>Lodge Claims - <i class="ti-user"></i></p>
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

                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 table-responsive scroll-table" style="height:500px;">
                    <table class="dynamicTable table table-bordered non-bootstrap display pt-3 pb-3">
                        <thead>
                        <tr  class="tbl-cre">
                            <th>Partner Name</th>
                            <th>URL</th>
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
    document.title = "Lodge Claims";
</script>