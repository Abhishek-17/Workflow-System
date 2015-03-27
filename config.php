<script>
    function toggleshow(id) {
        if (document.getElementById(id).style.display == "none")
            document.getElementById(id).style.display = '';
        else
            document.getElementById(id).style.display = "none";

    }
</script>

<?php
//echo $_SESSION['email'];

session_start();
if (isset($_SESSION['email'])) {
    echo '
        <div style="text-align:right">' . $_SESSION['email'] . '&nbsp; <a href="logout.php">Logout</a> </div>';
}
$con = mysqli_connect("localhost", "root", "", "workflows");

if (mysqli_connect_errno($con)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
/* $result = mysqli_query($con,"show tables");

  while($row = mysqli_fetch_array($result))
  {
  echo $row[0];
  echo "<br>";
  } */

function callfunc($funcname, $parameters) {//array of parameters
    if ($funcname == "reverse") {//reverse func
        return "This is the otput of reverse function :)";
    }
    else if ($funcname == "display") {//reverse func
         $s="<br>in function ".$funcname."  ist parameter:".$parameters[0]."<br>";
        echo $s;
       // print_r($parameters);echo "-----<br>";
        
    }
    else if ($funcname == "display2") {//reverse func
         $s="<br>in function ".$funcname."  ist parameter:".$parameters[0]."<br>";
        echo $s;
      //  print_r($parameters);echo "-----<br>";
        
    }
    else 
    {
       if(gettype($parameters)==="array")return $parameters[0]." In function ".$funcname." ";
       else return $parameters." In function ".$funcname." ";
        
    }
    return "in function ".$funcname." ";
}
function isvalid($originalval,$property,$val,$operator){
    print_r($originalval);echo "<br>";
    print_r($property);echo "<br>";
    print_r($val);echo "<br>";
    print_r($operator);echo "<br>--";
    if($property==="value")$val=$originalval;
    return true;
}

function insertintodb($table, $valuearray, $fl, $columns = array()) {//receives array of values
    global $con;
    $schema = "(";
    $i = 0;
    if (!count($columns)) {
        if ($result = mysqli_query($con, "describe $table")) {
            while ($row = mysqli_fetch_array($result)) {

                if ($fl && !$i)
                    $schema.=$row[0] . ",";
                else if ($i)
                    $schema.=$row[0] . ",";
                $i+=1;
                //echo "<br>";
            }
            $schema[strlen($schema) - 1] = ')';
        }
    }
    else {
        foreach ($columns as $column) {
            $schema.=$column . ",";
        }
        $schema[strlen($schema) - 1] = ')';
    }
    if ($i) {
        $arrlength = count($valuearray);
        $values = "(";
        for ($x = 0; $x < $arrlength; $x++) {
            $values.="'" . $valuearray[$x] . "',";
        }
        $values[strlen($values) - 1] = ')';
        // echo 'table='.$table."<br/>";
        // echo $values." : <br/>";
        //return;
        $query = "insert into $table " . $schema . " values" . $values;
        // echo $query."<br/>";
        //return;
        if (!mysqli_query($con, $query)) {
            die('Error while inserting in ' . $table . ': ' . mysqli_error($con));
        }
    }
}

function selectfromdb($table, $columns = array('id'), $where = "id>0") {
    global $con;
    $select = "select ";
    foreach ($columns as $col) {
        $select.=$col . ",";
    }
    $select[strlen($select) - 1] = ' ';
    $select.="from $table where $where";
    // echo $select.'<br>';
    $result = mysqli_query($con, $select);
    return $result;
}

function getmaxidfromdb($table, $columns = " id ", $where = "id>0") {
    global $con;
    if ($result = mysqli_query($con, "select MAX(id) from $table")) {
        $row = mysqli_fetch_array($result);
        return (string) $row[0];
    }
}

function deletefromdb($table, $where) {
    global $con;
}

function myprint($result) {
    while ($row = mysqli_fetch_array($result)) {
        foreach ($row as $col) {
            echo $col . " ";
        }
        echo "<br/>";
    }
}

function extractform($wfid, $taskid) {
    global $con;
    
    echo "<h3>Workflow number: $wfid and Task number: $taskid</h3><br/>";
    $query = "select type,name,val from inputs where workflow_id=$wfid and transition_id=$taskid";
    $result = mysqli_query($con, $query);
    $divid = 'w' . $wfid . 't' . $taskid . 'div';
    echo "<div id='$divid' style='display:none'>";
    while ($row = mysqli_fetch_array($result)) {
        $res = "";
        $arr = unserialize($row['val']);
        // print_r($arr); echo"here";exit(0);
        foreach ($arr as $vals) {
            //print_r($vals); echo '<br>';
            //array( [1,val,array(1,value)], [] , [] ... )
            $key = $vals[1];
            $value = $vals[2][1];
                //print_r($value); exit(0);
            // print_r($v);
            if ($vals[0]) {//key should come from prevoius node;
                //echo $key."<br>";exit(0);
                $v = explode(";", $key); //parameters + functionlist
                $functionlist = explode(":", $v[1]); 
                $paramlist = explode(":", $v[0]);
                $parameters = array();
                foreach ($paramlist as $param) {
                    $l = explode(",", $param);
                    if (count($l) == 1) {
                        array_push($parameters, $param);
                    } else if (count($l) == 2) {//fetch data from previous node
                        $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $l[1] . "' and transition_id='" . $l[0] . "' and workflow_id='" . $wfid . "'");
                        $row2 = mysqli_fetch_array($result2);
                        array_push($parameters, $row2['chosen_val']);
                    }
                }
               
                //  print_r($v); echo '<br>';
                foreach ($functionlist as $function) {
                    $parameters = callfunc($function, $parameters);
                   // print_r($parameters); echo "<br>";
                }
                $key = $parameters;
              //  echo $key; exit(0);
            }
            if ($vals[2][0]) {//value should come from previous nodes
                $v = explode(";", $value); //parameters + functionlist
                $functionlist = explode(":", $v[1]);
                $paramlist = explode(":", $v[0]);
                $parameters = array();
                foreach ($paramlist as $param) {
                    $l = explode(",", $param);
                    if (count($l) == 1) {
                        array_push($parameters, $param);
                    } else if (count($l) == 2) {//fetch data from previous node
                        $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $l[1] . "' and transition_id='" . $l[0] . "' and workflow_id='" . $wfid . "'");
                        $row2 = mysqli_fetch_array($result2);
                        array_push($parameters, $row2['chosen_val']);
                    }
                }
                //  print_r($v); echo '<br>';
                foreach ($functionlist as $function) {
                    $parameters = callfunc($function, $parameters);
                }
                $value = $parameters;
               // print_r($value); exit(0);
            }
            $res.="<input type='" . $row['type'] . "' name='" . $row['name'] . "' value='" . $key . "'>" . $value . "<br>";
        }
        $res.="";
        echo "<br/>";
       // echo "HTML code is <br/>";
       // echo '<pre>' . htmlspecialchars($res) . '</pre>';
        echo $row['name'] . " : " . $res;
    }

    echo "</div>";
    $p = "'";
    $p.=$divid . "'";
    echo '<button onclick="toggleshow(' . $p . ');return false;">Details.. </button>';
}

//$result=selectfromdb("organization",array("id",'org_name'),"org_name!='root'"); 
//myprint($result);
?>