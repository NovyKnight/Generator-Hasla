<?php
require 'GeneratorHasel.php';

$dsn = 'mysql:host=localhost;dbname=generator_hase';
$uzytkownik = 'root'; 
$hasloBazy = '';       

try {
    $pdo = new PDO($dsn, $uzytkownik, $hasloBazy, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Błąd połączenia: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dlugoscHasla = $_POST['dlugosc'] ?? 10;
    $minMaleLitery = $_POST['minMaleLitery'] ?? 3;
    $minDuzeLitery = $_POST['minDuzeLitery'] ?? 3;
    $minCyfry = $_POST['minCyfry'] ?? 2;
    $minZnakiSpecjalne = $_POST['minZnakiSpecjalne'] ?? 2;
    $uzywajMaleLitery = isset($_POST['uzywajMaleLitery']);
    $uzywajDuzeLitery = isset($_POST['uzywajDuzeLitery']);
    $uzywajCyfry = isset($_POST['uzywajCyfry']);
    $uzywajZnakiSpecjalne = isset($_POST['uzywajZnakiSpecjalne']);

    try {
        $generator = new GeneratorHasel($pdo, $dlugoscHasla, $minMaleLitery, $minDuzeLitery, $minCyfry, $minZnakiSpecjalne, $uzywajMaleLitery, $uzywajDuzeLitery, $uzywajCyfry, $uzywajZnakiSpecjalne);
        $wygenerowaneHaslo = $generator->generujHaslo();
        
        $generator->zapiszHaslo($wygenerowaneHaslo);

        $komunikat = "Wygenerowane hasło: " . $wygenerowaneHaslo;
    } catch (Exception $e) {
        $komunikat = "Błąd: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Haseł</title>
</head>
<body>
    <h1>Generator Haseł</h1>

    <form method="post">
        <label for="dlugosc">Długość hasła:</label>
        <input type="number" id="dlugosc" name="dlugosc" min="1" value="10" required><br><br>

        <label for="minMaleLitery">Min. małych liter:</label>
        <input type="number" id="minMaleLitery" name="minMaleLitery" min="0" value="3"><br><br>

        <label for="minDuzeLitery">Min. dużych liter:</label>
        <input type="number" id="minDuzeLitery" name="minDuzeLitery" min="0" value="3"><br><br>

        <label for="minCyfry">Min. cyfr:</label>
        <input type="number" id="minCyfry" name="minCyfry" min="0" value="2"><br><br>

        <label for="minZnakiSpecjalne">Min. znaków specjalnych:</label>
        <input type="number" id="minZnakiSpecjalne" name="minZnakiSpecjalne" min="0" value="2"><br><br>

        <label for="uzywajMaleLitery">Używaj małych liter:</label>
        <input type="checkbox" id="uzywajMaleLitery" name="uzywajMaleLitery" checked><br><br>

        <label for="uzywajDuzeLitery">Używaj dużych liter:</label>
        <input type="checkbox" id="uzywajDuzeLitery" name="uzywajDuzeLitery" checked><br><br>

        <label for="uzywajCyfry">Używaj cyfr:</label>
        <input type="checkbox" id="uzywajCyfry" name="uzywajCyfry" checked><br><br>

        <label for="uzywajZnakiSpecjalne">Używaj znaków specjalnych:</label>
        <input type="checkbox" id="uzywajZnakiSpecjalne" name="uzywajZnakiSpecjalne" checked><br><br>

        <button type="submit">Generuj</button>
    </form>

    <?php if (isset($komunikat)): ?>
        <p><?= $komunikat ?></p>
    <?php endif; ?>
</body>
</html>
