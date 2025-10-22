<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "wiki_user";
$password = "CGT141Sucks!";
$dbname = "articles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];
$author = $_POST['author'];
$content = $_POST['articleContent'];  // the new field

$sql = "INSERT INTO ListOfArticles (title, author, articleContent) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $title, $author, $content);
$stmt->execute();

echo "New article added successfully! <a href='index.html'>Back to form</a>";

$stmt->close();
$conn->close();
?>
