<?php

    require 'config.php';
if(isset($_POST['wid'])&&isset($_POST['nodeid'])){
    $result=  selectfromdb("inputs",array("name","type"),"workflow_id='".$_POST['wid']."' and transition_id='".$_POST['nodeid']."'");
    while($row=  mysqli_fetch_array($result)){
        //do validation checks ist..
      //  echo "here: ".$_POST[$row['name']]." : here<br>";    
         $update="UPDATE inputs SET chosen_val='".$_POST[$row['name']]."' WHERE transition_id='".$_POST['nodeid']."' AND workflow_id=". $_POST['wid']." AND name='".$row['name']."'";
       // echo $update.'<br>';
         mysqli_query($con,$update);
        
    }
   // exit(0);
    //updating active states of nodes
    $result=  selectfromdb("transition",array("outnodes","branch"),"workflow_id='".$_POST['wid']."' and id='".$_POST['nodeid']."'");
    while ($row = mysqli_fetch_array($result)) {
        //outnode: array(nodeid1,nodeid2,..)
        $out=  unserialize($row['outnodes']);
        $branch=unserialize($row['branch']);
    
        foreach($branch as $br){
            $validation=$br['validation'];
            $outnodes=$br['outnodes'];
            $fl=1;
            foreach($validation as $condition){
                $name=$condition[0];//input name
                $property=$condition[1][0];
                $val=$condition[1][1];
                $operator=$condition[1][2];
                $fl=isvalid($_POST[$name],$property,$val,$operator);
                           //originalvalue  , property,value tobe compared with, operator
                if(!$fl)break;//conditions dont hold
                
            }
            if($fl){
               foreach($outnodes as $i){
                   if(!in_array($i,$out))array_push($out,$i);
               }
            }
        }
            foreach($out as $node){
            $result1=  selectfromdb("transition",array("innodes","done_innodes"),"workflow_id='".$_POST['wid']."' and id='".$node."'");
            $row1=mysqli_fetch_array($result1);
            $doneinnodes=  unserialize($row1['done_innodes']);
            $innodes=unserialize($row1['innodes']);
           // print_r($doneinnodes);echo " :doneinnodes<br>";
            //print_r($innodes);
            if(!in_array($_POST['nodeid'],$doneinnodes)){
                array_push($doneinnodes,$_POST['nodeid']);
                $update="UPDATE transition SET done_innodes='".serialize($doneinnodes)."' WHERE id='".$node." AND workflow_id=". $_POST['wid'];
                foreach($innodes as $list){
                    $fl=1;
                    foreach($list as $outnode){
                            if(!in_array($outnode,$doneinnodes)){
                                $fl=0; break;
                            }
                    }
                    if($fl){
                       // echo "asdadadadadaad :::<br>";
                          
                             $activate="UPDATE transition SET activated='1' WHERE id='".$node."' AND workflow_id=". $_POST['wid'];
                           //  echo $activate."<br>";
                             mysqli_query($con,$activate);
                        
                    }
                }
                 mysqli_query($con,$update);
            }
            
        }
    }
   $deactivate="UPDATE transition SET activated='2' WHERE id='".$_POST['nodeid']."' AND workflow_id=". $_POST['wid'];//setting done
                            
  mysqli_query($con,$deactivate);
    header('Location: active_tasks.php');
}

?>
