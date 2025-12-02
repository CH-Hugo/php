<?php
require_once __DIR__ . "/inc/page.inc.php";
require_once __DIR__ . "/inc/database.inc.php";

// --- Connexion à la base de données
$db = new DatabaseManager(
    dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
    username: 'lowify',
    password: 'lowifypassword'
);

// --- Récupération des artistes
$artists = $db->executeQuery("SELECT id, name, cover FROM artist");

// --- CSS
$css_inline = '<style>
body { background-color: #121212; color: white; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
.container { padding: 40px 20px; }
.artist-cover { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #1db954; object-fit: cover; margin: 0 auto; display: block; }
.card { background-color: #1e1e1e; border-radius: 15px; padding: 10px; margin: 10px; text-align: center; transition: transform 0.3s, box-shadow 0.3s; }
.card:hover { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
.row { display: flex; flex-wrap: wrap; margin: -10px; }
.col-md-3 { padding: 10px; flex: 1 0 21%; max-width: 25%; }
a { text-decoration: none; color: white; }
a:hover { color: #1db954; }
.link-home { display: inline-block; margin-bottom: 20px; color: #1db954; text-decoration: none; font-weight: bold; }
.link-home:hover { color: #1ed760; }
</style>';

// --- Génération des cartes
$artistsHTML = "";
foreach ($artists as $artist) {
    $artistsHTML .= '<div class="col-md-3">';
    $artistsHTML .= '<a href="artist.php?id=' . intval($artist['id']) . '">';
    $artistsHTML .= '<div class="card">';
    $artistsHTML .= '<img src="' . $artist['cover'] . '" class="artist-cover" alt="' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '">';
    $artistsHTML .= '<div>' . htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8') . '</div>';
    $artistsHTML .= '</div></a></div>';
}

// --- Contenu final
$html = $css_inline;
$html .= '<div class="container">';
$html .= '<a href="index.php" class="link-home">&larr; Retour à l\'accueil</a>';
$html .= '<h1>Artistes</h1>';
$html .= '<div class="row">' . $artistsHTML . '</div>';
$html .= '</div>';

// --- Rendu de fin !!
$page = new HTMLPage("Lowify - Artistes");
$page->addContent($html);
echo $page->render();

