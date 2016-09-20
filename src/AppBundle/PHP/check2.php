<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta
	http-equiv="Content-Type"
	content="text/html; charset=utf-8"
/>
<title>Unbenanntes Dokument</title>
</head>

<body>

<?php
$dbhost = 'localhost';
$dbuser = 'd01f2e47';
$dbpass = '4Y4E5p3aErY4JrMN';
$conn = mysql_connect ( $dbhost, $dbuser, $dbpass );
if (! $conn) {
	die ( 'Could not connect: ' . mysql_error () );
}
$sql = 'INSERT INTO quizcomtext' . '(QuestionText) ' . 'VALUES ( "Why?")';

mysql_select_db ( 'd01f2e47' );
$retval = mysql_query ( $sql, $conn );
if (! $retval) {
	die ( 'Could not enter data: ' . mysql_error () );
}
echo "Entered data successfully\n";
mysql_close ( $conn );
?>
</body>
</html>