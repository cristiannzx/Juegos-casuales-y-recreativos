<?php

session_start();

if (!isset($_SESSION['tablero'])) {
    $_SESSION['tablero'] = array_fill(0, 9, "");
    $_SESSION['turno']  = "X";
    $_SESSION['modo']  = "compu";  
}

if (isset($_GET['reset'])) {
    $_SESSION['tablero'] = array_fill(0, 9, "");
    $_SESSION['turno']  = "X";
    header("Location: michi.php");
    exit;
}


if (isset($_GET['modo'])) {
    $newModo = ($_GET['modo'] === "2p") ? "2p" : "compu";
    $_SESSION['modo'] = $newModo;

    $_SESSION['tablero'] = array_fill(0, 9, "");
    $_SESSION['turno']  = "X";

    header("Location: michi.php");
    exit;
}

$tablero = &$_SESSION['tablero'];
$turno  = &$_SESSION['turno'];
$modo  = $_SESSION['modo'];


function checkWinner($b) {
    $win = [
        [0,1,2],[3,4,5],[6,7,8],
        [0,3,6],[1,4,7],[2,5,8],
        [0,4,8],[2,4,6]
    ];
    foreach ($win as $w) {
        if ($b[$w[0]] !== "" &&
            $b[$w[0]] === $b[$w[1]] &&
            $b[$w[1]] === $b[$w[2]]) {
            return $b[$w[0]];
        }
    }
    return "";
}


function isFull($b) {
    return !in_array("", $b);
}

function minimax($b, $player) {
    $ganador = checkWinner($b);
    if ($ganador === "O") return ["score" => 10];
    if ($ganador === "X") return ["score" => -10];
    if (isFull($b)) return ["score" => 0];

    $moves = [];

    for ($i = 0; $i < 9; $i++) {
        if ($b[$i] === "") {
            $move = ["index" => $i];
            $b[$i] = $player;

          
            if ($player === "O") {
                $result = minimax($b, "X");
                $move["score"] = $result["score"];
            } 
      
            else {
                $result = minimax($b, "O");
                $move["score"] = $result["score"];
            }

            $b[$i] = "";
            $moves[] = $move;
        }
    }


    if ($player === "O") {
        $best = ["score" => -INF];
        foreach ($moves as $m) {
            if ($m["score"] > $best["score"]) $best = $m;
        }
        return $best;
    }

    else {
        $best = ["score" => INF];
        foreach ($moves as $m) {
            if ($m["score"] < $best["score"]) $best = $m;
        }
        return $best;
    }
}


function cpuPlay() {
    global $tablero;
    $best = minimax($tablero, "O");
    $tablero[$best["index"]] = "O";
}


$ganador = checkWinner($tablero);

if (isset($_GET['pos']) && !$ganador && !isFull($tablero)) {

    $p = intval($_GET['pos']);

    if ($tablero[$p] === "") {
        $tablero[$p] = $turno;
        $turno = ($turno === "X") ? "O" : "X";
    }

    $ganador = checkWinner($tablero);


    if ($modo === "compu" && !$ganador && $turno === "O") {
        cpuPlay();
        $turno = "X";
    }

    header("Location: michi.php");
    exit;
}

$ganador = checkWinner($tablero);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Michi inteligente</title>
<link rel="stylesheet" href="../css/mich.css">
<link rel="stylesheet" href="../css/juego.css">
</head>
<body>

<header>
        <div class="title">🎮 JUEGO TRES EN RAYA</div>
        <a href="../index.php" class="btn">Regresar</a>
    </header>

    <h3>Modo actual: 
    <?php echo ($modo === "compu") ? "Jugador VS Computadora" : "2 Jugadores"; ?>
    </h3>


<div style="margin-bottom: 15px;">
    <a href="michi.php?modo=compu"><button>Jugador vs Computadora</button></a>
    <a href="michi.php?modo=2p"><button>2 Jugadores</button></a>
    <a href="michi.php?reset=1"><button>Reiniciar</button></a>
</div>

<h2>
<?php
    if ($ganador) echo "Ganador: $ganador 🎉";
    else if (isFull($tablero)) echo "Empate 🤝";
    else echo "Turno: $turno";
?>
</h2>
<div class="board">
    <?php foreach ($tablero as $i => $cell): ?>

        <?php
         
            $active = (!$ganador && !$cell && !isFull($tablero));
            $href = $active ? "michi.php?pos=$i" : "#";
        ?>

        <a href="<?php echo $href; ?>" 
           class="cell"
           style="pointer-events: <?php echo $active ? 'auto' : 'none'; ?>;">

            <?php 
                if ($cell === "X") echo "<span class='x'>X</span>";
                if ($cell === "O") echo "<span class='o'>O</span>";
            ?>
        </a>

    <?php endforeach; ?>
</div>

</body>
</html>
