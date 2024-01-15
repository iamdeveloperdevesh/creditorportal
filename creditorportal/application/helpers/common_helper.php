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

