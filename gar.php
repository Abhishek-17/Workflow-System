<?php


$workflow=array((string)$xml['name'],"ist work flow","hi!",date("Y/m/d"),date("Y/m/d"),"abhi");
//echo "$workflow<br/>";    
//echo  (string)$xml->node[0]['id'];

insertintodb("workflow",$workflow,0);
//exit(0);
$wid=getmaxidfromdb("workflow");
////echo '<form action="adddoer.php" method="post"><input type="hidden" name="wid" value='.$wid.'>';
//$nodelist='';
//$idlist='';
foreach ($xml->node as $node) {
    $record=array();
     $out=(string)$node->outnode[0]['id'];
     if(strlen($out)==0)$out="";
    $record=array($wid,$node['id'],$node['type'],$out,"","100",0);
    insertintodb("transition",$record,1);
    if($node['type']=="end")
    { 
        $record=array($wid,$node['id'],"none:endnode","none:endnode","none:endnode");
        insertintodb("inputs",$record,0);
    }
    else
    foreach ($node->input as $input)
    {
        
        $record=array($wid,$node['id'],$input['type'],$input['name']);
        $vars=array();
        $type=$input['type'];
        //if($type=='range')
       // {
        //    array_push($record,$input['value']);
        //}
        //else
        //{
            foreach ($input->value as $value)
            {
                //echo ": ".$value['val']." :<br/>";
                $vars[(string)$value['val']]=(string)$value;
            }
            // print_r ($vars);
            if(count($vars)==0)$vars="";
            array_push($record,serialize($vars));
        //}
            insertintodb("inputs",$record,0);
            
    }
        
        //print_r($record);
       // echo "<br/><br/><br/>::<br/>";
  
    }
    header('Location: blank.php');
   /* echo '<input type="hidden" name="idlist" value="'.$idlist.'">';
    echo '<input type="hidden" name="nodelist" value="'.$nodelist.'">';
    echo '
        
     <input type="submit" value="submit" />
    </form>';*/
    
/*
    extractform(1,1);
    extractform(1,3);
    extractform(1,4);*/
?>
