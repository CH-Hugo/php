<?php
require_once __DIR__ . "/inc/page.inc.php";
require_once __DIR__ . "/inc/database.inc.php";

$artistId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($artistId <= 0) {
    header("Location: error.php?message=ID+d'artiste+invalide");
    exit;
}

// --- Connexion BD
$db = new DatabaseManager(
    dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
    username: 'lowify',
    password: 'lowifypassword'
);

// --- Récupération de l'artiste
$artistQuery = $db->executeQuery(
    "SELECT id, name, cover, monthly_listeners FROM artist WHERE id = :id",
    [':id' => $artistId]
);
if (empty($artistQuery)) {
    header("Location: error.php?message=Artiste+non+trouvé");
    exit;
}
$artist = $artistQuery[0];

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

// --- Top 5 chansons
$topSongsQuery = $db->executeQuery(
    "SELECT s.id, s.name, s.duration, s.note, a.cover AS album_cover
     FROM song s
     JOIN album a ON s.album_id = a.id
     WHERE s.artist_id = :id
     ORDER BY s.note DESC
     LIMIT 5",
    [':id' => $artistId]
);

// Préparer HTML top chansons
$topSongsHTML = "";
foreach ($topSongsQuery as $song) {
    $songName = htmlspecialchars($song['name'], ENT_QUOTES, 'UTF-8');
    $duration = formatDuration($song['duration']);
    $note = $song['note'];
    $albumCover = $song['album_cover'];

    $topSongsHTML .= <<<HTML
<div class="col-md-3">
    <div class="card">
        <img src="$albumCover" class="artist-cover" alt="$songName">
        <div>$songName</div>
        <div>Durée: $duration</div>
        <div>Note: $note</div>
    </div>
</div>
HTML;
}

// --- Albums de l'artiste
$albumsQuery = $db->executeQuery(
    "SELECT id, name, cover, YEAR(release_date) AS year
     FROM album
     WHERE artist_id = :id
     ORDER BY release_date DESC",
    [':id' => $artistId]
);

// Préparer HTML albums
$albumsHTML = "";
foreach ($albumsQuery as $album) {
    $albumName = htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8');
    $albumCover = $album['cover'];
    $albumYear = $album['year'];
    $albumId = intval($album['id']);

    $albumsHTML .= <<<HTML
<div class="col-md-3">
    <a href="album.php?id=$albumId">
        <div class="card">
            <img src="$albumCover" class="artist-cover" alt="$albumName">
            <div>$albumName ($albumYear)</div>
        </div>
    </a>
</div>
HTML;
}

// --- CSS
$css_inline = <<<CSS
<style>
body { background-color: #121212; color: white; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
.container { padding: 40px 20px; }
.artist-cover { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #1db954; object-fit: cover; display: block; margin: 0 auto; }
.card { background-color: #1e1e1e; border-radius: 15px; padding: 10px; margin: 10px; text-align: center; transition: transform 0.3s, box-shadow 0.3s; }
.card:hover { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
.row { display: flex; flex-wrap: wrap; margin: -10px; }
.col-md-3 { padding: 10px; flex: 1 0 21%; max-width: 25%; }
a { text-decoration: none; color: white; }
a:hover { color: #1db954; }
</style>
CSS;

// --- Contenu final
$monthlyListeners = formatListeners($artist['monthly_listeners']);
$artistName = htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8');
$artistCover = $artist['cover'];

$content = $css_inline . <<<HTML
<div class="container">
    <a href="artists.php"> < Retour aux artistes</a>
    <h1>$artistName</h1>
    <img src="$artistCover" class="artist-cover" alt="$artistName">
    <p>Auditeurs mensuels: $monthlyListeners</p>

    <h2>Top 5 chansons</h2>
    <div class="row">$topSongsHTML</div>

    <h2>Albums</h2>
    <div class="row">$albumsHTML</div>
</div>
HTML;

// --- Rendu de fin
$page = new HTMLPage("Lowify - $artistName");
$page->addContent($content);
echo $page->render();
