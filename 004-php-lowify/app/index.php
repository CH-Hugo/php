<?php
require_once __DIR__ . "/inc/page.inc.php";
require_once __DIR__ . "/inc/database.inc.php";

// --- Connexion BD (oui oui encore ce commentaire....)
$db = new DatabaseManager(
    dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
    username: 'lowify',
    password: 'lowifypassword'
);

function formatListeners($n) {
    if ($n >= 1000000) return round($n / 1000000, 1) . "M";
    if ($n >= 1000) return round($n / 1000, 1) . "k";
    return $n;
}

function formatDuration($seconds) {
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf("%02d:%02d", $min, $sec);
}

// --- Top 5 artistes
$topArtistsQuery = $db->executeQuery(
    "SELECT id, name, cover, monthly_listeners FROM artist ORDER BY monthly_listeners DESC LIMIT 5"
);
$topArtistsHTML = "";
foreach ($topArtistsQuery as $artist) {
    $topArtistsHTML .= '<div class="col-md-3">';
    $topArtistsHTML .= '<a href="artist.php?id=' . intval($artist['id']) . '">';
    $topArtistsHTML .= '<div class="card">';
    $topArtistsHTML .= '<img src="' . $artist['cover'] . '" class="artist-cover" alt="' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '">';
    $topArtistsHTML .= '<div>' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $topArtistsHTML .= '<div>Auditeurs: ' . formatListeners($artist['monthly_listeners']) . '</div>';
    $topArtistsHTML .= '</div></a></div>';
}

// --- Top 5 albums récents
$topAlbumsQuery = $db->executeQuery(
    "SELECT a.id, a.name, a.cover, YEAR(a.release_date) AS year, ar.name AS artist_name, ar.id AS artist_id
     FROM album a
     JOIN artist ar ON a.artist_id = ar.id
     ORDER BY a.release_date DESC
     LIMIT 5"
);
$topAlbumsHTML = "";
foreach ($topAlbumsQuery as $album) {
    $topAlbumsHTML .= '<div class="col-md-3">';
    $topAlbumsHTML .= '<a href="album.php?id=' . intval($album['id']) . '">';
    $topAlbumsHTML .= '<div class="card">';
    $topAlbumsHTML .= '<img src="' . $album['cover'] . '" class="artist-cover" alt="' . htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') . '">';
    $topAlbumsHTML .= '<div>' . htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') . ' (' . $album['year'] . ')</div>';
    $topAlbumsHTML .= '<div>Artiste: ' . htmlspecialchars($album['artist_name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $topAlbumsHTML .= '</div></a></div>';
}

// --- CSS
$css_inline = '<style>
body { background-color: #121212; color: white; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
.container { padding: 40px 20px; }
.artist-cover { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #1db954; object-fit: cover; display: block; margin: 0 auto; }
.card { background-color: #1e1e1e; border-radius: 15px; padding: 10px; margin: 10px; text-align: center; transition: transform 0.3s, box-shadow 0.3s; }
.card:hover { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
.row { display: flex; flex-wrap: wrap; margin: -10px; }
.col-md-3 { padding: 10px; flex: 1 0 21%; max-width: 25%; }
a { text-decoration: none; color: white; }
a:hover { color: #1db954; }
input[type=text] { padding: 10px; width: 70%; border-radius: 5px; border: none; margin-right: 10px; }
button { padding: 10px 20px; border-radius: 5px; border: none; background-color: #1db954; color: white; cursor: pointer; }
button:hover { background-color: #1ed760; }
</style>';

// --- Formulaire de recherche
$searchForm = '<form action="search.php" method="get">';
$searchForm .= '<input type="text" name="query" placeholder="Rechercher artistes, albums ou chansons">';
$searchForm .= '<button type="submit">Rechercher</button>';
$searchForm .= '</form>';

// --- Contenu final
$content = $css_inline;
$content .= '<div class="container">';
$content .= '<h1>Lowify</h1>';
$content .= $searchForm;
$content .= '<h2>Top artistes</h2><div class="row">' . $topArtistsHTML . '</div>';
$content .= '<h2>Top albums récents</h2><div class="row">' . $topAlbumsHTML . '</div>';
$content .= '</div>';

// --- Rendu de fin
$page = new HTMLPage("Lowify - Accueil");
$page->addContent($content);
echo $page->render();
