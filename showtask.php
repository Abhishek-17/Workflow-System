
<?php
require_once 'config.php';
if(isset($_GET['wid'])){
    $wid=$_GET['wid'];
    $result=  selectfromdb("transition", array('id','type'), "workflow_id=".$_GET['wid']);
    echo '<form action="adddoer.php" method="post"><input type="hidden" name="wid" value='.$wid.'>';
    $nodelist='';
    $idlist='';
    while($node=  mysqli_fetch_array($result)){
      if($node['type']!="end")
     {
        
        extractform($_GET['wid'],$node['id']);
        //generate list of users/doers
        echo "<br>Add the doer:";
        echo '<select name="n'.$node['id'].'"><optgroup label="Default"><option value="0">none</option</optgroup><br><br>';
        $idlist.="n".$node['id'].";";
        $nodelist.=$node['id'].";";
        $result1=selectfromdb("organization",array("id",'org_name'),"org_name!='root'");
         while($row1 = mysqli_fetch_array($result1))
        {
             echo '<optgroup label="'.$row1['org_name'].'">';
             $result2=selectfromdb("login",array("id","username","email"),"org_id=".$row1['id']);
             while($row2 = mysqli_fetch_array($result2)){
                 echo '<option value="'.$row2['id'].'">'.$row2['username'].' : '.$row2['email'].'</option>';
             }
             echo '</optgroup>';
        }
       echo '
        </select></br>';
     }
    }
    echo '<input type="hidden" name="idlist" value="'.$idlist.'">';
    echo '<input type="hidden" name="nodelist" value="'.$nodelist.'">';
    echo '
        
     <input type="submit" value="submit" />
    </form>';

}
?>
