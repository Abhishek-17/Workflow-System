<?php
require 'config.php';
if(isset($_POST['idlist'])){
    $ids=explode(";",$_POST['idlist']);
    $nodes=explode(";",$_POST['nodelist']);
    $l=count($ids);
    $i=0;
    while($i<$l){
      //  echo $ids[$i]." ".$nodes[$i].'<br>';
        
        if(isset($_POST[$ids[$i]])){
            
            $update="UPDATE transition SET doer=".$_POST[$ids[$i]]."
                WHERE id=$nodes[$i] AND workflow_id=".$_POST['wid'];
            mysqli_query($con,$update);
            echo $update.'<br/>';
        }
        $i+=1;
    }
    header( 'Location: blank.php' ) ;
}
else echo "error: idlist is empty in adddoer.php<br/>";
?>
