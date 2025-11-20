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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle edits (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
    $newTitle   = trim($_POST['title'] ?? '');
    $editorName = trim($_POST['editor_name'] ?? '');
    $newContent = $_POST['articleContent'] ?? ''; // may contain HTML

    // 1) Fetch current editors for this article
    $sqlEditors = "SELECT editors FROM ListOfArticles WHERE id = ?";
    $stmtEditors = $conn->prepare($sqlEditors);
    $stmtEditors->bind_param("i", $id);
    $stmtEditors->execute();
    $resEditors = $stmtEditors->get_result();
    $rowEditors = $resEditors->fetch_assoc();
    $currentEditorsStr = $rowEditors['editors'] ?? '';
    $stmtEditors->close();

    // 2) Build new editors list (comma-separated, unique-ish)
    $editorsArray = [];
    if ($currentEditorsStr !== '') {
        $editorsArray = array_map('trim', explode(',', $currentEditorsStr));
    }

    // 3) Add editor if provided and not already listed
    if ($editorName !== '') {
        if (!in_array($editorName, $editorsArray, true)) {
            $editorsArray[] = $editorName;
        }
    }

    $updatedEditorsStr = implode(', ', $editorsArray);

    // 4) Only update if we have a non-empty title and content
    if ($newTitle !== '' && trim(strip_tags($newContent)) !== '') {
        $updateSql = "
            UPDATE ListOfArticles
            SET title = ?, editors = ?, articleContent = ?, updated_at = NOW()
            WHERE id = ?
        ";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("sssi", $newTitle, $updatedEditorsStr, $newContent, $id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }

    // Redirect to avoid form resubmission on refresh
    header("Location: viewArticle.php?id=" . $id);
    exit;
}

// Fetch article for display
$sql = "SELECT title, author, articleContent, created_at, updated_at, editors
        FROM ListOfArticles
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result  = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Prepare editors list for dropdown
$editorsList = [];
if ($article && !empty($article['editors'])) {
    $editorsList = array_filter(array_map('trim', explode(',', $article['editors'])));
}
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

  <title>
    <?php echo htmlspecialchars($article['title'] ?? "Article Not Found"); ?>
  </title>

  <!-- TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/4uk0fzz7q2ybbsb8tg0kb5dt5cavktkmnkviu8c4or5c2urs/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (document.getElementById('articleContentEdit')) {
        tinymce.init({
          selector: '#articleContentEdit',
          plugins: 'advlist autolink lists link image charmap preview anchor code',
          toolbar: 'undo redo | styles | bold italic underline forecolor backcolor | ' +
                   'alignleft aligncenter alignright | bullist numlist outdent indent | ' +
                   'fontfamily fontsize | code preview',
          menubar: false,
          height: 400,
          content_style: "body { font-family: 'Georgia', serif; font-size: 12pt; }"
        });
      }
    });
  </script>

  <style>
    .article-meta {
      color: #333;
      font-size: 0.95rem;
      margin-bottom: 1rem;
    }
    .article-meta strong {
      font-weight: 600;
    }
    .article-content {
      border-top: 1px solid #ccc;
      padding-top: 1.5rem;
      background: #fff;
      padding: 1.5rem;
      border-radius: 8px;
    }
    .edit-section {
      margin-top: 2rem;
      padding: 1rem 1.5rem;
      background: #fffbe6;
      border-radius: 8px;
      border: 1px solid #ffe58f;
    }
  </style>
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
          View Article
        </a>

        <a href="addArticle.html"
           class="header-item top-link text-decoration-none text-center">
          Write Article
        </a>

        <a href="aboutwebsite.html"
           class="header-item top-link text-decoration-none text-center">
          About the Website
        </a>
      </div>
    </div>
  </header>

  <!-- ======= MAIN CONTENT ======= -->
  <main class="page-content container mt-4 mb-5">

    <!-- Back button (blue, like Submit) -->
    <div class="mb-3">
      <a href="articlesList.php" class="btn btn-primary">
        &larr; Back to Articles
      </a>
    </div>

    <?php if ($article): ?>
      <h1 class="mb-3">
        <?php echo htmlspecialchars($article['title']); ?>
      </h1>

      <div class="article-meta">
        <div><strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?></div>
        <div><strong>Created:</strong> <?php echo htmlspecialchars($article['created_at']); ?></div>
        <div><strong>Last Edited:</strong> <?php echo htmlspecialchars($article['updated_at']); ?></div>
        <?php if (!empty($editorsList)): ?>
          <div><strong>Editors:</strong> <?php echo htmlspecialchars(implode(', ', $editorsList)); ?></div>
        <?php endif; ?>
      </div>

      <div class="article-content mb-4">
        <?php echo $article['articleContent']; ?>
      </div>

      <!-- Edit button + collapsible edit form -->
      <div class="mt-4">
        <!-- Now blue to match the "Submit" style -->
        <button class="btn btn-primary" type="button" id="toggleEditBtn">
          Edit Article
        </button>
      </div>

      <div id="editSection" class="edit-section mt-3" style="display: none;">
        <h2 class="h5 mb-3">Submit an Edit</h2>
        <form method="POST" action="viewArticle.php?id=<?php echo $id; ?>">
          <!-- Edit title -->
          <div class="mb-3">
            <label for="title" class="form-label"><strong>New Title</strong></label>
            <input type="text"
                   id="title"
                   name="title"
                   class="form-control"
                   value="<?php echo htmlspecialchars($article['title']); ?>"
                   required>
          </div>

          <!-- Editor selection with smart UX + autocomplete -->
          <div class="mb-3">
            <label class="form-label"><strong>Your Name (as Editor)</strong></label>

            <?php if (empty($editorsList)): ?>
              <div class="form-text mb-2">
                Enter your name as the editor. Leave empty if you are the original author.
              </div>
              <input type="text"
                     name="editor_name"
                     class="form-control"
                     placeholder="Your name (optional)">
            <?php else: ?>
              <div class="form-text mb-2">
                Enter your name as the editor. Leave empty if you are the original author.
                Otherwise, use the dropdown if you have edited this article before.
              </div>
              <input type="text"
                     name="editor_name"
                     class="form-control"
                     list="editors_datalist"
                     placeholder="Start typing your name or pick from the list">
              <datalist id="editors_datalist">
                <?php foreach ($editorsList as $editor): ?>
                  <option value="<?php echo htmlspecialchars($editor, ENT_QUOTES, 'UTF-8'); ?>"></option>
                <?php endforeach; ?>
              </datalist>
            <?php endif; ?>
          </div>

          <!-- Article Content (TinyMCE editor) -->
          <div class="mb-3">
            <label for="articleContentEdit" class="form-label"><strong>Edit Article Content</strong></label>
            <textarea id="articleContentEdit"
                      name="articleContent"
                      placeholder="Edit the article content here..."><?php
                echo $article['articleContent'] ?? '';
            ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary">
            Submit Edits
          </button>
        </form>
      </div>

    <?php else: ?>
      <h2>Article not found.</h2>
    <?php endif; ?>

  </main>

  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous">
  </script>

  <script>
    // Simple toggle for the edit form
    const toggleBtn   = document.getElementById('toggleEditBtn');
    const editSection = document.getElementById('editSection');

    if (toggleBtn && editSection) {
      toggleBtn.addEventListener('click', () => {
        if (editSection.style.display === 'none') {
          editSection.style.display = 'block';
        } else {
          editSection.style.display = 'none';
        }
      });
    }
  </script>

</body>
</html>
