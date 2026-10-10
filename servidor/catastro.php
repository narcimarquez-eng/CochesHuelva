<?php
/*
 * catastro.php — puente entre el Paseo por el relieve / Carreras por Huelva y el Catastro.
 *
 * El navegador no puede pedir los datos al Catastro directamente (sus servicios no dejan que otra web los lea),
 * así que esta página los pide en su nombre y se los devuelve tal cual.
 *
 *   ?z=25830&bbox=x0,y0,x1,y1              partes de edificio (INSPIRE BU.BuildingPart), con sus plantas
 *   ?z=25830&bbox=x0,y0,x1,y1&t=building   edificios (BU.Building)
 *   ?z=25830&bbox=x0,y0,x1,y1&t=parcela    parcelas catastrales (CP.CadastralParcel)     <- nuevo
 *   ?foto=REFERENCIA                        foto de la fachada (JPEG); 404 si no la hay
 *
 * z: sistema de coordenadas (25828 a 25831, ETRS89 UTM husos 28 a 31; 4082 y 4083 en Canarias).
 * bbox: en metros de ese sistema, como mucho 2 km de lado.
 * Los errores salen como <error>texto</error> con su código HTTP, para que la página diga qué ha pasado.
 * Lo que llega del Catastro se guarda un tiempo en una carpeta temporal del servidor: lo repetido no se vuelve a pedir.
 */

// Webs que pueden llamar a esta página desde otro sitio (la propia web no hace falta ponerla)
$ORIGENES = ['https://narcimarquez-eng.github.io', 'https://www.meteohuelva.es', 'https://meteohuelva.es'];
$CATASTRO = getenv('CATASTRO_BASE') ?: 'https://ovc.catastro.meh.es';   // se puede cambiar para hacer pruebas
$CACHE_DIR = sys_get_temp_dir() . '/catastro-cache';
$CACHE_DIAS = ['wfs' => 7, 'foto' => 30];

$origen = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origen, $ORIGENES, true)) {
  header('Access-Control-Allow-Origin: ' . $origen);
  header('Vary: Origin');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Max-Age: 86400');
  http_response_code(204);
  exit;
}

function error_xml($codigo, $texto, $cache = true) {
  http_response_code($codigo);
  header('Content-Type: application/xml; charset=utf-8');
  if ($cache && $codigo < 500) header('Cache-Control: public, max-age=86400');
  else header('Cache-Control: no-store');
  echo '<?xml version="1.0" encoding="utf-8"?><error>' . htmlspecialchars($texto, ENT_XML1, 'UTF-8') . '</error>';
  exit;
}

// Pide una URL al Catastro: [código HTTP, tipo, cuerpo] o null si no contesta
function pedir($url) {
  if (function_exists('curl_init')) {
    $c = curl_init($url);
    curl_setopt_array($c, [
      CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3,
      CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 40, CURLOPT_ENCODING => '',
      CURLOPT_USERAGENT => 'Paseo-por-el-relieve/1.0 (+https://www.meteohuelva.es)',
    ]);
    $cuerpo = curl_exec($c);
    if ($cuerpo === false) { $GLOBALS['ultimo_error'] = curl_error($c); curl_close($c); return null; }
    $r = [curl_getinfo($c, CURLINFO_RESPONSE_CODE), (string)curl_getinfo($c, CURLINFO_CONTENT_TYPE), $cuerpo];
    curl_close($c);
    return $r;
  }
  $ctx = stream_context_create(['http' => ['timeout' => 40, 'ignore_errors' => true, 'header' => "User-Agent: Paseo-por-el-relieve/1.0\r\n"]]);
  $cuerpo = @file_get_contents($url, false, $ctx);
  if ($cuerpo === false) { $GLOBALS['ultimo_error'] = 'sin respuesta'; return null; }
  $codigo = 200; $tipo = '';
  foreach ($http_response_header ?? [] as $h) {
    if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) $codigo = (int)$m[1];
    if (stripos($h, 'Content-Type:') === 0) $tipo = trim(substr($h, 13));
  }
  return [$codigo, $tipo, $cuerpo];
}

// Lo ya pedido se guarda unos días en el servidor
function de_cache($clave, $dias) {
  global $CACHE_DIR;
  $f = $CACHE_DIR . '/' . $clave;
  if (is_file($f) && filemtime($f) > time() - $dias * 86400) return file_get_contents($f);
  return null;
}
function a_cache($clave, $datos) {
  global $CACHE_DIR;
  if (!is_dir($CACHE_DIR)) @mkdir($CACHE_DIR, 0775, true);
  if (is_dir($CACHE_DIR) && is_writable($CACHE_DIR)) @file_put_contents($CACHE_DIR . '/' . $clave, $datos, LOCK_EX);
}

