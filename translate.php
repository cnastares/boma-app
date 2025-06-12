<?php
/**
 * Traductor masivo de mensajes Laravel (inglés → español)
 * Ejemplo:
 *   php translate.php base-lang/en/messages.php base-lang/es/messages.php
 */

$VERBOSE = false;

// extraer bandera -v si está presente
$args = $argv;
array_shift($args); // remove script name
foreach ($args as $i => $arg) {
    if ($arg === '-v') {
        $VERBOSE = true;
        unset($args[$i]);
    }
}
$args = array_values($args);
if (count($args) !== 2) {
    fwrite(STDERR, "Uso: php translate.php [-v] <origen> <destino>\n");
    exit(1);
}
[$in, $out] = $args;

if (!is_file($in)) {
    fwrite(STDERR, "No se encontró el archivo de entrada: $in\n");
    exit(1);
}

$src = include $in;
if (!is_array($src)) {
    fwrite(STDERR, "El archivo de entrada debe devolver un array PHP válido.\n");
    exit(1);
}

/* ---------- traducción recursiva --------- */
function translateArray(array $arr, &$count = 0): array
{
    $out = [];
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            $out[$k] = translateArray($v, $count);
        } else {
            $count++;
            echo "[$count] $k ...";
            $out[$k] = translate($v);
            echo " ok\n";
        }
    }
    return $out;
}

/* ---------- llamada sencilla a LibreTranslate --------- */
function translate(string $text): string
{
    global $VERBOSE;

    if (preg_match('/[áéíóúñäëïöü]/iu', $text)) return $text;

    $ch = curl_init('https://translate.argosopentech.com/translate'); // <-- endpoint alternativo
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_POSTFIELDS     => http_build_query([
            'q'      => $text,
            'source' => 'en',
            'target' => 'es',
            'format' => 'text'
        ]),
    ]);
    $json       = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errNo      = curl_errno($ch);
    $errMsg     = curl_error($ch);
    curl_close($ch);

    if ($VERBOSE) {
        if ($errNo || !$json || $httpStatus >= 400) {
            echo " [HTTP $httpStatus - $errMsg]";
        } else {
            echo " [HTTP $httpStatus]";
        }
    }

    if ($errNo || !$json || $httpStatus >= 400) {
        fwrite(STDERR, "Error al llamar a la API de traducción (HTTP $httpStatus): $errMsg\n");
        return $text;
    }

    $data = json_decode($json ?? '', true);
    return $data['translatedText'] ?? $text;
}

/* ---------- procesar y guardar --------- */
$total = 0;
$translated = translateArray($src, $total);
$export = "<?php\n\nreturn " . var_export($translated, true) . ";\n";

if (!is_dir(dirname($out))) {
    mkdir(dirname($out), 0777, true);
}
file_put_contents($out, $export);
echo "Traducción completada ($total cadenas). Archivo: $out\n";
