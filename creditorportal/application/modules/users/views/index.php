<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

.imp-button {
    color: #7cb342 !important;
    background: #f1f8e9 !important;
    border-radius: 10px;
    text-align: left;
    padding-left: 16%;
    border-color: #9ccc65 !important;
    border: 1px solid !important;
    padding: 11px 28px 11px 17px;
}
.mat-import {
    color: #7cb342 !important;

}
.buttonMTop {
    margin-top: 15px;
}
.buttonMTop .cnl-btn {
    margin-left: 14px;
}
.buttonMTop .imp-button {
    margin-left: 25px;
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

    @media(max-width:425px){
		.paging_full_numbers span .paginate_button:nth-child(4){
			position: absolute;
    		right: -27px;
		}

		.paging_full_numbers span .paginate_button:nth-child(5){
			position: absolute;
    		right: -67px;
		}

		.paging_full_numbers span .paginate_button:nth-child(7){
			position: absolute;
    		right: -122px;
		}

		.paging_full_numbers .next{
			position: absolute;
    		right: -131px;
		}

		.paging_full_numbers .last{
			position: absolute;
    		right: -194px;
		}

	}
</style>
<?php //echo "<pre>";print_r($roles);exit;?>
<!-- Center Section -->
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Users - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="userid" class="col-form-label">User Id</label>
                        <div class="dataTables_filter input-group">
                            <input id="userid" type="text" class="searchInput form-control" placeholder="Enter User Id" aria-describedby="inputGroupPrepend" >
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="firstname" class="col-form-label">First Name</label>
                        <div class="dataTables_filter input-group">
                            <input id="firstname" type="text" class="searchInput form-control" placeholder="Enter First Name" aria-describedby="inputGroupPrepend" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123)">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="lastname" class="col-form-label">Last Name</label>
                        <div class="dataTables_filter input-group">
                            <input id="lastname" type="text" class="searchInput form-control" placeholder="Enter Last Name" aria-describedby="inputGroupPrepend" required="" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123)">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="employeecode" class="col-form-label">Employee ID</label>
                        <div class="dataTables_filter input-group">
                            <input id="employeecode" type="text" class="searchInput form-control" placeholder="Enter Employee ID" aria-describedby="inputGroupPrepend" required="">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="emailid" class="col-form-label">Employee Email.Id</label>
                        <div class="dataTables_filter input-group">
                            <input id="emailid" type="text" class="searchInput form-control" placeholder="Enter Email Id" aria-describedby="inputGroupPrepend" required="">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="contact" class="col-form-label">Mobile No</label>
                        <div class="dataTables_filter input-group">
                            <input id="contact" type="text" maxlength="10" class="searchInput form-control" placeholder="Enter Mobile No" aria-describedby="inputGroupPrepend" required="" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); $(this).val($(this).val().replace(/[^0-9]/g, ''));">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">stay_current_portrait</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="role" class="col-form-label">Role</label>
                        <div class="dataTables_filter input-group">
                            <select id="role" class="searchInput form-control" name="role">
                                <option value="">All</option>
                                <?php
                                if(!empty($roles)){
                                    //foreach($roles as $cdrow)
                                    for($i=0; $i < sizeof($roles); $i++){
                                        ?>
                                        <option value="<?php echo $roles[$i]['role_id']; ?>"><?php echo $roles[$i]['role_name']; ?></option>
                                        <?php
                                    }}
                                ?>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">admin_panel_settings</span></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Location</label>
                        <div class="dataTables_filter input-group">
                            <select id="location_id" class="searchInput form-control" name="location_id">
                                <option value="">All</option>
                                <?php
                                if(!empty($locations)){
                                    for($i=0; $i < sizeof($locations); $i++){
                                        ?>
                                        <option value="<?php echo $locations[$i]['location_id']; ?>" ><?php echo $locations[$i]['location_name']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">add_location</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-6 text-left">
                        <label style="visibility: hidden;" class="mt-1">For Space</label>
                        <a href="<?php echo base_url();?>users/importUser"><button class="btn imp-button btn-import">Import User <span class="material-icons mat-import">contact_mail</span></button></a>
                    </div>
                    <div class="col-md-2 col-6 txt-lr">
                        <label style="visibility: hidden;" class="mt-1 display-sm-web">For Space</label>
                        <a href="<?php echo base_url();?>users/importUser"><button class="btn cnl-btn btn-import">Import User <span class="material-icons mat-import">contact_mail</span></button></a>
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
                    <?php if(in_array('UserAdd',$this->RolePermission)){?>
                        <div class="col-md-4 col-6">
                            <a href="<?php echo base_url();?>users/addEdit">
                                <button class="btn btn-sec add-btn fl-right">
                                <span class="partner">Add User</span>
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
                        <thead class="tbl-cre">
                        <tr>
                            <th>User Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Employee ID</th>
                            <th>Email Id</th>
                            <th>Mobile</th>
                            <th>Role</th>
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
<div class="modal fade " id="APImodal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">API Request to generate Token</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12" >

                        <div class="col-md-6" id="healthAPI"></div>
                        <div class="col-md-6" id="gadgetAPI"></div>
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
    document.title = "Permissions";
</script>

<script type="text/javascript">
    function deleteData(id)
    {
        var r=confirm("Are you sure you want to delete this record?");
        if (r==true)
        {
            $.ajax({
                url: "<?php echo base_url().$this->router->fetch_module();?>/users/delRecord/"+id,
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
    function openModalAPIrequest(user_name,Password){
        $('#APImodal').modal('show');
        var html1='<h5>Health Insurance</h5>{\n' +
            '"username":"'+user_name+'",\n' +
            '"password":'+Password+',\n' +
            '"type":1\n' +
            '}';
        var html2='<h5>Gadget Insurance</h5>{\n' +
            '"username":"'+user_name+'",\n' +
            '"password":'+Password+',\n' +
            '"type":2\n' +
            '}';
        $("#healthAPI").html(html1);
        $("#gadgetAPI").html(html2);


    }
    document.title = "Users";
</script>