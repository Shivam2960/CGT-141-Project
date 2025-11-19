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

$sql = "SELECT id, title, author FROM ListOfArticles ORDER BY title ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>All Articles</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 30px; }
    a { text-decoration: none; color: blue; }
    a:hover { text-decoration: underline; }
    .article { margin-bottom: 10px; }
  </style>
</head>
<body>
  <h1>All Articles</h1>
  <p><a href="index.html">← Back to Home</a></p>

  <?php
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          echo "<div class='article'>";
          echo "<a href='viewArticle.php?id=" . $row['id'] . "'>";
          echo htmlspecialchars($row['title'] ?? '');
          echo "</a> by " . htmlspecialchars($row['author'] ?? '');
          echo "</div>";
      }
  } else {
      echo "<p>No articles found.</p>";
  }

  $conn->close();
  ?>
</body>
</html>
