<!doctype html>
<html class="+no-js no-js- no-js i-has-no-js">
<head>
<meta charset="UTF-8">
<title>MarioQuiz - Update</title>
<meta
	name="viewport"
	content="width=device-width, initial-scale=1.0, user-scalable=0;"
>
<meta
	name="decription"
	content=""
>
<meta
	name="HandheldFriendly"
	content="True"
>
<meta
	name="MobileOptimized"
	content="320"
>
<meta
	http-equiv="X-UA-Compatible"
	content="IE=edge,chrome=1"
/>
<meta
	http-equiv="cleartype"
	content="on"
>
<link
	rel="stylesheet"
	href="css/normalize.css"
>
<link
	rel="stylesheet"
	href="css/styles-admin.css"
>
<link
	href='http://fonts.googleapis.com/css?family=Roboto+Condensed'
	rel='stylesheet'
	type='text/css'
>
<link
	rel="stylesheet"
	href="font-awesome/css/font-awesome.min.css"
>

<script src="js/modernizr.js"></script>


</head>

<body>

	<header>

		<div id="logo"></div>

	</header>

	<div id="joker">

		<ul>
			<li><a href="admin-frage.html"><em class="neuefrage"></em> NEUE FRAGE</a></li>
			<li><a href="admin-fragen-liste.php"><em class="fragenliste"></em> FRAGEN-LISTE</a></li>
		</ul>
	</div>

	<div id="fragen-liste">
		<div id="container">

<?php

// Global variables
$dbhost = 'localhost';
$dbuser = 'd01f2e47';
$dbpass = '4Y4E5p3aErY4JrMN';
$dbname = 'd01f2e47';
$tablename = 'quizcomtext';

// 1. Create a database connection
$connection = mysqli_connect ( $dbhost, $dbuser, $dbpass );
if (! $connection) {
	die ( "Database connection failed: " . mysqli_error () );
}

// 2. Select a database to use
$db_select = mysqli_select_db ( $connection, $dbname );
if (! $db_select) {
	die ( "Database selection failed: " . mysqli_error () );
}

// 3. Set Database connection to UTF-8 to keep umlaute alive
mysqli_query ( $connection, "SET NAMES 'utf8'" );

// HTML post variables
$questionid = mysqli_real_escape_string ( $connection, $_POST ['questionid'] );
$category = "MarioQuiz";
$subcategory = mysqli_real_escape_string ( $connection, $_POST ['subcategory'] );
$tag1 = mysqli_real_escape_string ( $connection, $_POST ['tag1'] );
$tag2 = mysqli_real_escape_string ( $connection, $_POST ['tag2'] );
$tag3 = mysqli_real_escape_string ( $connection, $_POST ['tag3'] );
$language = "German";
$author = mysqli_real_escape_string ( $connection, $_POST ['author'] );
$datepublished = date ( 'Y-m-d' );
$questiontype = mysqli_real_escape_string ( $connection, $_POST ['questiontype'] );
$questiontext = mysqli_real_escape_string ( $connection, $_POST ['questiontext'] );
$answer1 = mysqli_real_escape_string ( $connection, $_POST ['answer1'] );
$answer2 = mysqli_real_escape_string ( $connection, $_POST ['answer2'] );
$answer3 = mysqli_real_escape_string ( $connection, $_POST ['answer3'] );
$answer4 = mysqli_real_escape_string ( $connection, $_POST ['answer4'] );
$correctanswer = mysqli_real_escape_string ( $connection, $_POST ['correctanswer'] );
$difficultyscore = mysqli_real_escape_string ( $connection, $_POST ['difficultyscore'] );
$approved = mysqli_real_escape_string ( $connection, $_POST ['approved'] );
$status = mysqli_real_escape_string ( $connection, $_POST ['status'] );

// Image data
$tmpname = $_FILES ['img_update'] ['tmp_name'];
$imagetype = $_FILES ['img_update'] ['type'];

// Check if no new image was uploaded...

