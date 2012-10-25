<?php
/*
 * Date: 2012/10/25
 * Time: 2:22 PM
 */
function test_array($array) {
	header("Content-Type: application/json");
	echo json_encode($array);
	exit();
}

?>
