<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); 

mysqli_report(MYSQLI_REPORT_OFF);

$servername = "localhost";
$username   = "wiki_user";
$password   = "CGT141ISGREAT!";
$dbname     = "articles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "ERROR_DB_CONNECT";
    exit;
}

$title   = $_POST['title']          ?? '';
$author  = $_POST['author']         ?? '';
$content = $_POST['articleContent'] ?? '';


if ($title === '' || $author === '' || trim(strip_tags($content)) === '') {
    die('Title, author, and content are required.');
}
$sql  = "INSERT INTO ListOfArticles (title, author, articleContent) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "ERROR_PREPARE";
    $conn->close();
    exit;
}

$stmt->bind_param("sss", $title, $author, $content);

try {
    if ($stmt->execute()) {
        echo "SUCCESS";
    } else {
        if ($stmt->errno === 1062 || $conn->errno === 1062) {
            echo "DUPLICATE";
        } else {
            echo "ERROR";
        }
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo "DUPLICATE";
    } else {
        echo "ERROR";
    }
}

$stmt->close();
$conn->close();
?>
