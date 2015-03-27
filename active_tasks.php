
<?php

require 'config.php';
if (isset($_GET['wid']) && isset($_GET['nodeid'])) {
    echo "<h2>Submit Data </h1>";
    echo '<form action="submit_data.php" method="post">';
    echo '<input type="hidden" name="wid" value="' . $_GET['wid'] . '"> ';
    echo '<input type="hidden" name="nodeid" value="' . $_GET['nodeid'] . '"';
    extractform($_GET['wid'], $_GET['nodeid']);
    $result = selectfromdb("transition", array("functions"), "workflow_id='" . $_GET['wid'] . "' and id='" . $_GET['nodeid'] . "'");
    $list = unserialize(mysqli_fetch_array($result)['functions']);
    foreach ($list as $pair) {//pair of parameter and functionlist
        $parameters = array();
        $data = $pair["data"];
        $functionlist = $pair["functions"];
        foreach ($data as $param) {
            //echo $param[0] . " -------------------<br>";
            if ($param[0] === "data")
                array_push($parameters, $param[1]);
            else if ($param[0] === "node") {
                //print_r($param);echo "<br>";
                $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $param[2] . "' and transition_id='" . $param[1] . "' and workflow_id='" . $_GET['wid'] . "'");
                $row2 = mysqli_fetch_array($result2);
                //echo ":".$row2['chosen_val'].":<br>";
                array_push($parameters, $row2['chosen_val']);
            }
        }
        foreach ($functionlist as $function) {
            $prameters = callfunc($function, $parameters);
        }
    }
    echo ' <br/><input type="submit" value="submit" /></form>';
} else {
    echo "<h2>Active Tasks </h1>";
    $wid = getmaxidfromdb("workflow");
    $res = selectfromdb("workflow", array("id"));
    while ($row1 = mysqli_fetch_array($res)) {
        $wid=$row1['id'];
        $result = selectfromdb("transition", array("id", "type"), "workflow_id='" . $wid . "' and activated='1'");
        while ($row = mysqli_fetch_array($result)) {
            echo '<a href="?wid=' . $wid . '&nodeid=' . $row['id'] . '"> workflowid=' . $wid . " nodeid=" . $row['id'] . " node description=" . $row['type'] . '</a><br>';
        }
    }
}
?>
