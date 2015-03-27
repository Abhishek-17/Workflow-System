<?php
require 'config.php';

if(isset($_POST['con_name']) && isset($_POST['con_manager']) && isset($_POST['con_deadline']) && isset($_POST['org1_id']) && isset($_POST['org2_id']))
{
    if($_POST['con_name']!="" && $_POST['con_manager']!="" && $_POST['con_deadline']!="" && $_POST['org1_id']!="" && $_POST['org2_id']!="")
    {
        $sql="insert into contract(con_name,con_manager,con_deadline,org1_id,org2_id) values('$_POST[con_name]' ,'$_POST[con_manager]' ,$_POST[con_deadline] ,$_POST[org1_id] ,$_POST[org2_id] )";
        //echo $sql;
        $result=mysqli_query($con,$sql);
        header('Location: initialize.php');
    }
    else
    {
        echo 'Some field was not filled <br>
        <html>
        <body>
        <h1><font face="Arial" size="+1" color="#FF2222">ADD CONTRACT</font></h1>
        <form action="initialize_contract.php" method="post">
            CONTRACT NAME : <input type="text" name="con_name" /><br><br>
            CONTACT MANAGER : <input type="text" name="con_manager" /><br><br>
            CONTRACT DEADLINE: <input type="text" name="con_deadline" /><br><br>
            ORGANIZATION 1 ID:<input type="text" name="org1_id" /><br><br>
            ORGANIZATION 2 ID:<input type="text" name="org2_id" /><br><br>
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
    <h1><font face="Arial" size="+1" color="#FF2222">ADD CONTRACT</font></h1>
    <form action="#" method="post">
        <br><br>CONTRACT NAME : <input type="text" name="con_name" />
        <br><br>CONTRACT MANAGER : 
        <select name="con_manager"><br><br>';
        $result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
         while($row = mysqli_fetch_array($result))
        {
             echo '<optgroup label="'.$row['org_name'].'">';
             $result2=selectfromdb("login",array("id","username","email"),"org_id=".$row['id']);
             while($row2 = mysqli_fetch_array($result2)){
                 echo '<option value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
             }
             echo '</optgroup>';
        }
       echo '
        </select>
        <br><br>CONTRACT DEADLINE: <input type="text" name="con_deadline" />
        <br><br>ORGANIZATION 1 ID:
        <select name="org1_id">';
        $result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
         while($row = mysqli_fetch_array($result))
        {
           echo '<option value="'.$row['id'].'">'.$row['org_name'].'</option>';
        }
       echo '
         </select>
        <br><br>ORGANIZATION 2 ID:
        <select name="org2_id">';
       $result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
         while($row = mysqli_fetch_array($result))
        {
           echo '<option value="'.$row['id'].'">'.$row['org_name'].'</option>';
        }
        echo '
          </select>
        <br><br><input type="submit" value="CREATE" />
    </form> 
    </body>
    </html>';
}
?>