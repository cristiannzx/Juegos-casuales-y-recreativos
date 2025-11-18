<?php
$palabras = ["ATAHUALPA", "AZTECA", "MESOPOTAMIA", "PREHISTORIA", "RENACIMIENTO", "ABSOLUTISMO", "INCAS", "AYLLU"];
$tamano = 15;

$tablero = array_fill(0, $tamano, array_fill(0, $tamano, ''));

function colocarPalabra(&$tablero, $palabra, $tamano) {
    $len = strlen($palabra);
    $direcciones = [
        [0, 1],
        [1, 0],
        [1, 1],
    ];

    while (true) {
        $dir = $direcciones[array_rand($direcciones)];
        $dx = $dir[0];
        $dy = $dir[1];

        $fila = rand(0, $tamano - 1);
        $col = rand(0, $tamano - 1);

        if ($fila + $dx * ($len - 1) >= $tamano) continue;
        if ($col + $dy * ($len - 1) >= $tamano) continue;

        $conflicto = false;
        for ($i = 0; $i < $len; $i++) {
            if ($tablero[$fila + $dx * $i][$col + $dy * $i] !== '' &&
                $tablero[$fila + $dx * $i][$col + $dy * $i] !== $palabra[$i]) {
                $conflicto = true;
                break;
            }
        }
        if ($conflicto) continue;

        for ($i = 0; $i < $len; $i++) {
            $tablero[$fila + $dx * $i][$col + $dy * $i] = $palabra[$i];
        }
        break;
    }
}

foreach ($palabras as $p) {
    colocarPalabra($tablero, $p, $tamano);
}

for ($i = 0; $i < $tamano; $i++) {
    for ($j = 0; $j < $tamano; $j++) {
        if ($tablero[$i][$j] == '') {
            $tablero[$i][$j] = chr(rand(65, 90)); 
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sopa de Letras</title>
<link rel="stylesheet" href="../css/letras.css">
<link rel="stylesheet" href="../css/juego.css">
</head>
<body>
    <header>
        <div class="title">🎮 JUEGO DE SOPA DE LETRAS</div>
        <a href="menu.php" class="btn">Regresar</a>
    </header>
<center>

<button onclick="location.reload()">Mezclar de nuevo</button>

<div id="ganaste" style="display:none; font-size:22px; color:green; font-weight:bold;">
¡Excelente encontraste todas las palabras! 
</div>
<table id="sopa">
<?php foreach ($tablero as $fila): ?>
<tr>
<?php foreach ($fila as $letra): ?>
    <td><?php echo $letra; ?></td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>

<p><strong>Palabras a encontrar:</strong> <?= implode(", ", $palabras) ?></p>

<script>
const palabras = <?php echo json_encode($palabras); ?>;
let encontradas = [];
let seleccionadas = [];

function contarLetras(str) {
    const mapa = {};
    str.split("").forEach(l => mapa[l] = (mapa[l] || 0) + 1);
    return mapa;
}

function iguales(a, b) {
    const ka = Object.keys(a), kb = Object.keys(b);
    if (ka.length !== kb.length) return false;
    for (let c in a) if (a[c] !== b[c]) return false;
    return true;
}

document.querySelectorAll("#sopa td").forEach(td => {
    td.addEventListener("click", () => {

        if (td.classList.contains("selected")) {
            td.classList.remove("selected");
            seleccionadas = seleccionadas.filter(c => c !== td);
            return;
        }

        td.classList.add("selected");
        seleccionadas.push(td);

        let letrasSeleccionadas = seleccionadas.map(c => c.textContent).join("");
        let mapaSeleccion = contarLetras(letrasSeleccionadas);

        palabras.forEach(p => {

            if (encontradas.includes(p)) return;

            let mapaPalabra = contarLetras(p);

            if (iguales(mapaSeleccion, mapaPalabra)) {

                seleccionadas.forEach(c => c.classList.add("found"));
                encontradas.push(p);

                seleccionadas = [];
                document.querySelectorAll(".selected").forEach(x => x.classList.remove("selected"));
            }
        });

        if (encontradas.length === palabras.length) {
            document.getElementById("ganaste").style.display = "block";
        }
    });
});
</script>
</center>
</body>
</html>
