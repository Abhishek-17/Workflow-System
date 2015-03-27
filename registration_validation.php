<?php
/*session_start();
$_SESSION['username']=$_POST['username'];
$_SESSION['password']=$_POST['password'];
$_SESSION['email']=$_POST['email'];
  */
 

require 'config.php';

$sqlcheck = "select * from login where email='" . $_POST['email'] ."'";
//echo $sqlcheck;
$result=mysqli_query($con,$sqlcheck);
$row = mysqli_fetch_array($result);
//echo $row;
if($row=="")
{   
   //echo $_POST['org_id']; exit(0);
    $sql="insert into login(username,password,status,email,org_id) values('$_POST[username]','$_POST[password]','manager','$_POST[email]','$_POST[org_id]')" ;
    mysqli_query($con,$sql);
    header( 'Location: login.php' ) ;
    mysqli_close($con);
}
else
{
    header( 'Location: registration.php' ) ;
    mysqli_close($con);
}

?>