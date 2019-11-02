<?php
	session_start();
	include 'functions.php';
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$connection = connectDB();
		echo $_POST["id"] . '<br \>';
		deleteTournament($connection, $_POST["id"]);
		disconnectDB($connection);
		header("Location: tournois.php");
	}
	else{
		header("Location: tournois.php");
	}
?>
