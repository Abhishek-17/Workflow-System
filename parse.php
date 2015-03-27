<?php

require 'config.php';
// The file raw.xml contains an XML document with a root element
// and at least an element /[root]/title.




if (file_exists($file)) {
    $xml = simplexml_load_file($file);
    if ($xml === false)
        exit(0);
    //print_r($xml);
    // echo "<br/><br/>";
}
else {
    exit('Failed to open ' . $file);
}
$workflow = array((string) $xml['name'], "ist work flow", date("Y/m/d"), date("Y/m/d"), (string) $xml['user']);
//echo  (string)$xml['name'].'<br>'; exit(0);
//echo  (string)$xml->node[0]['id'];

insertintodb("workflow", $workflow, 0);

//exit(0);
$wid = getmaxidfromdb("workflow");

foreach ($xml->node as $node) {
    $out = array();
    $branch = array();
    foreach ($node->outnode as $outnode) {
        array_push($out, (string) $outnode['id']);
    }
    // if(strlen($out))$out=substr($out,0,strlen($out)-1);//removing last comma

    $activated = 0; //status: inactive
    if ($node['type'] == "start")
        $activated = 1;
    $innodelist = explode(";", (string) ($node->innodes[0]['id']));
    //echo (string)($node->innodes[0]);
    $innodes = array();
    foreach ($innodelist as $inlist) {//array of arrays:: ((5,6),(8),(9,10))
        $l = array();
        $in = explode(",", $inlist);
        foreach ($in as $i) {
            array_push($l, (string) $i);
        }
        array_push($innodes, $l);
    }
    $done_innodes = array();

    //storing inputs and their validation info
    foreach ($node->input as $input) {
        $record = array($wid, $node['id'], $input['type'], $input['name']);
        $vars = array();
        $type = $input['type'];
        foreach ($input->value as $value) {
            $val = array();
            //echo ": ".$value['val']." :<br/>";
            if ((string) $value->get[0] === "") {
                array_push($val, 0); //value is data
                array_push($val, (string) $value);
            } else {
                array_push($val, 1); //value should be received from function call
                array_push($val, (string) $value->get[0]);
            }
            if (isset($value["val"])) {
                array_push($vars, array(0, (string) $value['val'], $val));
            } else if (isset($value["fval"])) {
                array_push($vars, array(1, (string) $value['fval'], $val));
            }
        }
        // print_r ($vars);
        //exit(0);
        array_push($record, serialize($vars));
        array_push($record, ""); //chosen value: initially blank
        $validation = array();
        $validation["property"] = array();


        foreach ($input->condition as $condition) {
            if ($condition['type'] == "validation") {
                $property = $validation['property'];
                foreach ($condition->check as $check) {
                    $property[(string) $check['property']] = array((string) $check['val'], (string) $check['operator']);
                    // echo "validate:: ".$check['property'].'<br>';
                }
                $validation["property"] = $property;
                //unset($property);
            }
        }
        array_push($record, serialize($validation));
        //  array_push($record,serialize($branch));
        insertintodb("inputs", $record, 0);
    }
    //exit(0);
    //storing function calls of the node
    //array(  [data=>array(),functions=>array()], [] .... )
    $functions = array();
    foreach ($node->function as $function) {
        $list = array("data" => array(), "functions" => array());
        foreach ($function->data as $data) {
       //   echo ":".$data['type'].":<br>";
            if ((string)$data['type'] ==="node") {
         //        echo "here";
                $nodeid = explode(",", (string) $data)[0];
                $inputname = explode(",", (string) $data)[1];
                array_push($list["data"], array("node", $nodeid, $inputname));
            } else {
                array_push($list["data"], array("data", (string) $data));
            }
        }

        foreach ($function->functionname as $func) {
            array_push($list["functions"], (string) $func);
        }
        array_push($functions, $list);
    }

    //branch=array( [ validation=>([inputname,([property,val,operator])], [],[]..),outnodes=>(1,2,3,9) ] , [  ].... )
    foreach ($node->condition as $condition) {
        if ($condition['type'] == "branch") {
            $property = array();
            foreach ($condition->check as $check) {
                array_push($property, array((string) $check['inputname'], array((string) $check['property'],(string) $check['val'], (string) $check['operator'])));

                //echo "branch:: ".$check['property'].'<br>';
            }
            $outnodes = array();
            foreach ($condition->outnode as $outnode) {
                array_push($outnodes, (string) $outnode['id']);
            }
            $pair = array();
            $pair["validation"] = $property;
            $pair["outnodes"] = $outnodes;
            array_push($branch, $pair);
        }
    }
    $record = array($wid, $node['id'], $node['type'], serialize($innodes), serialize($done_innodes), serialize($out), serialize($functions), serialize($branch), $activated, "100", 0);
    insertintodb("transition", $record, 1);
   
}
 //exit(0);
?>