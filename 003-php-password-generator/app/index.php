<?php

function generateSelectOptions($selected = 12): string
{
    $html = "";
    $options = range(8, 42);
    foreach ($options as $value) {
        $attribute = ((int)$value === (int)$selected) ? "selected" : "";
        $html .= "<option $attribute value=\"$value\">$value</option>";
    }
    return $html;
}

function generateRandomCharacter(string $characters): string
{
    return $characters[random_int(0, strlen($characters) - 1)];
}

function shuffleString(string $string): string
{
    $array = str_split($string);
    shuffle($array);
    return implode('', $array);
}

function generatePassword(int $length, int $useUpper, int $useLower, int $useDigits, int $useSymbols): string
{
    $password = '';
    $sequences = [];

    if ($useUpper) $sequences[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($useLower) $sequences[] = 'abcdefghijklmnopqrstuvwxyz';
    if ($useDigits) $sequences[] = '0123456789';
    if ($useSymbols) $sequences[] = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    if (empty($sequences)) return '';

    foreach ($sequences as $seq) {
        $password .= generateRandomCharacter($seq);
    }

    $remainingLength = $length - strlen($password);

    for ($i = 0; $i < $remainingLength; $i++) {
        $seq = $sequences[random_int(0, count($sequences) - 1)];
        $password .= generateRandomCharacter($seq);
    }

    return shuffleString($password);
}

$formMethod = $_SERVER['REQUEST_METHOD'];

if ($formMethod === 'POST') {
    $length = $_POST['length'] ?? 12;
    $useUpper = $_POST['uppercase'] ?? 0;
    $useLower = $_POST['lowercase'] ?? 0;
    $useDigits = $_POST['digits'] ?? 0;
    $useSymbols = $_POST['symbols'] ?? 0;
} else {
    $length = 12;
    $useUpper = 1;
    $useLower = 1;
    $useDigits = 1;
    $useSymbols = 0;
}

$password = '';
if ($formMethod === 'POST') {
    $password = generatePassword($length, $useUpper, $useLower, $useDigits, $useSymbols);
}

$isUpperChecked = $useUpper ? 'checked' : '';
$isLowerChecked = $useLower ? 'checked' : '';
$isDigitsChecked = $useDigits ? 'checked' : '';
$isSymbolsChecked = $useSymbols ? 'checked' : '';

// Générer les options pour le select
$selectOptions = generateSelectOptions($length);


echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur de mots de passe</title>
    <style>
    body {
    text-align: center;
    background-color: #053c60;
    color: white;
    font-family: 'Poppins', sans-serif;
    }
    
    h1 {
    margin-block: 70px;
    text-align: center;
    color: white;
    }
    
    .checkbox-group {
    display: flex;
    justify-content: center;
    gap: 20px; 
    flex-wrap: wrap;
}

.checkbox-group input[type="checkbox"] {
    margin: 0 5px 0 0;
}

    label:hover::before {
        border-color: green;
    }
    
.btn-generate {
    background: linear-gradient(135deg, #4CAF50, #2E8B57);
    color: white;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 16px;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    margin-top: 40px;
}

.btn-generate:hover {
    background: linear-gradient(135deg, #45a049, #276644);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3);
}

#password {
margin-top: 20px;
    width: 320px;
    padding: 12px 15px;
    font-size: 17px;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.12);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    color: white;
    text-align: center;
    transition: all 0.25s ease;
}

#password:hover {
    border-color: white;
}

</style>
</head>
<body>
    <h1>Besoin d'un mot de passe ?<br> Essayez mon générateur de mots de passe !</h1>
    <form method="post" action="">
        <label for="password">Mot de passe généré :</label><br>
        <input type="text" id="password" name="password" value="$password" readonly style="width:300px"><br><br>

        <label>Taille du mot de passe :</label>
        <select name="length">
            $selectOptions
        </select><br><br>
<div class="checkbox-group">
    <input type="checkbox" id="uppercase" name="uppercase" value="1" $isUpperChecked>
    <label for="uppercase">Majuscules</label>

    <input type="checkbox" id="lowercase" name="lowercase" value="1" $isLowerChecked>
    <label for="lowercase">Minuscules</label>

    <input type="checkbox" id="digits" name="digits" value="1" $isDigitsChecked>
    <label for="digits">Chiffres</label>

    <input type="checkbox" id="symbols" name="symbols" value="1" $isSymbolsChecked>
    <label for="symbols">Symboles</label>
</div>


        <button type="submit" class="btn-generate">Générer le mot de passe</button>
    </form>
</body>
</html>
HTML;