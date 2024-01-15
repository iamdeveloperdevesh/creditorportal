<!-- Center Section -->
<div class="col-md-10">


    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-8 col-6">
                        <p>Product Details</p>
                    </div>

                        <div class="col-md-4 col-6">
                            <a href="<?php echo base_url();?>tele_product_config">
                                <button class="btn btn-sec add-btn fl-right">Add Product <span class="display-none-sm"><span class="material-icons spn-icon">add_circle_outline</span></span></button>
                            </a>
                        </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 table-responsive scroll-table">
                    <table id="policyTable" class=" table table-bordered non-bootstrap display pt-3 pb-3">
                        <thead class="tbl-cre">
                        <tr>
                            <th>Plan Name</th>
                            <th>Policy Type</th>
                            <th>Status</th>
                            <th>Actions</th>
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
<script>
    $( document ).ready(function() {
        getPolicyData();
    });

    function getPolicyData() {
        $.ajax({
            url: "tele_product_config/getPolicyDetails",
            async: false,
            type: "POST",
            dataType:"json",
            success: function(res){
              //  console.log(res);
                $("#policyTable").DataTable({
                    destroy: true,
                    order: [],
                    data:res.aaData,
                    "pagingType": "simple_numbers",
                    columns:[

                        {data: 0},
                        {data: 1},
                        {data: 2},
                        {data: 3},

                    ]
                });
            }
        });
    }

    function deleteData(id)
    {
        var r=confirm("Are you sure you want to delete this record?");
        if (r==true)
        {
            $.ajax({
                url: "tele_product_config/deletePolicy",
                async: false,
                type: "POST",
                data:{id},
                dataType: 'json',
                success: function(data2){
                   if(data2.status == 200){
                      alert(data2.msg);
                       getPolicyData();
                   }else{
                       alert(data2.msg);
                   }
                }
            });
        }
    }
    document.title = "Product List";
</script>