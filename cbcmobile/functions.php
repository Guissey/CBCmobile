<?php

/*
Converts a dataUrl image $base64_string (which is coded in base 64 with a header) into a binary file and save it as $outputfile on the web server.
*/

function base64ToImage($base64_string, $output_file) {
	$img = explode( ',', $base64_string);
	$img = base64_decode($img[1]);
	$success = file_put_contents($output_file, $img);
    	return $output_file;
}


/* !! Maybe create another DB user with less privileges for security purposes !!
Tries to connect to the mysql database called cbcDB.
Returns the object of connection if successful or false if unsuccessful.
*/

function connectDB(){
	$host = "127.0.0.1";
	$user = "admin";
	$pwd = "admin";
	$db = "cbcDB";
	$connection = mysqli_connect($host, $user, $pwd);
	if (!$connection) {
    		die('Could not connect to DB: ' . mysql_error() );
	}
	$select = mysqli_select_db($connection, $db);
	if (!$select) {
// If we couldn't, then it either doesn't exist, or we can't see it.
  		$query = 'CREATE DATABASE ' . $db . ' CHARACTER SET utf8 COLLATE utf8_general_ci;';
		if (!mysqli_query($connection, $query)) {
			die('Could not create DB');
		}
		else{
			$select = mysqli_select_db($connection, $db);
			if (!$select){
				die('Could not connect to DB');
			}
			$query =  '
CREATE TABLE `interclub` (
 `team_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
 `team_name` varchar(20) NOT NULL,
 UNIQUE KEY `team_id` (`team_id`)
) DEFAULT CHARSET=utf8;
CREATE TABLE `tournament` (
 `tournament_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `date` varchar(40) NOT NULL,
 `name` varchar(100) NOT NULL,
 `summary` text NOT NULL,
 `interclub` tinyint(4) unsigned DEFAULT NULL,
 `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`tournament_id`),
 UNIQUE KEY `tournament_id` (`tournament_id`),
 KEY `interclub` (`interclub`),
 CONSTRAINT `tournament_ibfk_1` FOREIGN KEY (`interclub`) REFERENCES `interclub` (`team_id`)
) DEFAULT CHARSET=utf8;
			';
			if (!mysqli_multi_query($connection, $query)){
				die('Could not create tables');
			}
		}
	}
	mysqli_set_charset($connection, "utf8");
	return $connection;
}


/*
Closes the connexion defined by the variable $connexion 
*/

function disconnectDB($connection){
	return mysqli_close($connection);
}


/*
Adds the interclub teams <option> inside a <select> field in a form

$connexion is the object of connexion previously returned by the connectDB function
*/

function selectInterclubTeam($connection, $interclub){
	$id = false;
	$result = mysqli_query($connection, "SELECT * FROM interclub");
	while ($team = mysqli_fetch_assoc($result)){
		$select = '';
		if ($team[team_id] == $interclub){$select = " selected";}
		echo '<option value="' . $team[team_id] . '"' . $select . '>' . $team[team_name] . '</option>';
	}
}


/* !!!!Conversion des sauts de lignes entre html et DB!!!!
Shows the informations about the tourney.
*/
function tournamentInfo($connection, $tournamentId){
	$result = mysqli_query($connection, 'SELECT * FROM tournament WHERE tournament_id =' . $tournamentId);
	$tournament = mysqli_fetch_assoc($result);
	echo'
<div class="article-title">
	<span class="main-title"><strong>' . htmlspecialchars($tournament[name]) . '</strong></span><br \>
	<span class="sec-title">' . htmlspecialchars($tournament[date]) . '</span>
</div>

<div class="article-description">
	<p class="description">' . htmlspecialchars($tournament[summary]) . '</p>
</div>
	';
}


/* !!!!Conversion des sauts de lignes entre html et DB!!!!
Displays the complete summary of the tournament

$connection: the object of connection returned by connectDB()
$tournamentID: refers to the attribute tournament[tournament_id] in the DB
*/

function tournamentSummary($connection, $tournamentID){

	$dir = "pictures/tournament" . $tournamentID;
	if (!is_dir($dir)) {
    			mkdir($dir, 0775, true);
	}
	//Gets the list of paths of the pictures in $dir using the 		glob function.
	
	$str = $dir . '/*';
	$fileList = glob($str);
	
	//Uses these paths to create the html codes for the pictures 		preview and the carousel
	$preview = '<div>';
	$carousel = '
<!-- Carousel -->

<div id="carousel" class="carousel slide" data-ride="carousel" data-interval="false" style="display: none;">

<!-- Carousel slides -->

<div class="carousel-inner" role="listbox">';
	$i = 0;
	foreach($fileList as $filename){
		
		$preview .= '<img id="preview' . $i . '" class="preview-picture" src="' . $filename . '" onclick="showCarousel(this)">';

		$fix = "";
		if ($i == 0){ $fix = " active"; };
	
		$carousel .= '
<div id="item' . $i . '" class="item' . $fix . '">
	<img src="' . $filename .'">
	<div class="item-straight-parent">
		<div id="slide' . $i . '" class="item-straight" style="background-image: url(' . "'" . $filename . "'" . ');">
		</div>
	</div>
</div>';
		$i++;
	}
	
	$preview .='
</div>
<div>
	<input id="file" type="file" accept="image/*" class = "upload-pictures" multiple>
	<label for="file">Partagez vos photos</label>	
	<a href="managetournament.php?id=' . $_GET[id] . '"><label class="update-infos">Modifier infos</label></a>
	<div>
	<form action="deletetournament.php" method="post">
		<input type="hidden" name="id" value="' . $_GET["id"] . '">
		<button type="submit" class="delete-tournament">Supprimer tournoi</button>
	</form>
	</div>
</div>';

	$carousel .= '
</div>

<!-- Carousel controls -->

<a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
    	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    	<span class="sr-only">Previous</span>
</a>
<a class="right carousel-control" href="#carousel" role="button" data-slide="next">
    	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    	<span class="sr-only">Next</span>
</a>

<button class="btn btn-default rotate-slide" type="button" onclick="rotateSlide()">
	<span class="glyphicon glyphicon-refresh" aria-label="Fermer"></span>
</button>

<button class="btn btn-default delete-image" type="button" onclick="deleteImage()">
	<span class="glyphicon glyphicon-trash" aria-label="Supprimer"></span>
</button>

<button class="btn btn-default close-carousel" type="button" onclick="closeCarousel()">
	<span class="glyphicon glyphicon-remove" aria-label="Fermer"></span>
</button>

</div>';
	
	//Complete echo of the summary
	echo '<div class="subcontent">';
	tournamentInfo($connection, $tournamentID);
	echo $preview . '</div> ' . $carousel;
}


/*
Echoes the preview of the tournament.

$connexion is the object of connexion previously returned by the connectDB function
$tournamentId is the ID of the tournament in the DB
*/
function tournamentPreview($connection, $tournamentId){
	$result = mysqli_query($connection, 'SELECT * FROM tournament WHERE tournament_id =' . $tournamentId);
	$tournament = mysqli_fetch_assoc($result);
	echo'
<div class="article" onclick="window.location=\'tournois.php?id=' . $tournament[tournament_id] . '\';">
	<span class="date">' . $tournament[date] . '</span><br />
	<span class="title">' . $tournament[name] . '</span><br />
	<span class="preview">' . substr($tournament[summary], 0, 50) . ' ...</span>
	</div>';
}


/*
Creates the tournament in the DB with the datas given as parameters.
Returns the tournament_id generated.
*/

function createTournament($connection, $name, $date, $summary , $type){
	$query = 
'INSERT INTO `tournament` (`name`, `date`, `summary`, `interclub`) VALUES ("' . $name . '", "' . $date . '", "' . $summary . '", ' . $type . ');';
	if (!($result = mysqli_query($connection, $query))){
		//echo ' nique';
		return FALSE;
	}
	else{
		$id = mysqli_insert_id($connection);
		if (!is_dir('pictures/tournament' . $id)) {
    			mkdir('pictures/tournament' . $id, 0775, true);
			//echo 'Il faut créer le dossier';
		}
		//echo $id;
		return $id;
	}
}

/*
Updates the tournament in the DB with the datas given as parameters.

*/

function updateTournament($connection, $id, $name, $date, $summary , $type){
	$query = 
'UPDATE `tournament` SET `name` = "' . $name .'", `date` = "' . $date .'" , `summary` = "' . $summary .'", `interclub` = ' . $type . ' WHERE tournament_id = ' . $id . ';';
	echo $query;
	return mysqli_query($connection, $query);
}

/*
Deletes the tournament in the DB which ID is given as the $id parameter.

*/

function deleteTournament($connection, $id){
	$query = 
'DELETE FROM tournament WHERE tournament_id = ' . $id . ';';
	echo $query;
	$dirPath = "pictures/tournament" . $id ."/";
	echo $dirPath;
    	if (! is_dir($dirPath)) {
        	throw new InvalidArgumentException("Path doesn't exist");
	}
    	$files = glob($dirPath . '*', GLOB_MARK);
    	foreach ($files as $file) {
		echo $file;
            	unlink($file);
	}
    	rmdir($dirPath);
	return mysqli_query($connection, $query);
}

//Echoes the html for 10 previews of tournament ordered from most recent to least recent starting from the article number $offset.
function previewPage($connection, $offset){
	$team = 'IS NULL';
	$page = "tournois.php?";
	if (isset($_GET["team_id"])){
		$team = '= ' . $_GET["team_id"];
		$page = "interclub.php?team_id=" . $_GET["team_id"] ."&";
	}
	$query = 
'SELECT * FROM tournament  WHERE interclub ' . $team . ' ORDER BY creation_date DESC LIMIT 10 OFFSET ' . $offset;
	$result = mysqli_query($connection, $query);
	while ($tournament = mysqli_fetch_assoc($result)){
		echo'
<div class="article" onclick="window.location=\'' . $page . 'id=' . $tournament[tournament_id] . '\';">
	<span class="date">' . $tournament[date] . '</span><br />
	<span class="title">' . $tournament[name] . '</span><br />
	<span class="preview">' . substr($tournament[summary], 0, 50) . ' ...</span>
</div>';
	}
}

/*Echoes the html for the headband
$page: 0 pour News, 1 pour Infos, 2 pour Tournoi, 3 pour Interclub

*/
function headband($connection, $page){
	$current = "";
	$active_li = array("", "", "", "");
	$disable_link = array("", "", "", "");
	switch ($page){
		case 0: 
			$current = "News";
			$active[$page] = ' class="active"';
			$disable_link[$page] = 'class="disabled"';
			break;
		case 1: 
			$current = "Infos";
			$active[$page] = ' class="active"';
			$disable_link[$page] = 'class="disabled"';
			break;
		case 2: 
			if (!isset($_GET["id"])){
				$current = "Tournois";
				$active[$page] = ' class="active"';
				$disable_link[$page] = 'class="disabled"';
			}
			else{
				$current = '<a href="tournois.php">Tournois</a>';
				$active[$page] = ' class="active"';
			}
			break;
	}
	$listTeams = "";
	$result = mysqli_query($connection, "SELECT * FROM interclub");
	while ($team = mysqli_fetch_assoc($result)){
		$teamID = $team[team_id];
		$teamName = $team[team_name];
		$activeTeam = "";
		$disabledTeam = "";
		if ($teamID == $_GET[team_id]){
			$activeTeam = ' class="active"';
			$disabledTeam = ' class="disabled"';
			if (!isset($_GET["id"])){
				$current = $teamName;
			}
			else{
				$current = '<a href="interclub.php?team_id=' . $teamID . '">' . $teamName . '</a>';
			}
		}
		$listTeams .= '<li' . $activeTeam . '> <a tabindex="-1" href="interclub.php?team_id=' . $teamID .'"' . $disabledTeam . '>'. $teamName . '</a> </li>' ;
	}

	echo'
<div class="dropdown menu-icon" >
   		<button class="btn btn-default dropdown-toggle menu-icon inactive-icon" type="button" data-toggle="dropdown">
			<span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
		</button>
    		<ul class="dropdown-menu">
      			<li' . $active[0] . '><a tabindex="-1" href="index.php"' . $disable_link[0] . '>News</a></li>
			<li role="separator" class="divider"></li>
      			<li' . $active[1] . '><a tabindex="-1" href="" ' . $disable_link[1] . '>Infos</a></li>
			<li role="separator" class="divider"></li>
			<li' . $active[2] . '><a tabindex="-1" href="tournois.php" ' . $disable_link[2] . '>Tournois</a></li>
			<li role="separator" class="divider"></li>
      			<li class="dropdown-submenu">
	<a class="test" tabindex="-1" href="#">Interclubs</a>
       	<ul class="dropdown-menu">' . $listTeams . '
        </ul>
			</li>
		</ul>
	</div>

		
	<div class="current">' . $current . '</div>


	<div class="btn-group user-icon">
  		<button type="button" class="btn btn-default dropdown-toggle user-icon inactive-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    			<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
  		</button>
  		<ul class="dropdown-menu dropdown-menu-right">
    			<li><a href="#"></a>';
	logSection();
	echo'
			</li>
  		</ul>
	</div>
	';
}


/*
Echoes the html for the logging section at the top right corner
*/
function logSection(){
	echo'
<form method="post" class="text-center border border-light p-5 connect_field" autocomplete="off">
	<p class="h4 mb-4">Connexion</p>
    	<input type="text" name="username" class="form-control mb-4" placeholder="Identifiant">
    	<input type="password" name="password" class="form-control mb-4" placeholder="Mot de Passe">
	<button class="btn btn-info btn-block my-4" type="submit">Se connecter</button>
	<input type="hidden" name="connect" value="yes">
</form>
<a href="manageinterclub.php">Gérer équipes interclubs</a>
	';
}

?>
