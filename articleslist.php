<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username   = "wiki_user";
$password   = "CGT141ISGREAT!";
$dbname     = "articles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
  SELECT id, title, author
  FROM ListOfArticles
  WHERE title IS NOT NULL
    AND title <> ''
    AND author IS NOT NULL
    AND author <> ''
    AND articleContent IS NOT NULL
    AND articleContent <> ''
  ORDER BY title ASC
";
$result = $conn->query($sql);

$officialArticles = [

    [
        'title'  => 'NBA 2K Series Overview',
        'author' => 'Shivam Patel',
        'path'   => 'nba2k.html'
    ],
        [
        'title'  => 'Minecraft',
        'author' => 'Shivam Patel',
        'path'   => 'minecraft.html'
    ],
    [
    'title'  => 'League of Legends',
    'author' => 'Ryan He',
    'path'   => 'leagueoflegends.html'
],
    [
    'title'  => 'FC Series (FIFA)',
    'author' => 'Ryunosuke Matsuda',
    'path'   => 'fifa.html'
],
    [
    'title'  => 'Pokemon',
    'author' => 'Ryunosuke Matsuda',
    'path'   => 'pokemon.html'
],
[
  'title'  => 'Once Human: Is it worth playing?',
  'author' => 'Yoonki Lee',
  'path'   => 'oncehuman.html'
],
[
  'title'  => 'Wuthering Waves: Iuno & Chisa Character Lore',
  'author' => 'Yoonki Lee',
  'path'   => 'wutheringwaves.html'
],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <link href="master.css" rel="stylesheet" type="text/css">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Articles - The Wiki of Games</title>


</head>
<body>
  <header class="head">
    <div class="container-fluid">
      <div class="head-inner">

        <a href="index.html"
           class="header-item brand-link d-flex align-items-center justify-content-center text-decoration-none"
           aria-label="The Wiki of Games homepage">
          <img src="images/icon.png" alt="The Wiki of Games logo" class="brand-icon">
          <span class="brand-text">The Wiki of Games</span>
        </a>

        <nav class="main-nav d-flex flex-grow-1 justify-content-center" aria-label="Main navigation">
          <a href="index.html"
             class="header-item top-link text-decoration-none text-center">
            Home Page
          </a>

          <a href="articleslist.php"
             class="header-item top-link text-decoration-none text-center">
            View Articles
          </a>

          <a href="addarticle.html"
             class="header-item top-link text-decoration-none text-center">
            Write Articles
          </a>

          <a href="aboutwebsite.html"
             class="header-item top-link text-decoration-none text-center">
            About the Website
          </a>
        </nav>

      </div>
    </div>
  </header>


  <main class="page-content container mt-4">

    <h1 class="page-title mb-4">All Articles</h1>


<section class="mb-5" aria-labelledby="official-heading">

<div class="article-status-box">
  <h2 class="h6 mb-2">Article Types</h2>
  <p class="mb-1">
    <strong>Official Articles</strong> are fact-checked and locked by developers.
  </p>
  <p class="mb-0">
    <strong>Unofficial Articles</strong> are community-written, unverified, and editable.
  </p>

  <p class="conversion-note">
    Once an unofficial article is in a good state, it will be converted into an official
    article by the developers of this website during their free time.
  </p>
</div>


  <h2 id="official-heading" class="mb-3">Official Articles</h2>

  <?php if (!empty($officialArticles)): ?>
    <ul class="list-group">

      <?php foreach ($officialArticles as $article): ?>
        <?php
          $title  = htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');
          $author = htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8');
          $path   = htmlspecialchars($article['path'],   ENT_QUOTES, 'UTF-8');
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="<?= $path ?>" class="text-decoration-none flex-grow-1">
            <span class="article-title"><?= $title ?></span>
          </a>
          <span class="text-muted small ms-2">by <?= $author ?></span>
        </li>
      <?php endforeach; ?>

    </ul>
  <?php else: ?>
    <p class="text-muted">No official articles have been published yet.</p>
  <?php endif; ?>

  <div class="clear-floats"></div>

</section>



<section class="mb-5" aria-labelledby="unofficial-heading">
  <h2 id="unofficial-heading" class="mb-3">Unofficial Articles</h2>

  <?php if ($result && $result->num_rows > 0): ?>
    <ul class="list-group">

      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $id     = (int)$row['id'];
          $title  = htmlspecialchars($row['title']  ?? '', ENT_QUOTES, 'UTF-8');
          $author = htmlspecialchars($row['author'] ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="viewarticle.php?id=<?= $id ?>" class="text-decoration-none flex-grow-1">
            <span class="article-title"><?= $title ?></span>
          </a>
          <span class="text-muted small ms-2">by <?= $author ?></span>
        </li>
      <?php endwhile; ?>

    </ul>
  <?php else: ?>
    <p class="text-muted">No unofficial articles have been submitted yet.</p>
  <?php endif; ?>

</section>

    <?php if ($result) $result->free(); $conn->close(); ?>

  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
