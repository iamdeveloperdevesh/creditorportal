<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


function getFooterData()
{
	$response = array();
	$CI = &get_instance();
	$bestseller_query = $CI->db->query("Select product_id, product_name, link, product_code, product_final_price, short_description From tbl_products Where is_best_seller = '1' AND status='Active' order by product_id desc");

	if ($bestseller_query->num_rows() >= 1) {
		$response['bestseller_products'] = $bestseller_query->result_array();

		foreach ($response['bestseller_products'] as $key => $val) {
			$prod_img_query = $CI->db->query("Select `image_id`, `imagename`, `default`, `product_thumbnail_image` From tbl_productimages Where product_id = '" . $val['product_id'] . "' AND status='Active' order by `default` desc limit 0,1");

			if ($prod_img_query->num_rows() >= 1) {
				$response['bestseller_products'][$key]['prod_images'] = $prod_img_query->result_array();
			}
		}
	}

	$saleoff_query = $CI->db->query("Select product_id, product_name, link, product_code, product_final_price, short_description From tbl_products Where is_sale_off = '1' AND status='Active'");

	if ($saleoff_query->num_rows() >= 1) {
		$response['saleoff_products'] = $saleoff_query->result_array();

		foreach ($response['saleoff_products'] as $key => $val) {
			$prod_img_query = $CI->db->query("Select `image_id`, `imagename`, `default`, `product_thumbnail_image` From tbl_productimages Where product_id = '" . $val['product_id'] . "' AND status='Active' order by `default` desc limit 0,1");

			if ($prod_img_query->num_rows() >= 1) {
				$response['saleoff_products'][$key]['prod_images'] = $prod_img_query->result_array();
			}
		}
	}

	$newarrival_query = $CI->db->query("Select product_id, product_name, link, product_code, product_final_price, short_description From tbl_products Where is_new_arrival = '1' AND status='Active'");

	if ($newarrival_query->num_rows() >= 1) {
		$response['newarrival_products'] = $newarrival_query->result_array();

		foreach ($response['newarrival_products'] as $key => $val) {
			$prod_img_query = $CI->db->query("Select `image_id`, `imagename`, `default`, `product_thumbnail_image` From tbl_productimages Where product_id = '" . $val['product_id'] . "' AND status='Active' order by `default` desc limit 0,1");

			if ($prod_img_query->num_rows() >= 1) {
				$response['newarrival_products'][$key]['prod_images'] = $prod_img_query->result_array();
			}
		}
	}
	return $response;
}
function getEvents()
{
	$event_menu = array();
	$CI = &get_instance();
	$query = $CI->db->query("Select event_id, link,event_name from tbl_events Where status='Active'");
	if ($query->num_rows() >= 1) {
		$event_menu = $query->result_array();
	}
	return  $event_menu;
}

function getCategories()
{
	$product_category_menu = array();
	$CI = &get_instance();
	$query = $CI->db->query("Select category_id, link, category_name from tbl_categories Where status='Active'");
	if ($query->num_rows() >= 1) {
		$product_category_menu = $query->result_array();
	}
	return  $product_category_menu;
}

function getNoOfProductInCart($cart_session)
{
	$CI = &get_instance();
	// $query= $CI->db->query("Select sum(quantity) as totalProduct from tbl_shoppingcarts Where cart_session='".$cart_session."' ");
	$query = $CI->db->query("Select count(`product_id`) as totalProduct from tbl_shoppingcarts Where cart_session='" . $cart_session . "' ");

	if ($query->num_rows() >= 1) {
		return $query->result();
	} else {
		return false;
	}
}


if (!function_exists('dd')) {
	function dd()
	{
		array_map(function ($x) {
			dump($x);
		}, func_get_args());
		die;
	}
}

function checkUWCase(int $lead_id, int $creditor_id, int $plan_id = 0)
{

	$CI = &get_instance();
	$CI->load->model('commonapi/commonapimodel');

	$has_gci = $has_gpa_or_gci = false;
	$proposal_payment_data['sum_insured'] = 0;

	// if plan_id not being passed, fetch it from lead_details table
	if (!$plan_id) {

		$lead_details = $CI->commonapimodel->commongetdata('lead_details', 'plan_id', "lead_id = " . $lead_id);

		foreach ($lead_details as $lead_detail) {

			$plan_id = $lead_detail['plan_id'];
		}
	}

	//checking for only bundled products
	$master_policy_arr = $CI->apimodel->getdata('master_plan', 'plan_name', "plan_id = $plan_id");

	if(isset($master_policy_arr[0]['plan_name']) && $master_policy_arr[0]['plan_name'] != ''){

		if(strtolower(trim($master_policy_arr[0]['plan_name'])) != 'bundled'){

			return false;
		}
	}

	//master_policy_id and policy_sub_type_id mapping to check for gci
	$master_policy_arr = $CI->commonapimodel->commongetdata('master_policy', 'policy_id, policy_sub_type_id', "plan_id = $plan_id AND creditor_id = $creditor_id");
	//echo "<pre>";print_r($master_policy_arr);exit;

	$policy_sub_type_id_arr = [];

	foreach ($master_policy_arr as $master_policy) {

		$policy_sub_type_id_arr[$master_policy['policy_id']] = $master_policy['policy_sub_type_id'];
	}

	//get master_quotes data using lead_id
	$master_quotes = $CI->commonapimodel->getMasterQuotesByLeadID('master_policy_id, age, sum_insured', $lead_id);

	foreach ($master_quotes as $master_quote) {

		if ($master_quote['age'] > 55 && $policy_sub_type_id_arr[$master_quote['master_policy_id']] == 3) {

			$has_gci = true;
		}

		if (in_array($policy_sub_type_id_arr[$master_quote['master_policy_id']], [2, 3])) {

			$has_gpa_or_gci = true;
		}

		$proposal_payment_data['sum_insured'] += $master_quote['sum_insured'];
	}
	
	if ($has_gci) {
		
		return true;
	}
	else {

		if ($has_gpa_or_gci) {

			$uw_case_raw_arr = $CI->commonapimodel->commongetdata('uw_cases', 'creditor_id, sum_insured', 'creditor_id = ' . $creditor_id . ' AND master_plan_id = ' . $plan_id);
			
			$uw_case_arr = [];
			if (!empty($uw_case_raw_arr)) {

				foreach ($uw_case_raw_arr as $key => $uw_case) {

					$uw_case_arr[$uw_case['creditor_id']] = $uw_case['sum_insured'];
				}

				if (isset($uw_case_arr[$creditor_id]) && $proposal_payment_data['sum_insured'] >= $uw_case_arr[$creditor_id]) {

					return true;
				}
			}
		}
	}

	return false;
}