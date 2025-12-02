<?php
require_once __DIR__ . "/inc/page.inc.php";
require_once __DIR__ . "/inc/database.inc.php";

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($query === '') {
    header("Location: index.php");
    exit;
}

// --- Connexion BD
$db = new DatabaseManager(
    dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
    username: 'lowify',
    password: 'lowifypassword'
);

// --- Fonctions utilitaires
function formatDuration($seconds) {
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf("%02d:%02d", $min, $sec);
}

// --- Recherche artistes
$artistsQuery = $db->executeQuery(
    "SELECT id, name, cover FROM artist 
     WHERE name LIKE :search",
    [':search' => '%' . $query . '%']
);
$artistsHTML = "";
foreach ($artistsQuery as $artist) {
    $artistsHTML .= '<div class="col-md-3">';
    $artistsHTML .= '<a href="artist.php?id=' . intval($artist['id']) . '">';
    $artistsHTML .= '<div class="card">';
    $artistsHTML .= '<img src="' . $artist['cover'] . '" class="artist-cover" alt="' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '">';
    $artistsHTML .= '<div>' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $artistsHTML .= '</div></a></div>';
}

// --- Recherche albums
$albumsQuery = $db->executeQuery(
    "SELECT a.id, a.name, a.cover, YEAR(a.release_date) AS year, ar.name AS artist_name, ar.id AS artist_id
     FROM album a
     JOIN artist ar ON a.artist_id = ar.id
     WHERE a.name LIKE :search",
    [':search' => '%' . $query . '%']
);
$albumsHTML = "";
foreach ($albumsQuery as $album) {
    $albumsHTML .= '<div class="col-md-3">';
    $albumsHTML .= '<a href="album.php?id=' . intval($album['id']) . '">';
    $albumsHTML .= '<div class="card">';
    $albumsHTML .= '<img src="' . $album['cover'] . '" class="artist-cover" alt="' . htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') . '">';
    $albumsHTML .= '<div>' . htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') . ' (' . $album['year'] . ')</div>';
    $albumsHTML .= '<div>Artiste: ' . htmlspecialchars($album['artist_name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $albumsHTML .= '</div></a></div>';
}

// --- Recherche chansons
$songsQuery = $db->executeQuery(
    "SELECT s.id, s.name, s.duration, s.note, a.name AS album_name, a.id AS album_id, ar.name AS artist_name
     FROM song s
     JOIN album a ON s.album_id = a.id
     JOIN artist ar ON s.artist_id = ar.id
     WHERE s.name LIKE :search",
    [':search' => '%' . $query . '%']
);
$songsHTML = "";
foreach ($songsQuery as $song) {
    $songsHTML .= '<div class="col-md-3">';
    $songsHTML .= '<div class="card">';
    $songsHTML .= '<div>' . htmlspecialchars($song['name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $songsHTML .= '<div>Durée: ' . formatDuration($song['duration']) . '</div>';
    $songsHTML .= '<div>Note: ' . $song['note'] . '</div>';
    $songsHTML .= '<div>Album: <a href="album.php?id=' . intval($song['album_id']) . '">' . htmlspecialchars($song['album_name'], ENT_QUOTES, 'UTF-8') . '</a></div>';
    $songsHTML .= '<div>Artiste: <a href="artist.php?id=' . intval($song['artist_id']) . '">' . htmlspecialchars($song['artist_name'], ENT_QUOTES, 'UTF-8') . '</a></div>';
    $songsHTML .= '</div></div>';
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
</style>';

// --- Contenu final
$content = $css_inline;
$content .= '<div class="container">';
$content .= '<h1>Résultats de recherche pour: ' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '</h1>';

if ($artistsHTML !== '') {
    $content .= '<h2>Artistes</h2><div class="row">' . $artistsHTML . '</div>';
}
if ($albumsHTML !== '') {
    $content .= '<h2>Albums</h2><div class="row">' . $albumsHTML . '</div>';
}
if ($songsHTML !== '') {
    $content .= '<h2>Chansons</h2><div class="row">' . $songsHTML . '</div>';
}

$content .= '</div>';

// --- Rendu de fin !!!!!!
$page = new HTMLPage("Lowify - Résultats recherche");
$page->addContent($content);
echo $page->render();
