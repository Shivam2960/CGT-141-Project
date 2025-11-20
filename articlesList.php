<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username   = "wiki_user";
$password   = "CGT141Sucks!";
$dbname     = "articles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unofficial articles (user-submitted) sorted alphabetically by title
$sql    = "SELECT id, title, author FROM ListOfArticles ORDER BY title ASC";
$result = $conn->query($sql);

// Define official articles (static HTML pages on the server)
$officialArticles = [
    [
        'title'  => 'Pokemon Red and Blue Review',
        'author' => 'Ryu',
        'path'   => 'pokemon.html'
    ],
    // Add more official articles here as you create them:
    // [
    //     'title'  => 'Super Mario Bros. Retrospective',
    //     'author' => 'Shivam',
    //     'path'   => 'smb_retro.html'
    // ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  >

  <!-- Custom Styles -->
  <link href="master.css" rel="stylesheet" type="text/css" />

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>All Articles - The Wiki of Games</title>
</head>
<body>

  <!-- ======= HEADER ======= -->
  <header class="head">
    <div class="container-fluid">
      <div class="head-inner d-flex align-items-center">

        <!-- Brand -->
        <a href="index.html"
           class="header-item brand-link d-flex align-items-center justify-content-center text-decoration-none">
          <img src="images/icon.png" alt="banner" class="brand-icon">
          <span class="brand-text">The Wiki of Games</span>
        </a>

        <!-- Navigation -->
        <a href="articlesList.php"
           class="header-item top-link text-decoration-none text-center">
          View Articles
        </a>

        <a href="addArticle.html"
           class="header-item top-link text-decoration-none text-center">
          Write Articles
        </a>

        <a href="aboutwebsite.html"
           class="header-item top-link text-decoration-none text-center">
          About the Website
        </a>
      </div>
    </div>
  </header>

  <!-- ======= MAIN CONTENT ======= -->
  <main class="page-content container mt-4">
    <h1 class="page-title mb-4">All Articles</h1>

    <!-- OFFICIAL ARTICLES -->
    <section class="mb-5">
      <h2 class="mb-3">Official Articles</h2>

      <?php if (!empty($officialArticles)): ?>
        <div class="list-group">
          <?php foreach ($officialArticles as $article): ?>
            <?php
              $title  = htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');
              $author = htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8');
              $path   = htmlspecialchars($article['path'], ENT_QUOTES, 'UTF-8');
            ?>
            <a href="<?= $path ?>"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span class="article-title"><?= $title ?></span>
              <span class="text-muted small">by <?= $author ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted">No official articles have been published yet.</p>
      <?php endif; ?>
    </section>

    <!-- UNOFFICIAL ARTICLES -->
    <section class="mb-5">
      <h2 class="mb-3">Unofficial Articles</h2>

      <?php if ($result && $result->num_rows > 0): ?>
        <div class="list-group">
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              $id     = (int)$row['id'];
              $title  = htmlspecialchars($row['title']  ?? '', ENT_QUOTES, 'UTF-8');
              $author = htmlspecialchars($row['author'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
            <a href="viewArticle.php?id=<?= $id ?>"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span class="article-title"><?= $title ?></span>
              <span class="text-muted small">by <?= $author ?></span>
            </a>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="text-muted">No unofficial articles have been submitted yet.</p>
      <?php endif; ?>
    </section>

    <?php
      if ($result) {
          $result->free();
      }
      $conn->close();
    ?>
  </main>

  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous">
  </script>

</body>
</html>
