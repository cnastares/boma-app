<?php
/**
 * Traductor masivo de mensajes Laravel (inglés → español)
 * Ejemplo:
 *   php translate.php base-lang/en/messages.php base-lang/es/messages.php
 */

if ($argc !== 3) {
    fwrite(STDERR, "Uso: php translate.php <origen> <destino>\n");
    exit(1);
}
[$_, $in, $out] = $argv;

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
    $json = curl_exec($ch);
    curl_close($ch);

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
