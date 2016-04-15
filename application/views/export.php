<?php
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename='. $_POST['filename'] . '"');
echo $_POST['export'];
?>
