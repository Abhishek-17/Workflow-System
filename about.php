<?php
header('Content-disposition: attachment; filename=1.pdf');
header('Content-type: application/pdf');
readfile('1.pdf');
?> 