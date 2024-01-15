<?php
//Get Login user details
$ludata = array();
$ludata['utoken'] = $_SESSION['webpanel']['utoken'];
$ludata['id'] = $_SESSION['webpanel']['employee_id'];
$luserDetails = curlFunction(SERVICE_URL . '/api/getLoginUserDetails', $ludata);
$luserDetails = json_decode($luserDetails, true);
//echo "here<pre>";print_r($luserDetails);exit;
//echo $luserDetails['Data']['user_data'][0]['employee_id'];exit;
$last_login = date("d-m-Y H:i:s A", strtotime($luserDetails['Data']['user_data'][0]['last_login']));
?>
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
	<?php /**?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/flag-icon.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/icofont.css">
	link href="<?php echo base_url(); ?>assets/css/daterangepicker.css" rel="stylesheet">
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.v3.3.6.js"></script>
	<?php **/ ?>
	<!-- ico-font-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">

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
					<!--   -->
					<div class="col-md-9 col-1 display-none-sm">
						<div class="nav-listing" id="nav-top">
							<a href="#home" class="active">PROTECTING <i class=" fa fa-angle-down"></i></a>
							<a href="#news">INVESTING <i class=" fa fa-angle-down"></i></a>
							<a href="#contact">FINANCING <i class=" fa fa-angle-down"></i></a>
							<a href="#about">ADVISING <i class=" fa fa-angle-down"></i></a>
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
		<!-- header area start -->
		<div class="header-area header-bottom">
			<div class="container pad-left-lg">
				<div class="row">
					<div class="col-md-10 col-10">
						<span class="brand-name">Aditya Birla Health Insurance</span>
					</div>
					<div class="col-md-2 col-2">
						<div class="user-profile pull-right">
							<h4 class="user-name dropdown-toggle" data-toggle="dropdown"><span class="display-none-sm"><?php echo $_SESSION['webpanel']['employee_fname'] . ' ' . $_SESSION['webpanel']['employee_lname']; ?> <br />(<?php echo $last_login; ?>)</span><span></span> <i class="fa fa-angle-down"></i></h4>
							<div class="dropdown-menu">
								<div class="display-none-lg">
									<?php if (in_array('SaleAdminDashboard', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>saleadmindashboard">Sale's Admin Dashboard</a>
									<?php } ?>
									<?php if (in_array('ApplicationLogs', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>applicationlogs">Application Logs</a>
									<?php } ?>
									<?php if (in_array('SmDashboard', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>smdashboard">SM Dashboard</a>
									<?php } ?>
									<?php if (in_array('MyProfile', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>myprofile/addEdit">Profile</a>
									<?php } ?>
									<?php if (in_array('EnrollmentFormList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>enrollmentforms">Enrollment Forms</a>
									<?php } ?>
									<?php if (in_array('PermissionList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>permission">Permissions</a>
									<?php } ?>
									<?php if (in_array('RoleList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>roles">Roles</a>
									<?php } ?>
									<?php if (in_array('UserList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>users">Users</a>
									<?php } ?>
									<?php if (in_array('CreditorList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>creditors">Creditors</a>
									<?php } ?>
									<?php if (in_array('CreditorBranchList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>creditorbranches">Creditor Branches</a>
									<?php } ?>
									<?php if (in_array('SMCreditorMappingList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>smcreditors">SM Partner Mapping</a>
									<?php } ?>
									<?php if (in_array('LocationList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>locationmst">Location</a>
									<?php } ?>
									<?php if (in_array('DiscrepancyTypeList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>discrepancytype">Discrepancy Type</a>
									<?php } ?>
									<?php if (in_array('DiscrepancySubTypeList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>discrepancysubtype">Discrepancy Subtype</a>
									<?php } ?>
									<?php if (in_array('AssignmentDeclarationList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>assignmentdeclaration">Declaration</a>
									<?php } ?>
									<?php if (in_array('FamilyConstructList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>familyconstruct">Family Construct</a>
									<?php } ?>
									<?php if (in_array('InsurerList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>insurer">Insurer</a>
									<?php } ?>
									<?php if (in_array('PolicySubtypeList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>policysubtype">Policy Subtype</a>
									<?php } ?>
									<?php if (in_array('SumInsuredList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>suminsured">Sum Insured</a>
									<?php } ?>
									<?php if (in_array('ProductsList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>products">Products</a>
									<?php } ?>
									<?php if (in_array('LeadList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>customerleads">Leads</a>
									<?php } ?>
									<?php if (in_array('CustomerProposalsList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>customerproposals">Customer Proposals</a>
									<?php } ?>
									<?php if (in_array('DiscrepancyProposalList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>discrepancyproposals">Discrepancy Proposals</a>
									<?php } ?>
									<?php if (in_array('BOProposalList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>boproposals">BO Proposals</a>
									<?php } ?>
									<?php if (in_array('COProposalList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>coproposals">CO Proposals</a>
									<?php } ?>
									<?php if (in_array('UWProposalList', $this->RolePermission)) { ?>
										<a class="dropdown-item" href="<?php echo base_url(); ?>uwproposals">UW Proposals</a>
									<?php } ?>

								</div>
								<a class="dropdown-item" href="<?php echo base_url("home/logout"); ?>">Log Out</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- header area end -->
		<?php if (uri_string() == "policyproposal/addedit" || uri_string() == "policyproposal/preview") : ?>
			<div class="header-pre sticky-pre" id="myHeader-pre">
				<div class="row">
					<div class="col-md-6 col-12 mb-2 text-left un-id">Unique ID : <span class="id-txt" id="unique_trace_id">6700</span></div>
					<div class="col-md-6 col-12"><span class="premium-top dropdown-toggle pre-credital" data-toggle="dropdown" aria-expanded="true">Premium : <i class="fa fa-inr"></i><span class="total_premium" id="total_premium">0</span></span>
						<div id="premium_calculations_data" class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; left: 0px; will-change: transform;">
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- page title area end -->
		<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
			<div class="container-fluid">
				<div class="row">
					<!-- Left Menu Start-->
					<div class="col-md-2 mt-2 pd-right menu-lft">
						<div class="main-menu">
							<div class="menu-inner">
								<nav>
									<ul class="metismenu mt-3 display-none-sm" id="menu">

										<?php if (in_array('SaleAdminDashboard', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>saleadmindashboard"><i class="ti-dashboard"></i> <span>Sale's Admin Dashboard</span></a></li>
										<?php } ?>

										<?php if (in_array('ApplicationLogs', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>applicationlogs"><i class="ti-dashboard"></i> <span>Application Logs</span></a></li>
										<?php } ?>

										<?php if (in_array('SmDashboard', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>smdashboard"><i class="ti-dashboard"></i> <span>SM Dashboard</span></a></li>
										<?php } ?>

										<?php if (in_array('MyProfile', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>myprofile/addEdit"><i class="ti-user"></i> <span>Profile</span></a></li>
										<?php } ?>
										<?php if (in_array('EnrollmentFormList', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>enrollmentforms"><i class="ti-user"></i><span>Enrollment Forms</a></span></li>
										<?php } ?>
										<?php if (in_array('PermissionList', $this->RolePermission) || in_array('RoleList', $this->RolePermission) || in_array('UserList', $this->RolePermission)) { ?>

											<li class="mb-3">
												<a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-tab"></i><span>UAC</span></a>
												<ul class="collapse">
													<?php if (in_array('PermissionList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>permission"><span>Permissions</span></a></li>
													<?php } ?>
													<?php if (in_array('RoleList', $this->RolePermission)) { ?>
														<li><a href="<?php echo base_url(); ?>roles"><span>Roles</a></span></li>
													<?php } ?>
													<?php if (in_array('UserList', $this->RolePermission)) { ?>
														<li><a href="<?php echo base_url(); ?>users"><span>Users</span></a></li>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<?php if (in_array('CreditorList', $this->RolePermission) || in_array('CreditorBranchList', $this->RolePermission)) { ?>
											<li class="mb-3">
												<a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i><span>Creditors</span></a>
												<ul class="collapse">
													<?php if (in_array('CreditorList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>creditors"><span>Creditors</span></a></li>
													<?php } ?>
													<?php if (in_array('CreditorBranchList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>creditorbranches"><span>Creditor Branches</span></a></li>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>

										<?php if (in_array('SMCreditorMappingList', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>smcreditors"><i class="ti-user"></i> <span>SM Partner Mapping</span></a></li>
										<?php } ?>

										<?php if (in_array('DiscrepancyTypeList', $this->RolePermission) || in_array('DiscrepancySubTypeList', $this->RolePermission) || in_array('LocationList', $this->RolePermission) || in_array('AssignmentDeclarationList', $this->RolePermission)) { ?>
											<li class="mb-3">
												<a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-tab"></i><span>Master's</span></a>
												<ul class="collapse">
													<?php if (in_array('CompanyList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>companymst"><span>Companies</span></a></li>
													<?php } ?>
													<?php if (in_array('LocationList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>locationmst"><span>Location</span></a></li>
													<?php } ?>
													<?php if (in_array('DiscrepancyTypeList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>discrepancytype"><span>Discrepancy Type</span></a></li>
													<?php } ?>
													<?php if (in_array('DiscrepancySubTypeList', $this->RolePermission)) { ?>
														<li><a href="<?php echo base_url(); ?>discrepancysubtype"><span>Discrepancy Subtype</a></span></li>
													<?php } ?>
													<?php if (in_array('AssignmentDeclarationList', $this->RolePermission)) { ?>
														<li><a href="<?php echo base_url(); ?>assignmentdeclaration"><span>Declaration</a></span></li>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>


										<!-- new added by Jiten -->
										<?php if (in_array('FamilyConstructList', $this->RolePermission) || in_array('InsurerList', $this->RolePermission) || in_array('PolicySubtypeList', $this->RolePermission) || in_array('SumInsuredList', $this->RolePermission) || in_array('ProductsList', $this->RolePermission)) { ?>
											<li class="mb-3">
												<a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i><span>Products</span></a>
												<ul class="collapse">
													<?php if (in_array('FamilyConstructList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>familyconstruct"><span>Family Construct</span></a></li>
													<?php } ?>
													<?php if (in_array('InsurerList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>insurer"><span>Insurer</span></a></li>
													<?php } ?>
													<?php if (in_array('PolicySubtypeList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>policysubtype"><span>Policy Subtype</span></a></li>
													<?php } ?>
													<?php if (in_array('SumInsuredList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>suminsured"><span>Sum Insured</span></a></li>
													<?php } ?>
													<?php if (in_array('ProductsList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>products"><span>Products</span></a></li>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>
										<!-- new started -->

										<!-- Danish-->
										<?php if (in_array('LeadList', $this->RolePermission)) { ?>
											<li><a href="<?php echo base_url(); ?>customerleads"><i class="ti-user"></i> <span>Leads</span></a></li>
										<?php } ?>

										<?php if (in_array('CustomerProposalsList', $this->RolePermission) || in_array('DiscrepancyProposalList', $this->RolePermission) || in_array('BOProposalList', $this->RolePermission) || in_array('COProposalList', $this->RolePermission) || in_array('UWProposalList', $this->RolePermission)) { ?>
											<li class="mb-3">
												<a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i><span>Proposals</span></a>
												<ul class="collapse">
													<?php if (in_array('CustomerProposalsList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>customerproposals"><span>Customer Proposals</span></a></li>
													<?php } ?>
													<?php if (in_array('DiscrepancyProposalList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>discrepancyproposals"><span>Discrepancy Proposals</span></a></li>
													<?php } ?>
													<?php if (in_array('BOProposalList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>boproposals"><span>BO Proposals</span></a></li>
													<?php } ?>
													<?php if (in_array('COProposalList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>coproposals"><span>CO Proposals</span></a></li>
													<?php } ?>
													<?php if (in_array('UWProposalList', $this->RolePermission)) { ?>
														<li class="active"><a href="<?php echo base_url(); ?>uwproposals"><span>UW Proposals</span></a></li>
													<?php } ?>
												</ul>
											</li>
										<?php } ?>

										<!-- end-->


									</ul>
								</nav>
							</div>
						</div>
					</div>
					<!-- Left Menu End-->