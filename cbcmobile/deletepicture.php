<?php

$ret = array();

if (isset($_POST["path"])){
	if (file_exists($_POST["path"])){
		unlink($_POST["path"]);
		$ret["success"] = true;
	}
	else{
		$ret["success"] = false;
	}
}

else{
	$ret["success"] = false;
}

echo json_encode($ret); 

?>
