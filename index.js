const SUPABASE_URL = "https://tqgxlrnnpdmbbwhrhnee.supabase.co";
const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRxZ3hscm5ucGRtYmJ3aHJobmVlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk0OTc2MDYsImV4cCI6MjA3NTA3MzYwNn0.pYvE_c43TkywbTLbrYR-SAxe5KGkle4w7-r95fWQ3Ws";

const supabaseClient = supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

async function createArticle() {
  const title = document.getElementById("title").value.trim();
  const content = document.getElementById("content").value.trim();

  if (!title || !content) {
    alert("Please enter both title and content.");
    return;
  }

  const { error } = await supabaseClient
    .from("articles")
    .insert([{ title, content }]);

  if (error) {
    alert("Error: " + error.message);
  } else {
    document.getElementById("title").value = "";
    document.getElementById("content").value = "";
    loadArticles();
  }
}

async function loadArticles() {
  const list = document.getElementById("articles-list");

  const { data, error } = await supabaseClient
    .from("articles")
    .select("id, title, created_at")
    .order("created_at", { ascending: false });

  list.innerHTML = "";

  if (error) {
    list.innerHTML = "Error loading articles.";
    return;
  }

  data.forEach(article => {
    const link = document.createElement("a");
    link.className = "article-link";
    link.innerText = article.title;
    link.href = `article.html?id=${article.id}`;
    list.appendChild(link);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("submit-btn").addEventListener("click", createArticle);
  loadArticles();
});
