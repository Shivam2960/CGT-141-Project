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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT title, author, articleContent, created_at, updated_at FROM ListOfArticles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($article['title'] ?? "Article Not Found"); ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
    .meta { color: #555; font-size: 0.9em; margin-bottom: 20px; }
    .content { border-top: 1px solid #ccc; padding-top: 20px; }
  </style>
</head>
<body>
  <a href="articlesList.php">← Back to Articles</a>

  <?php if ($article): ?>
    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
    <div class="meta">
      <strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?><br>
      <strong>Created:</strong> <?php echo $article['created_at']; ?><br>
      <strong>Last Edited:</strong> <?php echo $article['updated_at']; ?>
    </div>

    <div class="content">
      <?php echo $article['articleContent']; ?>
    </div>
  <?php else: ?>
    <h2>Article not found.</h2>
  <?php endif; ?>
</body>
</html>
