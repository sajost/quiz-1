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

	<div id="fragen-liste">

		<div id="container">


			<h1>Fragen-Liste</h1>


			<form>

				<div id="top-quiz">

					<h2>Welches Quiz willst du bearbeiten?</h2>
					<p>
						<strong>MarioQuiz</strong>
					
					
					<p>
						Im <a
							href="start.html"
							target="_blank"
						>MarioQuiz</a> befinden sich <span class="strong">1234</span> Fragen!
					</p>


<?php

// For loop: Find out the max. number of distinct subcategories

$result_distinct_categories = mysqli_query ( $connection, "SELECT DISTINCT Subcategory FROM quizcomtext" );
$num_rows_distinct_categories = mysqli_num_rows ( $result_distinct_categories );

// $row = mysqli_fetch_array($distinct_categories);
// echo $row['Subcategory'];
$array_distinct_categories = array (); // Make an array to hold all the data
$index = 0;

while ( $row = mysqli_fetch_assoc ( $result_distinct_categories ) ) { // Loop to give you the data in an associative array
	$array_distinct_categories [] = $row;
}

echo $num_rows_distinct_categories;
echo "<br>";
echo $array_distinct_categories [0] ['Subcategory'];
echo "<br>";
echo $array_distinct_categories [1] ['Subcategory'];

// Create loop for tables
$i = 0;

$subcategory = array (
		"Super Mario Land",
		"Super Mario World" 
);

// Subcategory 1

$sub1 = $subcategory [0];
$result = mysqli_query ( $connection, "SELECT Subcategory, QuestionID, QuestionText FROM quizcomtext WHERE Subcategory='" . $sub1 . "'" );
$num_rows = mysqli_num_rows ( $result );
if (! $result || mysqli_num_rows ( $result ) == 0) {
	echo "No rows found";
	exit ();
}

echo "<h2 class=\"h2-subkategorie\">Subkategorie: $sub1<span>";
echo $num_rows;
echo " Fragen</span></h2>";

echo "<table>
<tr>
<th>ID</th>
<th>Frage</th>
<th>Status</th>
</tr>";

while ( $row = mysqli_fetch_array ( $result ) ) {
	echo "<tr>";
	echo "<td>" . $row ['QuestionID'] . "</td>";
	echo "<td>" . $row ['QuestionText'] . "</td>";
	echo "<td>" . $row ['Status'] . "</td>";
	echo "</tr>";
}
echo "</table>";

// Subcategory 2

$sub2 = $subcategory [1];
$result = mysqli_query ( $connection, "SELECT Subcategory, QuestionID, QuestionText FROM quizcomtext WHERE Subcategory='" . $sub2 . "'" );
$num_rows = mysqli_num_rows ( $result );

if (! $result || mysqli_num_rows ( $result ) == 0) {
	echo "No rows found";
	exit ();
}

echo "<h2 class=\"h2-subkategorie\">Subkategorie: $sub2<span>";
echo $num_rows;
echo " Fragen</span></h2>";

echo "<table>
<tr>
<th>ID</th>
<th>Frage</th>
<th>Status</th>
</tr>";

while ( $row = mysqli_fetch_array ( $result ) ) {
	echo "<tr>";
	echo "<td>" . $row ['QuestionID'] . "</td>";
	echo "<td>" . $row ['QuestionText'] . "</td>";
	echo "<td>" . $row ['Status'] . "</td>";
	echo "</tr>";
}
echo "</table>";

mysqli_close ( $connection );
?>

</div>
		
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