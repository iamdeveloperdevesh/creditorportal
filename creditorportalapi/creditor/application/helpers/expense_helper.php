<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function expense_cal($expense = null,$order_id)
{
  $CI  =& get_instance();
  $save_expense = array();
  $faculty_comm = array();
	if(is_array($expense) && count($expense) > 0)
  {
    $otherdiscount          = find_other_expense($order_id);
    $commission_paid        = array();
    if(is_array($otherdiscount) && count($otherdiscount) > 0)
    {
      foreach($otherdiscount as $key => $val)
      {
        if($val['discount_id'])
        {
          if($val['percent_amount'] == 1)
          {
            $save_expense['discount'] = $val['without_tax_amount']*($val['voucher_amount'])/100;
          }
          else
          {
            $save_expense['discount'] = $val['voucher_amount'];
          }
          $save_expense['refferal']   = $val['referral_points'];
        }
        
        if($val['discount_type'] == 'Vendors')
        { 
          if($val['percent_amount'] == 1)
          {
            $commission_paid['discount'] = $val['without_tax_amount']*($val['voucher_amount'])/100;
          }
          else
          {
            $commission_paid['discount']    = $val['voucher_amount'];
          }
        }
      }
    }
    
    foreach($expense as $value)
    {
      if($value['product_type']     == 'course')
      {
        $faculty_or_student_expense_cou = fac_student($value['product_id'],$value['product_type']);
        if(is_array($faculty_or_student_expense_cou) && count($faculty_or_student_expense_cou) > 0)
        {
          foreach($faculty_or_student_expense_cou as $values)
          {
            if(isset($values['faculty_id']) && !empty($values['faculty_id']) && $values['faculty_id'] != 0)
            {
              if($values['expense_type_faculty'] == 'Percent')
              {
                $save_expense['course']['faculty'][] = $value['without_tax_amount']*($values['faculty_commission'])/100;
                $commission_paid['course']['faculty'][] = $value['without_tax_amount']*($values['faculty_commission'])/100;
                $faculty_comm['course_comm']['faculty'][] = $value['without_tax_amount']*($values['faculty_commission'])/100;
                
              }
              else
              {
                $save_expense['course']['faculty'][] = $values['faculty_commission'];
                $commission_paid['course']['faculty'][] = $values['faculty_commission'];
                $faculty_comm['course_comm']['faculty'][] = $values['faculty_commission'];
              }
            }

            if(isset($values['bstudent_id']) && !empty($values['bstudent_id']) && $values['bstudent_id'] != 0)
            {
              if($values['expense_type_student'] == 'Percent')
              {
                $save_expense['course']['student'][]  = $value['without_tax_amount']*($values['student_commission'])/100;
                $commission_paid['course']['student'][]  = $value['without_tax_amount']*($values['student_commission'])/100;
              }
              else
              {
                $save_expense['course']['student'][] = $values['student_commission'];
                $commission_paid['course']['student'][] = $values['student_commission'];
              }
            }
          }
        }
      }
      elseif($value['product_type'] == 'module')
      {
        $idcourse                       = find_course($value['product_id'],$value['product_type']);
        $faculty_or_student_expense_mod = fac_student($idcourse[0]['course_id'],'course');
        if(is_array($faculty_or_student_expense_mod) && count($faculty_or_student_expense_mod) > 0)
        {
          foreach($faculty_or_student_expense_mod as $values)
          {
            if(isset($values['faculty_id']) && !empty($values['faculty_id']) && $values['faculty_id'] != 0)
            {
              if($values['expense_type_faculty'] == 'Percent')
              {
                $save_expense['module']['faculty'][]  = $value['without_tax_amount']*($values['faculty_commission'])/100; 
                $commission_paid['module']['faculty'][]  = $value['without_tax_amount']*($values['faculty_commission'])/100; 
                $faculty_comm['module_comm']['faculty'][]  = $value['without_tax_amount']*($values['faculty_commission'])/100; 
              }
              else
              {
                $save_expense['module']['faculty'][]  = $values['faculty_commission'];
                $commission_paid['module']['faculty'][]  = $values['faculty_commission'];
                $faculty_comm['module_comm']['faculty'][]  = $values['faculty_commission'];
              }
            }

            if(isset($values['bstudent_id']) && !empty($values['bstudent_id']) && $values['bstudent_id'] != 0)
            {
              if($values['expense_type_student'] == 'Percent')
              {
                $save_expense['module']['student'][]  =  $value['without_tax_amount']*($values['student_commission'])/100; 
                $commission_paid['module']['student'][]  =  $value['without_tax_amount']*($values['student_commission'])/100; 
              }
              else
              {
                $save_expense['module']['student'][]  = $values['student_commission'];
                $commission_paid['module']['student'][]  = $values['student_commission'];
              }
            } 
          }
        }
      }
    }
  }
  return array($save_expense,$commission_paid,$faculty_comm);
}


