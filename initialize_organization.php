<?php
$con=mysqli_connect("localhost","root","","workflows");
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if(isset($_POST['org_name']) && isset($_POST['org_address']) && isset($_POST['org_payment']))
{
    if($_POST['org_name']!="" && $_POST['org_address']!="" && $_POST['org_payment']!="")
    {
        $sql="insert into organization(org_name,org_address,org_payment) values('$_POST[org_name]' ,'$_POST[org_address]' ,$_POST[org_payment] )";
        //echo $sql;
        $result=mysqli_query($con,$sql);
        header('Location: initialize.php');
    }
    else
    {
        echo 'Some field was not filled <br>
        <html>
        <body>
        <h1><font face="Arial" size="+1" color="#FF2222">ADD ORGANIZATION</font></h1>
        <form action="initialize_organization.php" method="post">
            ORGANIZATION NAME : <input type="text" name="org_name" value="'.$_POST['org_name'].'" /><br><br>
            ORGANIZATION ADDRESS : <input type="text" name="org_address" value="'.$_POST['org_address'].'" /><br><br>
            PAYMENT DETAILS: <input type="text" name="org_payment" value="'.$_POST['org_payment'].'" /><br><br>
            <input type="submit" value="CREATE" />
        </form> 
        </body>
        </html>';
    }
}
else
{

echo '
    <html>
    <body>
    <h1><font face="Arial" size="+1" color="#FF2222">ADD ORGANIZATION</font></h1>
    <form action="initialize_organization.php" method="post">
        ORGANIZATION NAME : <input type="text" name="org_name" /><br><br>
        ORGANIZATION ADDRESS : <input type="text" name="org_address" /><br><br>
        PAYMENT DETAILS: <input type="text" name="org_payment" /><br><br>
        <input type="submit" value="CREATE" />
    </form> 
    </body>
    </html>';
}
?>