<!-- start: CSS -->
	<link id="bootstrap-style" href="<?PHP echo base_url();?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?PHP echo base_url();?>css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="<?PHP echo base_url();?>css/bootstrap-glyphicons.css" rel="stylesheet">
    <link href="<?PHP echo base_url();?>css/daterangepicker.css" rel="stylesheet">
 
	<link id="base-style" href="<?PHP echo base_url();?>css/style.css" rel="stylesheet">
	<link id="base-style-responsive" href="<?PHP echo base_url();?>css/style-responsive.css" rel="stylesheet">
	 
    <link href="<?PHP echo base_url();?>css/colorbox.css" type="text/css" rel="stylesheet" />
	<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
	 end: CSS -->
        
<!-- start: Favicon -->
	<link rel="shortcut icon" href="img/favicon.ico">
	<!-- end: Favicon -->
	
	
	<!-- start: JavaScript-->
	<script src="<?PHP echo base_url();?>js/jquery-1.9.1.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery-migrate-1.0.0.min.js"></script>	
	<script src="<?PHP echo base_url();?>js/jquery-ui-1.10.0.custom.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.ui.touch-punch.js"></script>
	<script src="<?PHP echo base_url();?>js/modernizr.js"></script>
	<script src="<?PHP echo base_url();?>js/bootstrap.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.cookie.js"></script>
	<script src='<?PHP echo base_url();?>js/fullcalendar.min.js'></script>
	
	<script src='<?PHP echo base_url();?>js/jquery.dataTables.min.js'></script>
	<script src='<?PHP echo base_url();?>js/datatable.js'></script>
	
	<script src="<?PHP echo base_url();?>js/excanvas.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.flot.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.flot.pie.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.flot.stack.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.flot.resize.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.chosen.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.uniform.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.cleditor.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.noty.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.elfinder.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.raty.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.iphone.toggle.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.uploadify-3.1.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.gritter.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.imagesloaded.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.masonry.min.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.knob.modified.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.sparkline.min.js"></script>
	<script src="<?PHP echo base_url();?>js/counter.js"></script>
	<script src="<?PHP echo base_url();?>js/retina.js"></script>
	<script src="<?PHP echo base_url();?>js/custom.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.colorbox.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.form.js"></script>
	<script src="<?PHP echo base_url();?>js/jquery.validate.js"></script>
	<script src="<?PHP echo base_url();?>js/moment.min.js"></script>
	<script src="<?PHP echo base_url();?>js/daterangepicker.js"></script>
	
	<link href="<?PHP echo base_url();?>css/select2.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="<?PHP echo base_url();?>js/select2.min.js"></script>
	
	<!-- CK Editor plugins -->
	  <script type="text/javascript" src="<?PHP echo base_url();?>js/ckeditor/ckeditor.js"></script>
	  <script type="text/javascript" src="<?PHP echo base_url();?>js/ckeditor/adapters/jquery.js"></script>
   
	<!-- end: JavaScript-->
	
	<link href="<?PHP echo base_url();?>css/jquery.dataTables.css" rel="stylesheet">
	<script>
		function setTabIndex(){
			var tabindex = 1;
			$('input,select,textarea,.icon-plus,.icon-minus,button,a').each(function() {
				if (this.type != "hidden") {
					var $input = $(this);
					$input.attr("tabindex", tabindex);
					tabindex++;
				}
			});
		}
		
		$(function(){
			setTabIndex();
			$(".select2").each(function(){
				$(this).select2({
					placeholder: "Select",
					allowClear: true
				});
				$("#s2id_"+$(this).attr("id")).removeClass("searchInput");
			});
			//document.title = "Home - Commodity Alpha";
//			$(".inline").colorbox({inline:true, width:"50%",  onComplete : function() { 
//       $(this).colorbox.resize(); 
//    } });

			$(".dataTables_filter input.hasDatepicker").change( function () {
				/* Filter on the column (the index) of this element*/ 
				oTable.fnFilter( this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $(".searchInput").index(this) ) );
			});
			
			window.scrollTo(0,0);
		});
		
		function displayMsg(type,msg)
		{
			
			$.noty({
				text:msg,
				layout:"topRight",
				type:type
			});
		}
	</script>