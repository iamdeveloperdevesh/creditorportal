<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>ABHI - Creditor</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!--font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons&style=outlined" rel="stylesheet">
	<!-- <style type="text/css">
		@font-face {
			font-family: 'Titillium';
			src: url(assets/fonts/TitilliumWeb-Regular.ttf);
		}
	</style> -->

	<!-- Font Awesome-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">

	<!-- Flag icon-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/flag-icon.css">

	<!-- ico-font-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/icofont.css">

	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">

	<link href="<?php echo base_url(); ?>assets/css/daterangepicker.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/noty_theme_default.css" rel="stylesheet">

	<!-- Bootstrap css-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/themify-icons.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/metisMenu.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slicknav.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/typography.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default-css.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/styles.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/responsive.css">

	<link href="<?PHP echo base_url(); ?>assets/css/jquery.dataTables.css" rel="stylesheet">
	<link href="<?PHP echo base_url(); ?>assets/css/jquery.noty.css" rel="stylesheet">

	<link href="<?php echo base_url(); ?>assets/css/select2.css" type="text/css" rel="stylesheet" />

	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-material-datetimepicker.css" />
	
	<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" />

	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/essential-plugins.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>

	<!-- CK Editor plugins -->
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.v3.3.6.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.form.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/additional-methods.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>

	<!-- Datatable plugin-->
	<script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/jquery.dataTables.min.js'></script>
	<script type="text/javascript" src='<?PHP echo base_url(); ?>assets/js/datatable.js'></script>
	<script type="text/javascript" src="<?PHP echo base_url(); ?>assets/js/jquery.noty.js"></script>
	<script type="text/javascript" src="<?PHP echo base_url(); ?>assets/js/select2.min.js"></script>
	<script type="text/javascript" src="<?PHP echo base_url(); ?>assets/js/moment-with-locales.js"></script>
	<script type="text/javascript" src="<?PHP echo base_url(); ?>assets/js/bootstrap-material-datetimepicker.js"></script>
	
	<!--<script type="text/javascript" src="<?PHP echo base_url(); ?>assets/js/bootstrap-datetimepicker.min.js"></script>-->
	
	<script>
		function setTabIndex() {
			var tabindex = 1;
			$('input,select,textarea,.icon-plus,.icon-minus,button,a').each(function() {
				if (this.type != "hidden") {
					var $input = $(this);
					$input.attr("tabindex", tabindex);
					tabindex++;
				}
			});
		}

		$(function() {
			setTabIndex();
			$(".select2").each(function() {
				$(this).select2({
					placeholder: "Select",
					allowClear: true
				});
				$("#s2id_" + $(this).attr("id")).removeClass("searchInput");
			});
			//document.title = "Home - Commodity Alpha";
			//$(".inline").colorbox({inline:true, width:"50%",  onComplete : function() { 
			//$(this).colorbox.resize(); 
			//} });

			$(".dataTables_filter input.hasDatepicker").change(function() {
				/* Filter on the column (the index) of this element*/
				oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $(".searchInput").index(this)));
			});

			window.scrollTo(0, 0);
		});

		function displayMsg(type, msg) {
			$.noty({
				text: msg,
				layout: "topRight",
				type: type
			});
		}
	</script>
</head>
<style>
	.dtp-buttons>button.btn {
		border: none;
		border-radius: 2px;
		position: relative;
		box-shadow: none;
		color: rgba(0, 0, 0, 0.87);
		padding: 5px 16px;
		font-size: 12px;
		margin: 10px 1px;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 0;
		will-change: box-shadow, transform;
		transition: box-shadow 0.2s cubic-bezier(0.4, 0, 1, 1), background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
		outline: 0;
		cursor: pointer;
		text-decoration: none;
		background: transparent;
	}

	.dtp-buttons>button.btn:hover,
	.dtp-buttons>button.btn:focus {
		background-color: rgba(153, 153, 153, 0.2);
	}

	.dtp {
		overflow-y: auto !important;
	}

	.dtp>.dtp-content>.dtp-date-view>header.dtp-header,
	.dtp div.dtp-date,
	.dtp div.dtp-time,
	.dtp table.dtp-picker-days tr>td>a.selected {
		background: #6b3031 !important;
	}

	.error {
		color: red;
	}
</style>

<body class="body-bg">
	<!-- preloader area start -->
	<div id="preloader">
		<div class="loader"></div>
	</div>
	<!-- preloader area end -->
	<!-- main wrapper start -->
	<div class="horizontal-main-wrapper">
		<!-- main header area start -->
		<div class="mainheader-area">
			<div class="container con-left">
				<div class="row align-items-center">
					<div class="col-md-3 col-9">
						<div class="logo">
							<a href="<?php echo base_url(); ?>home"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="logo"></a>
						</div>
					</div>
					
					<div class="col-3 col-md-9 text-right">
						<a href="javascript:void(0);" class="icon display-none-lg sidebar-icon" onclick="open_side_mob()">
							<i class="fa fa-bars"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<!-- main header area end -->
		<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
			<div class="container-fluid">
				<div class="row">