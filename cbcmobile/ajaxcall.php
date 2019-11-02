<?php

include "functions.php";

$ret = array();
if (isset($_POST["imageData"])){
	$ret["success"] = true;
	$dataUrl = $_POST["imageData"];
	$id = $_POST["id"];
	$ret["image"] = $dataUrl;
	$ret["path"] = base64ToImage($dataUrl, "pictures/tournament" . $id . "/" . date('Y-m-d-H-i-s') . '_' . uniqid());
}
else{
	$ret["success"] = false;
}
	echo json_encode($ret); 

?>
