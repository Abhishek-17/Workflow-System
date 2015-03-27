<?php
if(isset($_SESSION['email'])) unset($_SESSION['email']);
session_destroy();
header('Location: login.php')
?>
