<?php
session_start();

if (!isset($_SESSION["iniciado"]) || isset($_POST["reiniciar"])) {

    $palabras = ["guerra", "incas", "imperio", "conquistador", "esclavo", "reynos"];
    $palabra = $palabras[array_rand($palabras)];

    $_SESSION["palabra"] = $palabra;              
    $_SESSION["progreso"] = str_repeat("_", strlen($palabra));
    $_SESSION["intentos"] = 0;
    $_SESSION["letras_usadas"] = [];

    $_SESSION["iniciado"] = true;               

}


if (isset($_POST["letra"])) {

    $letra = strtolower(trim($_POST["letra"]));

    if ($letra !== "" && preg_match("/^[a-zñ]$/", $letra)) {

        if (!in_array($letra, $_SESSION["letras_usadas"])) {

            $_SESSION["letras_usadas"][] = $letra;

            $palabra = $_SESSION["palabra"];
            $progreso = str_split($_SESSION["progreso"]);
            $encontro = false;

            for ($i = 0; $i < strlen($palabra); $i++) {
                if ($palabra[$i] === $letra) {
                    $progreso[$i] = $letra;
                    $encontro = true;
                }
            }

            $_SESSION["progreso"] = implode("", $progreso);

            if (!$encontro) {
                $_SESSION["intentos"]++;
            }
        }
    }
}

function dibujo($i) {

    $d = [
"
  +-----+
  |     |
        |
        |
        |
===========
",
"
  +-----+
  |     |
  O     |
        |
        |
===========
",
"
  +-----+
  |     |
  O     |
  |     |
        |
===========
",
"
  +-----+
  |     |
  O     |
 /|     |
        |
===========
",
"
  +-----+
  |     |
  O     |
 /|\\    |
        |
===========
",
"
  +-----+
  |     |
  O     |
 /|\\    |
 /      |
===========
",
"
  +-----+
  |     |
  O     |
 /|\\    |
 / \\    |
===========
"
    ];

    return $d[$i];
}

$intentos = $_SESSION["intentos"];
$ganaste  = ($_SESSION["progreso"] === $_SESSION["palabra"]);
$perdiste = ($intentos >= 6);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Colección de Juegos</title>
    <link rel="stylesheet" href="../css/juego.css">
    <link rel="stylesheet" href="../css/hanged.css">
</head>
<body>
    <header>
        <div class="title">🎮 JUEGO DEL HANGED</div>
        <a href="menu.php" class="btn">Regresar</a>
    </header>
    <center>
<h1>Historia</h1>

<pre><?php echo dibujo($intentos); ?></pre>

<p class="progreso"><?php echo $_SESSION["progreso"]; ?></p>

<p>Letras usadas: 
<?php 
if (!empty($_SESSION["letras_usadas"])) {
    echo implode(", ", $_SESSION["letras_usadas"]);
} else {
    echo "Ninguna";
}
?>
</p>

<?php if ($ganaste): ?>
    <h2>GANASTE La palabra era: <?php echo $_SESSION["palabra"]; ?></h2>

<?php elseif ($perdiste): ?>
    <h3>PERDISTE la palabra era: <?php echo $_SESSION["palabra"]; ?></h3>
<?php else: ?>

<form method="post">
    <input type="text" name="letra" maxlength="1" required>
    <button type="submit">Enviar</button>
</form>

<?php endif; ?>

<form method="post">
    <button name="reiniciar">Reiniciar Juego</button>
</form>
</center>
</body>
</html>