function find_other_expense($order_id)
{
  $CI  =& get_instance();
  $temp = $CI->session->userdata('in5minutes');
  
  if(array_key_exists('user_id',$temp)) 
  {
    $user_id        = $temp['user_id'];
  } 
  else if(isset($uid) && !empty($uid)) 
  {
    $user_id = $uid;
  }
  
  

  $condition      = "o.user_id = '".$user_id."' and o.order_id='".$order_id."' ";

  $CI -> db -> select('d.voucher_amount,d.percent_amount,u.referral_points,o.discount_id,o.without_tax_amount,d.discount_type');
  $CI -> db -> from('tbl_orders as o');
  $CI -> db -> join('tbl_shopping_cart as s', 'o.order_id  = s.order_id', 'left');
  $CI -> db -> join('tbl_discount as d', 'o.discount_id  = d.discount_id', 'left');
  $CI -> db -> join('tbl_users as u', 'o.user_id  = u.user_id', 'left');
  $CI->db->group_by('o.order_id');
  $CI -> db -> where($condition);
  $query = $CI -> db -> get();

  if($query -> num_rows() >= 1)
  {
    return $query->result_array();
  }
  else
  {
    return false;
  }
}

function vendor_expense($expense = null,$order_id) {
    $CI  =& get_instance();
	if(is_array($expense) && count($expense) > 0)
  {
    $otherdiscount          = find_other_expense($order_id);
    $commission_paid        = "";
    if(is_array($otherdiscount) && count($otherdiscount) > 0)
    {
      foreach($otherdiscount as $key => $val)
      {
          if($val['discount_type'] == 'Vendors')
        { 
          if($val['percent_amount'] == 1)
          {
            $commission_paid = $val['without_tax_amount']*($val['voucher_amount'])/100;
          }
          else
          {
            $commission_paid    = $val['voucher_amount'];
          }
        }
          
      }
      return $commission_paid;
    }
  }
}

function find_course($id,$type)
{
  $CI  =& get_instance();
  if($type == 'module')
  {
    $condition      = "module_id = '".$id."'";

    $CI -> db -> select('course_id');
    $CI -> db -> from('tbl_modules');
    $CI -> db -> where($condition);
    $query = $CI -> db -> get();

    if($query -> num_rows() >= 1)
    {
      return $query->result_array();
    }
    else
    {
      return false;
    }
  }
}

function fac_student($id,$type)
{
  $CI  =& get_instance();
  $temp = $CI->session->userdata('in5minutes');
  if(array_key_exists('user_id',$temp)) 
  {
   $user_id        = $temp['user_id'];
  } 
  else if(isset($uid) && !empty($uid)) 
  {
    $user_id = $uid;
  }

  if($type == 'course')
  {
    $condition      = "e.course_id = '".$id."'  and e.status='Active'";

    $CI -> db -> select('e.*');
    $CI -> db -> from('tbl_expenses as e');
    $CI -> db -> where($condition);
    $query = $CI -> db -> get();

    if($query -> num_rows() >= 1)
    {
      return $query->result_array();
    }
    else
    {
      return false;
    }
  }
}

function total_expense($save_expense)
{
  $discount_amt = 0;
  if(is_array($save_expense) && count($save_expense) > 0)
  {
    $discount_amt = 0;
    foreach($save_expense as $key=> $value)
    {
      if($key == 'discount')
      {
        $discount_amt +=  $value;
      }
      
      if($key == 'refferal')
      {
        $discount_amt +=  $value;
      }
    
      if($key == 'course')
      {
        foreach($value as $key => $val)
        {
          foreach($val as $key2 => $values_res)
          {
            $discount_amt +=  $values_res;
          }
        }
      }
      else if($key ==  'module')
      {
        foreach($value as $key => $vals)
        {
          foreach($vals as $keys2 => $values_rses)
          {
            $discount_amt +=  $values_rses;
          }
        }
      }
    }
  }
  return $discount_amt;
}

function total_commission($commission_paid)
{
  $com_amt = 0;
  if(is_array($commission_paid) && count($commission_paid) > 0)
  {
    $com_amt = 0;
    foreach($commission_paid as $key=> $value)
    {
      if($key == 'discount')
      {
        $com_amt +=  $value;
      }
      
      if($key == 'refferal')
      {
        $com_amt +=  $value;
      }
    
      if($key == 'course')
      {
        foreach($value as $key => $val)
        {
          foreach($val as $key2 => $values_res)
          {
            $com_amt +=  $values_res;
          }
        }
      }
      else if($key ==  'module')
      {
        foreach($value as $key => $vals)
        {
          foreach($vals as $keys2 => $values_rses)
          {
            $com_amt +=  $values_rses;
          }
        }
      }
    }
  }
  return $com_amt;
}

function total_commission_faculty($faculty_comm)
{

    $com_amt_facl_module = 0;
    $com_amt_facl_course = 0;
  if(is_array($faculty_comm) && count($faculty_comm) > 0)
  {
    $com_amt_facl_module = 0;
    $com_amt_facl_course = 0;
    
    foreach($faculty_comm as $key=> $value)
    {
      if($key == 'course_comm')
      {
        foreach($value as $key => $val)
        {
          foreach($val as $key2 => $values_res)
          {
            $com_amt_facl_course +=  $values_res;
          }
        }
      }
      else if($key ==  'module_comm')
      {
        foreach($value as $key => $vals)
        {
          foreach($vals as $keys2 => $values_rses)
          {
            $com_amt_facl_module +=  $values_rses;
          }
        }
      }
    }
  }
  return array($com_amt_facl_course,$com_amt_facl_module);
}