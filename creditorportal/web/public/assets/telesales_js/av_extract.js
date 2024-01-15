function extractBase_Av(type) {

 $.ajax({
  url: "/getextractData",
  type: "POST",
  async: false,
  data:{'type':type},
  success: function (e)
  {
   var res = JSON.parse(e);
   if(res.status == 400){
    swal("Alert",res.message,"warning");
   }else{
    var a = document.createElement("a");
    var url = '/resources/avextract/'+res.filename;
    
    a.href = url;
    a.download = res.filename;
    document.body.append(a);
    a.click();
    a.remove();
    window.URL.revokeObjectURL(url);
    swal("Success",res.message,"success");
    // location.reload();
   }
   }

 });


}

function SaveMTDinFolder() {
 $.ajax({
  url: "/SaveMTDdumps",
  type: "POST",
  async: false,
  success: function (e)
  {
   var res = JSON.parse(e);
   if(res.status == 400){
    swal("Alert",res.message,"warning");
   }else{

    swal("Success",res.message,"success");
    // location.reload();
   }
  }

 });
}
function SaveMISinFolder() {
 $.ajax({
  url: "/SaveMISdumps",
  type: "POST",
  async: false,
  success: function (e)
  {
   var res = JSON.parse(e);
   if(res.status == 400){
    swal("Alert",res.message,"warning");
   }else{

    swal("Success",res.message,"success");
    // location.reload();
   }
  }

 });
}

function LoadTable(){
 $.ajax({
  url: "/get_datatable_MIS_ajax",
  type: "POST",
  async: false,
  dataType: 'json',
  success: function (res)
  {
 $("#mistable").DataTable({
  destroy: true,
  order: [],
  data:res.data,
  "pagingType": "simple_numbers",
  columns:[

   {data: 0},
   {data: 1},
   {
    data: 2,
    render: (d, t, r, m) => {
     return `<a href="/DataMisTable?path=${d}"><button type="button"  class="btn btn-link"><i class="fa fa-download"></i></button><a>`
    }
   },
  ],
  fnRowCallback:(nRow, aData, iDisplayIndex, iDisplayIndexFull) => {

   $('td:eq(2)', nRow).html(`<a href="/DataMisTable?path=${aData[2]}"><button type="button"  class="btn btn-link"><i class="fa fa-download"></i></button><a>`);
  }
 });
  }
 });
}