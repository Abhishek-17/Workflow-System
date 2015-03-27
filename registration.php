<html>
<body>
<h1><font face="Arial" size="+1" color="#FF2222">Register</font></h1>
<form action="index.php" method="post">
<input type="submit" value="BACK"/>
</form>
<form action="registration_validation.php" method="post">
    USERNAME :  <input type="text" name="username" />
    <br><br>PASSWORD :  <input type="password" name="password" />
    <br><br>EMAIL : <input type="text" name="email" />
    <br><br>Organization :
    <select name="org_id">
        <?php
        require 'config.php';
        echo '<option value="0">none</option>';
        $result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
         while($row = mysqli_fetch_array($result))
        {
            echo '<option value="'.$row['id'].'">'.$row['org_name'].'</option>';
         }
        ?>
    </select>
    <br><br><input type="submit" value="REGISTER" />
</form> 
</body>
</html>