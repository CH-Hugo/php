<?php
require_once __DIR__ . "/inc/page.inc.php";
require_once __DIR__ . "/inc/database.inc.php";

$albumId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($albumId <= 0) {
    header("Location: error.php?message=ID+d'album+invalide");
    exit;
}

// --- Connexion BD
$db = new DatabaseManager(
    dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
    username: 'lowify',
    password: 'lowifypassword'
);

// --- Récupération infos de l'album
$albumQuery = $db->executeQuery(
    "SELECT id, name, cover, release_date, artist_id FROM album WHERE id = :id",
    [':id' => $albumId]
);
if (empty($albumQuery)) {
    header("Location: error.php?message=Album+non+trouvé");
    exit;
}
$album = $albumQuery[0];

// --- Récupération de l'artiste
$artistQuery = $db->executeQuery(
    "SELECT id, name FROM artist WHERE id = :id",
    [':id' => $album['artist_id']]
);
if (empty($artistQuery)) {
    header("Location: error.php?message=Artiste+de+l'album+introuvable");
    exit;
}
$artist = $artistQuery[0];

function formatDuration($seconds) {
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf("%02d:%02d", $min, $sec);
}

// --- Récupération des chansons de l'album
$songsQuery = $db->executeQuery(
    "SELECT id, name, duration, note FROM song WHERE album_id = :id ORDER BY id ASC",
    [':id' => $albumId]
);

// --- prépa HTML des chansons
$songsHTML = "";
foreach ($songsQuery as $song) {
    $songName = htmlspecialchars($song['name'], ENT_QUOTES, 'UTF-8');
    $duration = formatDuration($song['duration']);
    $note = $song['note'];

    $songsHTML .= <<<HTML
<div class="col-md-3">
    <div class="card">
        <div>$songName</div>
        <div>Durée: $duration</div>
        <div>Note: $note</div>
    </div>
</div>
HTML;
}

// --- CSS
$css_inline = <<<CSS
<style>
body { background-color: #121212; color: white; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
.container { padding: 40px 20px; }
.album-cover { width: 150px; height: 150px; border-radius: 15px; object-fit: cover; display: block; margin: 0 auto; }
.card { background-color: #1e1e1e; border-radius: 15px; padding: 10px; margin: 10px; text-align: center; transition: transform 0.3s, box-shadow 0.3s; }
.card:hover { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
.row { display: flex; flex-wrap: wrap; margin: -10px; }
.col-md-3 { padding: 10px; flex: 1 0 21%; max-width: 25%; }
a { text-decoration: none; color: white; }
a:hover { color: #1db954; }
</style>
CSS;

// --- Contenu final youpiiii
$albumName = htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8');
$artistName = htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8');
$artistId = intval($artist['id']);
$albumCover = $album['cover'];
$releaseDate = htmlspecialchars($album['release_date'], ENT_QUOTES, 'UTF-8');

$content = $css_inline . <<<HTML
<div class="container">
    <a href="artist.php?id=$artistId"> < Retour à l'artiste</a>
    <h1>$albumName</h1>
    <a href="artist.php?id=$artistId"><h2>$artistName</h2></a>
    <img src="$albumCover" class="album-cover" alt="$albumName">
    <p>Date de sortie: $releaseDate</p>

    <h2>Chansons</h2>
    <div class="row">$songsHTML</div>
</div>
HTML;

// --- Rendu de fin
$page = new HTMLPage("Lowify - $albumName");
$page->addContent($content);
echo $page->render();
