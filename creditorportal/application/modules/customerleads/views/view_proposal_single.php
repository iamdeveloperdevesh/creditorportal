<?php
?>
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Leads Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Lead Id</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['lead_id']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Trace Id</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['trace_id']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">First name</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['first_name']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Last name</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['last_name']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Email ID</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['email_id']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Mobile Number</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['mobile_no']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Address Line 1</label>
                        <div class="input-group">
                            <textarea type="text" readonly id="address_line1" name="address_line1" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo $customer_data['address_line1']; ?></textarea>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Address Line 2</label>
                        <div class="input-group">
                            <textarea type="text" readonly id="address_line2" name="address_line2" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo $customer_data['address_line2']; ?></textarea>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Address Line 3</label>
                        <div class="input-group">
                            <textarea type="text" readonly id="address_line3" name="address_line3" class="form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend"><?php echo $customer_data['address_line3']; ?></textarea>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">location_on</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Pincode</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['pincode']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">No Of Lives</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['no_of_lives']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Loan Disbursement Date</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['loan_disbursement_date']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Loan Amount</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['loan_amt']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Loan Account No</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['lan_id']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Loan Tenure </label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['loan_tenure']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card mt-3" >
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Policy Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Plan Name</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['plan_name']; ?>"   readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ml-1">
                    <?php
                    $i=1;
                    foreach ($policyData as $policy){ ?>

                        <div class="col-md-4 mb-3">
                            <label for="trace_id" class="col-form-label">Policy Type<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['policy_subtype']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="trace_id" class="col-form-label">Sum Insure<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['cover']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="trace_id" class="col-form-label">Premium<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['premium_amount']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>

                  <?php
                    $i++;
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3" >
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Payment Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Payment Transaction ID</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0" value="<?php echo $customer_data['transaction_number']; ?>"  readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Payment Status</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0"  value="<?php echo $customer_data['payment_status']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="trace_id" class="col-form-label">Amount</label>
                        <div class="dataTables_filter input-group">
                            <input id="trace_id" name="sSearch_0"  value="<?php echo $customer_data['trans_amount']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3" >
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>COI Details - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $i=1;
                    foreach ($policyData as $policy){ ?>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Certificate Number<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['certificate_number']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Proposal Number<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['proposal_no']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Start Date<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['start_date']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">End Date<?php echo $i; ?></label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $policy['end_date']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <?php
                        $i++;
                    }
                    ?>

                </div>
            </div>
        </div>
        <?php if($marine_data != false){ ?>
            <div class="card mt-3" >
                <div class="cre-head">
                    <div class="row">
                        <div class="col-md-10 col-10">
                            <p>Marine Details - <i class="ti-user"></i></p>
                        </div>
                        <div class="col-md-2 col-2">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Invoice Number</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['Invoice_number']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Invoice Date</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['Invoice_date']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Mode of Shipment</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['Invoice_number']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">From Country</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['from_country']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">To Country</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['to_country']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">From City</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['from_city']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">To City</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['to_city']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Currency Type</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['currency_type']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Cargo Value</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['cargo_value']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Rate of Exchange</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['rate_of_exchange']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Date of Shipment</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0"  value="<?php echo $marine_data['date_of_shipment']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Bill Number</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['Bill_number']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Bill Date</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['Bill_date']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Place of Issuance</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['place_of_issuence']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Subject Matter Insured</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['subject_matter_insured']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="trace_id" class="col-form-label">Type of Shipment</label>
                            <div class="dataTables_filter input-group">
                                <input id="trace_id" name="sSearch_0" value="<?php echo $marine_data['type_of_shipment']; ?>" readonly type="text" class="searchInput form-control" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       <?php }
        ?>

    </div>
</div>