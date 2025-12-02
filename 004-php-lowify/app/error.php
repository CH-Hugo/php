<?php
require_once __DIR__ . "/inc/page.inc.php";

// --- Récupération message d'erreur
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') : "Oops ! Une erreur est survenue…";

// --- CSS
$css_inline = '<style>
body { background-color: #121212; color: white; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; text-align: center; padding: 60px 20px; }
h1 { color: #ff4c4c; font-size: 3em; animation: shake 0.5s infinite; }
@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
p { font-size: 1.2em; }
a { color: #1db954; text-decoration: none; font-weight: bold; transition: color 0.3s; }
a:hover { color: #1ed760; }
img { margin-top: 20px; max-width: 300px; border-radius: 15px; }
</style>';

// --- Contenu HTML avec mon super gif ehe !
$content = $css_inline;
$content .= '<div class="container">';
$content .= '<h1>😵 Oups !</h1>';
$content .= '<p>' . $message . '</p>';
$content .= '<p><a href="index.php">Retour à l\'accueil</a></p>';
$content .= '<img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExNHk0Y3d4YWkwYTdkdGRzaGM4ZDV1Njh0ZW9nY2IxZmU4YWttb243eCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/J08r3aXHt0BDATrfyT/giphy.gif" alt="Erreur amusante">';
$content .= '</div>';

// --- rendu de fin !
$page = new HTMLPage("Lowify - Erreur");
$page->addContent($content);
echo $page->render();
