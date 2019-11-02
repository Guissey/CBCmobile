<?php
	session_start();
	include 'functions.php';
	$connection = connectDB();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

	<meta charset="utf-8">
	<meta name="viewport"
	content="width=device-width, initial-scale=1.0">

	<title> CBC69 mobile</title>

	<link href="bootstrap3.3.6/css/bootstrap.css" rel="stylesheet">
	<link href="index.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="bootstrap3.3.6/js/bootstrap.min.js"></script>
	<script src="script.js"></script>

</head>	


<body>
	

<div class="headband">
<?php
	headband($connection, 2);
?>
</div>

<div class="content">

<?php
	if (isset($_GET["id"])){
		tournamentSummary($connection, $_GET["id"]);
	}

	else{
		echo'
	<div id="inf-scroll-container" class="subcontent">
		';
		previewPage($connection, 0);
		echo'
	<div id="loader"><p>Chargement</p></div>
	</div>
	<a href="managetournament.php" class="add-tournament">
		<span class="glyphicon glyphicon-plus" aria-label="Nouveau tournoi" style="color: white"></span>
	</a>
		';
	}
	disconnectDB($connection);
?>

	
</div>


</body>

</html>
