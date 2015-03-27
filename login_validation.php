<?php

require 'config.php';
$sqlcheck = "select * from login where email='" . $_POST['email'] . "'" . " and password='" . $_POST['password'] . "'";
echo $sqlcheck;
$result = mysqli_query($con,$sqlcheck);
$row = mysqli_fetch_array($result);
if($row=="")
{
    header( 'Location: login.php' ) ;
    mysql_close($con);
}
else
{   session_start();
    $_SESSION['email']=$_POST['email'];
    if($row['status']=="root")
        header( 'Location: initialize.php' ) ;
   // else if($row['status']=="manager")
       // header( 'Location: start.php' ) ;
    else
        header( 'Location: blank.php' ) ;
    mysql_close($con);
}
?>