if (! file_exists ( $_FILES ['img_update'] ['tmp_name'] ) || ! is_uploaded_file ( $_FILES ['img_update'] ['tmp_name'] )) {
	
	// ... update all the fields that are not image related
	$record_update = "UPDATE quizcomtext SET Subcategory='" . $subcategory . "', Tag1='" . $tag1 . "', Tag2='" . $tag2 . "', Tag3='" . $tag3 . "', Author='" . $author . "', QuestionType='" . $questiontype . "', QuestionText='" . $questiontext . "', Answer1='" . $answer1 . "', Answer2='" . $answer2 . "', Answer3='" . $answer3 . "', Answer4='" . $answer4 . "', CorrectAnswer='" . $correctanswer . "', DifficultyScore='" . $difficultyscore . "', Approved='" . $approved . "', Status='" . $status . "' WHERE QuestionID='$questionid'";
	
	// Check Insertion
	if (mysqli_query ( $connection, $record_update )) {
		
		echo "<h1>";
		echo "Daten erfolgreich aktualisiert!";
		echo "</h1>";
	} 

	else {
		
		echo "<h1>";
		echo "Fehler: Leider konnten die Daten nicht eingetragen werden. Frag Sascha, warum." . mysqli_error ( $connection );
		echo "</h1>";
	}
} 

else // If new image was uploaded then do this

{
	
	// Image related variables
	
	$allowedimagetype = array (
			'image/jpeg',
			'image/gif',
			'image/png' 
	);
	$hndFile = fopen ( $tmpname, "r" );
	$imagedata = addslashes ( fread ( $hndFile, filesize ( $tmpname ) ) );
	$imagename = $_FILES ['img_update'] ['name'];
	$imagesize = getimagesize ( $_FILES ['img_update'] ['tmp_name'] );
	$imagewidth = $imagesize [0];
	$imageheight = $imagesize [1];
	$imagemaxwidth = 280;
	$imagemaxheight = 140;
	$uploaddir = 'quiz-images/';
	$imagepath = $uploaddir . $imagename;
	
	// Check uploaded file
	
	if ($imagesize == FALSE) {
		echo "Das ist kein Bild. Sieht doch jeder.";
		return false;
	} 

	elseif (! in_array ( $imagetype, $allowedimagetype )) {
		echo "Bitte nur jpg, gif and png.";
		return false;
	} 

	elseif ($imagewidth > $imagemaxwidth) {
		echo "Das Bild ist zu hoch. Max. Höhe: 140px.";
		return false;
	} 

	elseif ($imageheight > $imagemaxheight) {
		echo "Das Bild ist zu breit. Max. Breite: 280px.";
		return false;
	} 

	else 

	{
		
		// echo "$imagewidth $imageheight";
		
		$fileupload = move_uploaded_file ( $tmpname, $imagepath );
		if (! $fileupload) {
			echo "<h1>";
			echo "Es gab Probleme beim Hochladen des Bildes. Versuch es noch einmal &ndash; oder frag Sascha.";
			echo "</h1>";
			exit ();
		}
		
		// Update all records
		$record_update = "UPDATE quizcomtext SET Subcategory='" . $subcategory . "', Tag1='" . $tag1 . "', Tag2='" . $tag2 . "', Tag3='" . $tag3 . "', Author='" . $author . "', QuestionType='" . $questiontype . "', QuestionText='" . $questiontext . "', Answer1='" . $answer1 . "', Answer2='" . $answer2 . "', Answer3='" . $answer3 . "', Answer4='" . $answer4 . "', CorrectAnswer='" . $correctanswer . "', DifficultyScore='" . $difficultyscore . "', Approved='" . $approved . "', Status='" . $status . "', ImageName='" . $imagename . "', ImagePath='" . $imagepath . "', ImageType='" . $imagetype . "'  WHERE QuestionID='$questionid'";
		
		?>





<?php
		// Check Insertion
		if (mysqli_query ( $connection, $record_update )) {
			
			echo "<h1>";
			echo "Daten erfolgreich aktualisiert!";
			echo "</h1>";
		} 

		else {
			
			echo "<h1>";
			echo "Fehler: Leider konnten die Daten nicht eingetragen werden. Frag Sascha, warum." . mysqli_error ( $connection );
			echo "</h1>";
		}
	} // close "Check if else "Check if file was uploaded"
	  
	// Close connection
	
	mysqli_close ( $connection );
}

?>

</div>
	</div>


	<footer>

		<div id="footerlinks">
			<ul>
				<li><a href="start.html">Zur&uuml;ck zur Frage <?php echo "" . $row['QuestionText'] . "";?></a></li>
			</ul>
		</div>





		<div class="clear"></div>

	</footer>

	<!-- SAS Active-Link via IPhone aktivieren, ggf. auslagern -->
	<script type="text/javascript">
	document.addEventListener("touchstart", function() {},false);
</script>

	<!-- SAS JQuery -->

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

	<script src="jquery.fitvids.js"></script>
	<script>
        // Basic FitVids Test
        $("article").fitVids();
		</script>

	<script
		src="js/respond.js"
		type="text/javascript"
	></script>

</body>
</html>