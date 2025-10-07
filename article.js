const SUPABASE_URL = "https://tqgxlrnnpdmbbwhrhnee.supabase.co";
const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRxZ3hscm5ucGRtYmJ3aHJobmVlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk0OTc2MDYsImV4cCI6MjA3NTA3MzYwNn0.pYvE_c43TkywbTLbrYR-SAxe5KGkle4w7-r95fWQ3Ws";
const supabaseClient = supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
console.log("article.js loaded");

function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

async function viewArticle() {
  const view = document.getElementById("article-view");
  const id = getQueryParam("id");

  if (!id) {
    view.innerHTML = "No article ID provided.";
    return;
  }

  const { data, error } = await supabaseClient
    .from("articles")
    .select("*")
    .eq("id", id)
    .single();

  if (error) {
    view.innerHTML = "Error loading article.";
    return;
  }

  view.innerHTML = `
    <div class="article">
      <h1>${data.title}</h1>
      <p>${data.content.replace(/\n/g, "<br>")}</p>
      <small>Created at: ${new Date(data.created_at).toLocaleString()}</small>
    </div>
  `;
}

document.addEventListener("DOMContentLoaded", viewArticle);
