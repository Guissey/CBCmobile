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
	headband($connection, 0);
?>
</div>

<div class="content">
	
<div class="subcontent">

	<div class="article " style="cursor: pointer;" onclick="window.location='index.php?id=1';">
		<span class="category"></a>Stage</span><br />
		<span class="title">Stage jeune 26&27 août</span><br />
		<span class="preview">Ces 26 et 27 août se déroulera ...</span>
	</div>

	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>

	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>

	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>

	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>

	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>
	
	<div class="article">
		<span class="category"></a>Rubrique</span><br />
		<span class="title">Titre</span><br />
		<span class="preview">Début du contenu...</span>
	</div>

</div>

</div>

<?php
	disconnectDB($connection);
?>

</body>

</html>
