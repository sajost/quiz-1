<!doctype html>
<html class="+no-js no-js- no-js i-has-no-js">
<head>
<meta charset="UTF-8">
<title>MarioQuiz - Admin-Bereich</title>
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

<?php
if (isset ( $_POST ['submit'] )) {
	
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
	
	// HTML post variables
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
	
	// Image data
	$tmpname = $_FILES ['img'] ['tmp_name'];
	$type = $_FILES ['img'] ['type'];
	$hndFile = fopen ( $tmpname, "r" );
	$data = addslashes ( fread ( $hndFile, filesize ( $tmpname ) ) );
	
	// Message
	$message = "This was success!";
	
	// Insert records
	$record = "INSERT INTO quizcomtext (Category, Subcategory, Tag1, Tag2, Tag3, Language, Author, DatePublished, QuestionType, QuestionText, Answer1, Answer2, Answer3, Answer4, CorrectAnswer, DifficultyScore, Approved, ImgData, ImgType) VALUES ('$category', '$subcategory', '$tag1', '$tag2', '$tag3', '$language', '$author', '$datepublished', '$questiontype', '$questiontext', '$answer1', '$answer2', '$answer3', '$answer4', '$correctanswer', '$difficultyscore', '$approved', '$data', '$type')";
	
	// Check Insertion
	if (mysqli_query ( $connection, $record )) {
		
		echo "Records successfully inserted.";
	} else {
		
		echo "ERROR: Could not able to execute $sql. " . mysqli_error ( $connection );
	}
	
	// Close connection
	
	mysqli_close ( $connection );
} 

else 

{
	// Assuming you keep your html and php together
	?>
<header>

		<div id="logo"></div>

	</header>

	<div id="joker">

		<ul>
			<li><a href="admin-frage.html"><em class="neuefrage"></em> NEUE FRAGE</a></li>
			<li><a href="admin-fragen-liste.html"><em class="fragenliste"></em> FRAGEN-LISTE</a></li>
			<li><a href="#"><em class="admin-home"></em> HOME</a></li>
		</ul>

	</div>

	<div id="einleitung">

		<div id="container">


			<h1>MarioQuiz: Neue Frage eintragen</h1>


			<form
				action="http://qc.sea-sight.com/admin-frage-same.php"
				method="post"
				enctype="multipart/form-data"
			>

<?php echo $message; ?>

<div id="top-quiz">

					<h2>Neue Frage</h2>

					<div id="spalte-links">

						<span class="admin-fa">Unterkategorie</span> <select
							class="admin-questiontype"
							name="subcategory"
						>
							<option>Super Mario Land</option>
							<option>Super Mario World</option>
						</select> <span class="admin-fa">Fragetyp</span> <select
							class="admin-questiontype"
							name="questiontype"
						>
							<option>OneOutOfFour</option>
						</select> <span class="admin-fa">Autor</span> <select
							class="admin-questiontype"
							name="author"
						>
							<option>Sascha</option>
							<option>Lenny</option>
						</select>


					</div>

					<div id="spalte-rechts">

						<span class="admin-fa">Tags</span> <input
							class="admin-tag"
							name="tag1"
							id="tag1"
							placeholder=""
							size="30"
						> <input
							class="admin-tag"
							name="tag2"
							id="tag2"
							placeholder=""
							size="30"
						> <input
							class="admin-tag"
							name="tag3"
							id="tag3"
							placeholder=""
							size="30"
						>

					</div>

					<div class="clear"></div>

					<span class="admin-fa">Frage</span> <input
						class="admin-frage"
						name="questiontext"
						id="questiontext"
						placeholder=""
						size="80"
					>

					<div id="spalte-links">
						<span class="admin-fa">Antwort 1:</span><input
							class="admin-antwort"
							name="answer1"
							id="answer1"
							placeholder=""
							maxlenght="100"
							size="48"
						> <span class="admin-fa">Antwort 2:</span><input
							class="admin-antwort"
							name="answer2"
							id="answer2"
							placeholder=""
							maxlenght="100"
							size="48"
						> <span class="admin-fa">Antwort 3:</span><input
							class="admin-antwort"
							name="answer3"
							id="answer3"
							placeholder=""
							maxlenght="100"
							size="48"
						> <span class="admin-fa">Antwort 4:</span><input
							class="admin-antwort"
							name="answer4"
							id="answer4"
							placeholder=""
							maxlenght="100"
							size="48"
						> <span class="admin-fa">Richtige Antwort:</span> <input
							class="admin-richtigeantwort"
							name="correctanswer"
							id="correctanswer"
							placeholder=""
							size="2"
							maxlength="1"
						> <span class="admin-fa">Schwierigkeitsgrad (1-10):</span> <input
							class="admin-richtigeantwort"
							name="difficultyscore"
							id="difficultyscore"
							placeholder="5"
							size="2"
							maxlength="1"
						>
						</p>

					</div>

					<div id="spalte-rechts">
						<h2>Bild einf&uuml;gen</h2>
						<small>Das Bild wird physikalisch nicht verkleinert, aber automatisch umbenannt!
							Anzeigeh&ouml;he: immer 140px. Frei lassen f&uuml;r kein Bild.</small> <input
							class="admin-bild"
							type="file"
							name="img"
						>

						<h2>Geprüft</h2>
						<input
							class="radio"
							type="radio"
							name="approved"
							value="1"
						> <span class="admin-rb">Ja</span><br> <input
							class="radio"
							type="radio"
							name="approved"
							value="0"
							checked="checked"
						> <span class="admin-rb">Nein</span><br>


					</div>

					<div id="bottom-quiz">

						<input
							type="submit"
							name="Frage eintragen"
							value="Frage eintragen"
						>

					</div>

				</div>

			</form>

		</div>



	</div>


	<footer>

		<div id="footerlinks">
			<ul>
				<li><a href="start.html">Zum Quiz</a></li>
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

<?
}
?>

</body>
</html>