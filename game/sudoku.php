<?php

session_start();

class Sudoku {

    public function generateFullBoard() {
        $board = array_fill(0, 9, array_fill(0, 9, 0));
        $this->fillBoard($board);
        return $board;
    }

    private function fillBoard(&$board) {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {

                if ($board[$row][$col] == 0) {
                    $numbers = range(1, 9);
                    shuffle($numbers);

                    foreach ($numbers as $num) {
                        if ($this->isValid($board, $row, $col, $num)) {
                            $board[$row][$col] = $num;

                            if ($this->fillBoard($board)) return true;

                            $board[$row][$col] = 0;
                        }
                    }
                    return false;
                }
            }
        }
        return true;
    }

    public function generatePuzzle($difficulty = "medium") {
        $board = $this->generateFullBoard();

        $cells = [
            "easy" => 35,
            "medium" => 45,
            "hard" => 55
        ];
        $remove = $cells[$difficulty] ?? 45;

        while ($remove > 0) {
            $r = rand(0, 8);
            $c = rand(0, 8);

            if ($board[$r][$c] !== 0) {
                $board[$r][$c] = 0;
                $remove--;
            }
        }

        return $board;
    }

    private function isValid($board, $row, $col, $num) {
        for ($i = 0; $i < 9; $i++) {
            if ($board[$row][$i] == $num || $board[$i][$col] == $num)
                return false;
        }

        $sr = $row - ($row % 3);
        $sc = $col - ($col % 3);

        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($board[$sr + $i][$sc + $j] == $num) return false;
            }
        }

        return true;
    }

    public function validateSolution($board) {

        for ($r = 0; $r < 9; $r++) {
            for ($c = 0; $c < 9; $c++) {
                if ($board[$r][$c] < 1 || $board[$r][$c] > 9) {
                    return "empty";
                }
            }
        }

        for ($r = 0; $r < 9; $r++) {
            if (count(array_unique($board[$r])) != 9)
                return "invalid";
        }

        for ($c = 0; $c < 9; $c++) {
            $col = [];
            for ($r = 0; $r < 9; $r++)
                $col[] = $board[$r][$c];

            if (count(array_unique($col)) != 9)
                return "invalid";
        }

        for ($sr = 0; $sr < 9; $sr += 3) {
            for ($sc = 0; $sc < 9; $sc += 3) {

                $block = [];
                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 3; $j++) {
                        $block[] = $board[$sr + $i][$sc + $j];
                    }
                }

                if (count(array_unique($block)) != 9)
                    return "invalid";
            }
        }

        return "ok";
    }
}

$sudoku = new Sudoku();

if (!isset($_SESSION['puzzle']) || isset($_POST['nuevo'])) {
    $_SESSION['puzzle'] = $sudoku->generatePuzzle("medium");
}

$puzzle = $_SESSION['puzzle'];
$message = "";

if (isset($_POST['verificar'])) {

    $userBoard = [];

    for ($r = 0; $r < 9; $r++) {
        for ($c = 0; $c < 9; $c++) {
            $val = $_POST["cell_{$r}_{$c}"] ?? "";
            $userBoard[$r][$c] = intval($val);
        }
    }

    $puzzle = $userBoard;

    $result = $sudoku->validateSolution($userBoard);

    if ($result === "ok") {
        $message = "<p style='color: green; font-size: 20px;'>¡Sudoku correcto!</p>";
    } elseif ($result === "empty") {
        $message = "<center><p style='color: orange; font-size: 20px;'>Hay celdas vacías.</p></center>";
    } else {
        $message = "<center><p style='color: red; font-size: 20px;'>La solución tiene errores.</p></center>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sudoku</title>
<link rel="stylesheet" href="../css/sudoku.css">
<link rel="stylesheet" href="../css/juego.css">
</head>
<body>

<header>
        <div class="title">🎮 JUEGO DEL SUDOKU</div>
        <a href="../index.php" class="btn">Regresar</a>
    </header>
<?= $message ?>
<center>
    <h2>Llene todos los cuadros</h2>
<form method="post">

<table>
<?php for ($r = 0; $r < 9; $r++): ?>
    <tr>
    <?php for ($c = 0; $c < 9; $c++): 
        $val = $puzzle[$r][$c];
        $fixed = ($_SESSION['puzzle'][$r][$c] !== 0);

        $classes = "";
        if (($c + 1) % 3 == 0 && $c != 8) $classes .= "subgrid-border ";
        if (($r + 1) % 3 == 0 && $r != 8) $classes .= "subgrid-border-bottom ";
    ?>
        <td class="<?= $classes ?>">
            <?php if ($fixed): ?>
                <input class="fixed" type="text" name="cell_<?= $r ?>_<?= $c ?>" value="<?= $val ?>" readonly>
            <?php else: ?>
                <input type="text" name="cell_<?= $r ?>_<?= $c ?>" maxlength="1"
                       value="<?= $val !== 0 ? $val : '' ?>"
                       oninput="this.value=this.value.replace(/[^1-9]/,'')">
            <?php endif; ?>
        </td>
    <?php endfor; ?>
    </tr>
<?php endfor; ?>
</table>

<button type="submit" name="verificar">Verificar</button>
<button type="submit" name="nuevo">Nuevo Sudoku</button>

</form>
            </center>
</body>
</html>