// --- foto de la fachada ---
if (isset($_GET['foto'])) {
  $ref = strtoupper(trim($_GET['foto']));
  if (!preg_match('/^[0-9A-Z]{14}([0-9A-Z]{6})?$/', $ref)) error_xml(400, 'referencia catastral no válida');
  $clave = 'foto-' . $ref;
  if (de_cache($clave . '.sin', $CACHE_DIAS['foto']) !== null) error_xml(404, 'sin foto');   // ya se miró: no tiene
  $img = de_cache($clave, $CACHE_DIAS['foto']);
  if ($img === null) {
    $r = pedir($CATASTRO . '/OVCServWeb/OVCWcfLibres/OVCFotoFachada.svc/RecuperarFotoFachadaGet?ReferenciaCatastral=' . urlencode(substr($ref, 0, 14)));
    if (!$r) error_xml(502, 'el Catastro no responde (' . ($GLOBALS['ultimo_error'] ?? 'sin respuesta') . ')', false);
    [$codigo, $tipo, $cuerpo] = $r;
    $esImagen = strlen($cuerpo) > 1500 && (substr($cuerpo, 0, 3) === "\xFF\xD8\xFF" || substr($cuerpo, 0, 8) === "\x89PNG\r\n\x1a\n");
    if ($codigo === 404 || ($codigo === 200 && !$esImagen)) {
      a_cache($clave . '.sin', '1');
      error_xml(404, 'sin foto');
    }
    if ($codigo !== 200) error_xml(502, "el Catastro contestó HTTP $codigo", false);
    $img = $cuerpo;
    a_cache($clave, $img);
  }
  header('Content-Type: ' . (substr($img, 0, 3) === "\xFF\xD8\xFF" ? 'image/jpeg' : 'image/png'));
  header('Cache-Control: public, max-age=2592000');
  echo $img;
  exit;
}

// --- edificios y parcelas por recuadro ---
$z = (int)($_GET['z'] ?? 0);
if (!in_array($z, [25828, 25829, 25830, 25831, 4082, 4083], true)) error_xml(400, 'huso no válido');
$bb = array_map('trim', explode(',', $_GET['bbox'] ?? ''));
if (count($bb) !== 4 || count(array_filter($bb, 'is_numeric')) !== 4) error_xml(400, 'bbox no válido');
[$x0, $y0, $x1, $y1] = array_map('floatval', $bb);
if ($x1 <= $x0 || $y1 <= $y0) error_xml(400, 'bbox no válido');
if ($x1 - $x0 > 2000 || $y1 - $y0 > 2000) error_xml(400, 'bbox demasiado grande (como mucho 2 km de lado)');

$t = strtolower($_GET['t'] ?? '');
if ($t === 'parcela' || $t === 'parcelas' || $t === 'cp') { $servicio = 'wfsCP'; $tipo = 'CP.CadastralParcel'; $t = 'parcela'; }
elseif ($t === 'building') { $servicio = 'wfsBU'; $tipo = 'BU.Building'; }
else { $servicio = 'wfsBU'; $tipo = 'BU.BuildingPart'; $t = 'parte'; }

$bbox = implode(',', array_map(function ($v) { return number_format($v, 1, '.', ''); }, [$x0, $y0, $x1, $y1]));
$clave = 'wfs-' . $t . '-' . $z . '-' . str_replace(',', '_', $bbox);
$xml = de_cache($clave, $CACHE_DIAS['wfs']);
if ($xml === null) {
  $url = $CATASTRO . "/INSPIRE/$servicio.aspx?service=WFS&version=2.0.0&request=GetFeature&typeNames=$tipo&srsName=EPSG::$z&bbox=$bbox";
  $r = pedir($url);
  if (!$r) error_xml(502, 'el Catastro no responde (' . ($GLOBALS['ultimo_error'] ?? 'sin respuesta') . ')', false);
  [$codigo, $tipoR, $cuerpo] = $r;
  if ($codigo !== 200) error_xml(502, "el Catastro contestó HTTP $codigo", false);
  if (trim($cuerpo) === '' || strpos($cuerpo, '<') === false) error_xml(502, 'el Catastro devolvió una respuesta vacía', false);
  $xml = $cuerpo;
  // «no hay nada en este recuadro» (el mar, el campo) no es un error: se devuelve una lista vacía
  if (strpos($xml, 'ExceptionReport') !== false && stripos($xml, 'No records') !== false)
    $xml = '<?xml version="1.0" encoding="utf-8"?><!--Sin ' . ($t === 'parcela' ? 'parcelas' : 'edificios') . ' en este recuadro.--><gml:FeatureCollection xmlns:gml="http://www.opengis.net/gml/3.2" numberMatched="0" numberReturned="0"/>';
  // los demás errores del Catastro (ExceptionReport) se devuelven tal cual, sin guardarlos
  if (strpos($xml, 'ExceptionReport') === false) a_cache($clave, $xml);
}
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');
echo $xml;
