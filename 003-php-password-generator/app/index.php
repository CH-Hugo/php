<?php

$animals = "𓅓𓆣𓅂𓆇𓆉";
$gods    = "𓂀𓁹𓁛𓊹𓊽";
$plants  = "𓇋𓇌𓇏𓇐𓇑";
$objects = "𓏏𓏐𓏠𓏤𓐍";
$symbols = "𓀀𓀁𓀃𓁶𓂓";

$password = "";
$size = $_POST['size'] ?? 12;

$useAnimals = isset($_POST['use-animals']) ? 1 : 0;
$useGods    = isset($_POST['use-gods']) ? 1 : 0;
$usePlants  = isset($_POST['use-plants']) ? 1 : 0;
$useObjects = isset($_POST['use-objects']) ? 1 : 0;
$useSymbols = isset($_POST['use-symbols']) ? 1 : 0;

$pool = "";
if ($useAnimals) $pool .= $animals;
if ($useGods)    $pool .= $gods;
if ($usePlants)  $pool .= $plants;
if ($useObjects) $pool .= $objects;
if ($useSymbols) $pool .= $symbols;

// Générer le mot de passe
if ($pool != "") {
    $poolLength = strlen($pool);
    $password = "";
    for ($i = 0; $i < $size; $i++) {
        $index = rand(0, $poolLength - 1);
        $password .= $pool[$index];
    }
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = "⚠️ Veuillez sélectionner au moins une catégorie !";
}

// Préparer les checked
$animalsChecked = $useAnimals ? "checked" : "";
$godsChecked    = $useGods ? "checked" : "";
$plantsChecked  = $usePlants ? "checked" : "";
$objectsChecked = $useObjects ? "checked" : "";
$symbolsChecked = $useSymbols ? "checked" : "";

// Générer les options du select (8 → 42)
$sizeOptions = "";
for ($i = 8; $i <= 42; $i++) {
    $selected = ($i == $size) ? "selected" : "";
    $sizeOptions .= "<option value=\"$i\" $selected>$i</option>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur de mots de passe Hiéroglyphiques</title>
</head>
<body>

<h1>Générateur de mot de passe Hiéroglyphique</h1>

<p><strong>Mot de passe :</strong> <?php echo $password; ?></p>

<form method="POST" action="index.php">
    <div>
        <label for="size">Taille</label>
        <select name="size" id="size">
            <?php echo $sizeOptions; ?>
        </select>
    </div>

    <div>
        <input type="checkbox" id="use-animals" name="use-animals" value="1" <?php echo $animalsChecked; ?>>
        <label for="use-animals">Animaux</label>
    </div>

    <div>
        <input type="checkbox" id="use-gods" name="use-gods" value="1" <?php echo $godsChecked; ?>>
        <label for="use-gods">Dieux</label>
    </div>

    <div>
        <input type="checkbox" id="use-plants" name="use-plants" value="1" <?php echo $plantsChecked; ?>>
        <label for="use-plants">Plantes</label>
    </div>

    <div>
        <input type="checkbox" id="use-objects" name="use-objects" value="1" <?php echo $objectsChecked; ?>>
        <label for="use-objects">Objets</label>
    </div>

    <div>
        <input type="checkbox" id="use-symbols" name="use-symbols" value="1" <?php echo $symbolsChecked; ?>>
        <label for="use-symbols">Symboles</label>
    </div>

    <div>
        <button type="submit">Générer !</button>
    </div>
</form>

</body>
</html>
