<?php
	session_start();
	include 'functions.php';
	$connection = connectDB();
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$esc_name = mysqli_real_escape_string($connection, $_POST["team_name"]);
		$query = 'INSERT INTO `interclub`(`team_name`) VALUES ("' . $esc_name . '")';
		if (!($result = mysqli_query($connection, $query))){
			die("Impossible d'effectuer la requête: " . $query);
		}
		else{
			header("Location: ".$_SERVER['REQUEST_URI']);
		}
	}
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
	<div class="subcontent">
		<h3>Liste des équipes d'interclubs:</h3>
			<ul>
<?php
$result = mysqli_query($connection, "SELECT * FROM interclub");
while ($team = mysqli_fetch_assoc($result)){
	echo '<li>' . $team[team_name] . '</li>';
}
?>
			</ul>
		<form method="post" autocomplete="off">
			<input type="text" name="team_name" class="form-control mb-4" placeholder="Nouvelle équipe">
			<input type="submit" value="Ajouter">
		</form>
	</div>
</div>

<?php
	disconnectDB($connection);
?>

</body>

</html>
