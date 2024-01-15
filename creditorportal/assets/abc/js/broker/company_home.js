 $(document).ready( function () {
    $('#table_id').DataTable({
        "pageLength": 10
    });

    $("#settingsBtn").on("click", function() {
        var arr = [];
        var allRights = $("#allRights input[type='checkbox']");


        for(i = 0; i < allRights.length; ++i) { 
            if(allRights[i].checked) {
                arr.push(allRights[i].value);
            } 
        }

        $.post("/broker/save_blocked_routes",{
            "company_id":$("#comId").val(),
            "block_id":arr.join(",")
        }, function(e) {
            swal("Information", "Rights Successfully Added", "success");

        });

        
    });
});

function settings(comId, uriId) {
    $("#comId").val(comId);

    var uriIdArr = uriId.split(",");
    var allRights = $("#allRights input[type='checkbox']");

    for(i = 0; i < allRights.length; ++i) {
        if(uriIdArr.includes(allRights[i].value)) {
            allRights[i].checked = true;
        }
    }

    $("#settings").modal("show");
}