<!doctype html>
<html class="+no-js no-js- no-js i-has-no-js">
<head>
<meta charset="UTF-8">
<title>MarioQuiz - Fragen-Update</title>
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

// HTML GET variables
$questionid = mysqli_real_escape_string ( $connection, $_GET ['questionid'] );

?>


<header>

		<div id="logo"></div>

	</header>

	<div id="joker">

		<ul>
			<li><a href="admin-frage.html"><em class="neuefrage"></em> NEUE FRAGE</a></li>
			<li><a href="admin-fragen-liste.php"><em class="fragenliste"></em> FRAGEN-LISTE</a></li>
		</ul>

	</div>

	<div id="einleitung">

		<div id="container">


			<h1>MarioQuiz: Frage bearbeiten</h1>

<?php
$result = mysqli_query ( $connection, "SELECT QuestionID, Category, Subcategory, Tag1, Tag2, Tag3, Language, Author, DatePublished, QuestionType, QuestionText, Answer1, Answer2, Answer3, Answer4, CorrectAnswer, DifficultyScore, Approved, Status, ImageName, ImagePath, ImageType FROM quizcomtext WHERE QuestionID = '" . $questionid . "'" );
$num_rows = mysqli_num_rows ( $result );
$row = mysqli_fetch_array ( $result );

if (! $result) {
	echo "<p>Fehler beim Abrufen der Daten von der Datenbank.</p>";
}

// echo "<li>" . $row['QuestionID'] . "</li>";

?>

<form
				action="qc-update.php"
				method="post"
				enctype="multipart/form-data"
			>

				<input
					class="admin-bild"
					type="hidden"
					name="questionid"
					value="<?php echo "$questionid"; ?>"
				>

				<div id="top-quiz">

					<div id="spalte-links">

						<span class="admin-fa">Unterkategorie</span>

						<!-- SELECT SUBCATEGORY -->

						<select
							class="admin-questiontype"
							name="subcategory"
						>
							<option selected="selected"><?php echo "" . $row['Subcategory'] . ""; ?></option>

<?php

// Fill option fields with subcategory of record and all other subcategories in the database but the empty ones

$result_subcategory = mysqli_query ( $connection, "SELECT DISTINCT Subcategory FROM quizcomtext WHERE Subcategory != '" . $row ['Subcategory'] . "' AND Subcategory != ''" );
$num_rows_subcategory = mysqli_num_rows ( $result_subcategory );

for($i = 0; $i <= $num_rows_subcategory; $i ++) {
	
	while ( $row_subcategory = mysqli_fetch_array ( $result_subcategory ) ) {
		echo "<option>";
		echo "" . $row_subcategory ['Subcategory'] . "";
		echo "</option>";
	}
}
?>
</select> <span class="admin-fa">Fragetyp</span>

						<!-- SELECT QUESTIONTYPE -->

						<select
							class="admin-questiontype"
							name="questiontype"
						>
							<option selected="selected"><?php echo "" . $row['QuestionType'] . ""; ?></option>

<?php

// Fill option fields with question type of record and all other question types in the database but the empty ones

$result_questiontype = mysqli_query ( $connection, "SELECT DISTINCT QuestionType FROM quizcomtext WHERE QuestionType != '" . $row ['QuestionType'] . "' AND QuestionType != ''" );
$num_rows_questiontype = mysqli_num_rows ( $result_questiontype );

for($i = 0; $i <= $num_rows_questiontype; $i ++) {
	
	while ( $row_questiontype = mysqli_fetch_array ( $result_questiontype ) ) {
		echo "<option>";
		echo "" . $row_questiontype ['QuestionType'] . "";
		echo "</option>";
	}
}
?>

</select> <span class="admin-fa">Autor</span> <select
							class="admin-questiontype"
							name="author"
						>
							<option selected="selected"><?php echo "" . $row['Author'] . ""; ?></option>

<?php

// Fill option fields with author of record and all other authors in the database but the empty ones

$result_author = mysqli_query ( $connection, "SELECT DISTINCT Author FROM quizcomtext WHERE Author != '" . $row ['Author'] . "' AND Author != ''" );
$num_rows_author = mysqli_num_rows ( $result_author );

