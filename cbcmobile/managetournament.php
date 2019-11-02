<?php
	session_start();
	include 'functions.php';
	$connection = connectDB();

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$esc_name = mysqli_real_escape_string($connection, $_POST["name"]);
		$esc_date = mysqli_real_escape_string($connection, $_POST["date"]);
		$esc_summary = mysqli_real_escape_string($connection, $_POST["summary"]);
		if (!isset($_GET["id"])){
			$tournament_id = createTournament($connection, $esc_name, $esc_date, $esc_summary, $_POST["type"]);
			header("Location: tournois.php?id=" . $tournament_id);
		}
		else{
			if (updateTournament($connection, $_GET["id"],$esc_name, $esc_date, $esc_summary, $_POST["type"])){;
				header("Location: tournois.php?id=" . $_GET["id"]);
			}
			else{
				echo 'No success';
			}
		}
	}

	$name = "";
	$summary = "";
	$date = "";
	$interclub = "";
	if (isset($_GET["id"])){
		$result = mysqli_query($connection, 'SELECT * FROM tournament WHERE tournament_id =' . $_GET["id"]);
		$tournament = mysqli_fetch_assoc($result);
		$name = $tournament[name];
		$summary = $tournament[summary];
		$date = $tournament[date];
		$interclub = $tournament[interclub];
	}
	//echo $name . " " . $summary . " " . $date . " " . $interclub;
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
	headband($connection,2);
?>
</div>

<div class="content">

<form action="" method="post" class="tournament-form" autocomplete="off">
	<div class="form-input">
		<label for="type">Type de tournoi: </label>
		<select name="type">
    			<option value=NULL>Individuel</option>
<?php
			selectInterclubTeam($connection, $interclub);
?>
		</select>
	</div>
	<div class="form-input">
    		<label for="name">Nom du tournoi: </label>
    		<input type="text" name="name" id="name" value ="<?php echo $name ?>" required>
  	</div>
	<div class="form-input">
    		<label for="date">Date: </label>
    		<input type="text" name="date" id="date" value ="<?php echo $date ?>"required>
  	</div>
	<div class="form-input">
    		<label for="name">Résumé: </label>
    		<textarea name="summary" id="summary" style="width:100%; height: 50vh;" required><?php echo $summary ?></textarea>
  	</div>
	<div class="form-input">
    		<input type="submit" value="Valider">
  	</div>
	
</form>

<?php
	disconnectDB($connection);
?>

</div>

</body>

</html>
