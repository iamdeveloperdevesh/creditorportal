<?php
class Privilegeduser
{
    // override User method
    public function getByUsername($user_id) 
    {	
    	$user_privilage = array();
		$sel_roleid = mysql_query("select role_id from tbl_admin_users where user_id='".$user_id."' ");
		$row_rolid = mysql_fetch_array($sel_roleid);
		
		$role_id = $row_rolid['role_id'];
		$sql = mysql_query("SELECT t2.perm_desc FROM role_perm as t1 JOIN permissions as t2 ON t1.perm_id = t2.perm_id WHERE t1.role_id = '$role_id'");
 		while($row = mysql_fetch_object($sql))
 		{
 			$user_privilage[$row->perm_desc] = true;
 		}   
 		$_SESSION["webadmin"]["user_privilage"] = $user_privilage;    		
    }
 
    // check if user has a specific privilege
    public function hasPrivilege($perm) 
    {
		$user_privilage = $_SESSION["webadmin"]["user_privilage"];
		//print_r($user_privilage[$perm]);
     	return isset($user_privilage[$perm]);       
    }
	
	
}
?>