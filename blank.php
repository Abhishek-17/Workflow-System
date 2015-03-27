<?php //echo $_SESSION['email'];
require 'config.php'; 
?>
<html>
<body>

<form action="upload_file.php" method="post"
enctype="multipart/form-data">
<label for="file">Upload XML File:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>
    <h3>Currently available Workflows</h3>
 <?php 
    $result=selectfromdb("workflow",array('id','name'));
    echo '<table border="1"><tr><td>id</td><td>workflow name</td></tr>';
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$row['id'].'</td>';
        
        echo '<td><a href="showtask.php?wid='.$row['id'].'">'.$row['name'].'</a><br/></td>';
        echo '</tr>';
    }
     echo '</table>';
    

 ?>
</body>
</html>