for($i = 0; $i <= $num_rows_author; $i ++) {
	
	while ( $row_author = mysqli_fetch_array ( $result_author ) ) {
		echo "<option>";
		echo "" . $row_author ['Author'] . "";
		echo "</option>";
	}
}
?>
</select>


					</div>

					<div id="spalte-rechts">

						<span class="admin-fa">Tags</span> <input
							class="admin-tag"
							name="tag1"
							id="tag1"
							size="30"
							value="<?php echo $row['Tag1']; ?>"
						> <input
							class="admin-tag"
							name="tag2"
							id="tag2"
							size="30"
							value="<?php echo $row['Tag2']; ?>"
						> <input
							class="admin-tag"
							name="tag3"
							id="tag3"
							size="30"
							value="<?php echo $row['Tag3']; ?>"
						>

					</div>

					<div class="clear"></div>

					<span class="admin-fa">Frage</span> <input
						class="admin-frage"
						name="questiontext"
						id="questiontext"
						placeholder=""
						size="80"
						value="<?php echo $row['QuestionText']; ?>"
					>

					<div id="spalte-links">
						<span class="admin-fa">Antwort 1:</span><input
							class="admin-antwort"
							name="answer1"
							id="answer1"
							maxlenght="100"
							size="48"
							value="<?php echo $row['Answer1']; ?>"
						> <span class="admin-fa">Antwort 2:</span><input
							class="admin-antwort"
							name="answer2"
							id="answer2"
							maxlenght="100"
							size="48"
							value="<?php echo $row['Answer2']; ?>"
						> <span class="admin-fa">Antwort 3:</span><input
							class="admin-antwort"
							name="answer3"
							id="answer3"
							maxlenght="100"
							size="48"
							value="<?php echo $row['Answer3']; ?>"
						> <span class="admin-fa">Antwort 4:</span><input
							class="admin-antwort"
							name="answer4"
							id="answer4"
							maxlenght="100"
							size="48"
							value="<?php echo $row['Answer4']; ?>"
						> <span class="admin-fa">Richtige Antwort:</span> <input
							class="admin-richtigeantwort"
							name="correctanswer"
							id="correctanswer"
							size="2"
							maxlength="1"
							value="<?php echo $row['CorrectAnswer']; ?>"
						> <span class="admin-fa">Schwierigkeitsgrad (1-10):</span> <input
							class="admin-richtigeantwort"
							name="difficultyscore"
							id="difficultyscore"
							size="2"
							maxlength="1"
							value="<?php echo $row['DifficultyScore']; ?>"
						>
						</p>

					</div>

					<div id="spalte-rechts">
						<h2>Bild austauschen</h2>
						<small>Das Bild wird physikalisch nicht verkleinert, aber automatisch umbenannt!
							Anzeigeh&ouml;he: immer 140px. Frei lassen f&uuml;r kein Bild.</small><br> <input
							class="admin-bild"
							type="file"
							name="img_update"
						>

<?php

// If there is an image path insert the image, max height 140 pixels

if ($row ['ImagePath'] != '') {
	echo "<p><small>";
	echo "Hochgeladenes Bild: <br />";
	echo "<img src='" . $row ['ImagePath'] . "' height='140'/>";
	echo "<input class='admin-bild' type='hidden' name='img_old' value='" . $row ['ImagePath'] . "'>";
	echo "</small></p>";
}
?>

<h2>Geprüft</h2>

<?php
// If approved then show first set of radio buttons with checked 'approved', else show second set

if ($row ['Approved'] == 1) {
	echo "<input class='radio' type='radio' name='approved' value='1' checked='checked'> <span class='admin-rb'>Ja</span><br>";
	echo "<input class='radio' type='radio' name='approved' value='0'> <span class='admin-rb'>Nein</span>";
} 

else {
	echo "<input class='radio' type='radio' name='approved' value='1'> <span class='admin-rb'>Ja</span><br>";
	echo "<input class='radio' type='radio' name='approved' value='0' checked='checked'> <span class='admin-rb'>Nein</span>";
}

?>

<h2>Status</h2>

<?php
// If approved then show first set of radio buttons with checked 'status', else show second set

if ($row ['Status'] == 1) {
	echo "<input class='radio' type='radio' name='status' value='1' checked='checked'> <span class='admin-rb'>Aktiv</span><br>";
	echo "<input class='radio' type='radio' name='status' value='0'> <span class='admin-rb'>Inaktiv</span><br>";
	echo "<input class='radio' type='radio' name='status' value='2'> <span class='admin-rb'>Entfernt</span><br>";
}

if ($row ['Status'] == 0) {
	echo "<input class='radio' type='radio' name='status' value='1'> <span class='admin-rb'>Aktiv</span><br>";
	echo "<input class='radio' type='radio' name='status' value='0' checked='checked'> <span class='admin-rb'>Inaktiv</span><br>";
	echo "<input class='radio' type='radio' name='status' value='2'> <span class='admin-rb'>Entfernt</span><br>";
}

//

?>

<small>Frage wird aus der Fragen-Liste entfernt, aber nicht aus der Datenbank
							gelöscht.</small><br>

					</div>

					<div id="bottom-quiz">

						<input
							type="submit"
							class="submit"
							name="Frage aktualisieren"
							value="Frage aktualisieren"
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


</body>
</html>