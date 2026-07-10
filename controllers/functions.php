<?php
//evitar que se pueda acceder directamente al archivo
  if ($_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    header('Location: 404.php');
    exit;
  }

function appEnvValue($key, $default = null) {
  $value = getenv($key);

  if ($value !== false && $value !== '') {
    return $value;
  }

  if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
    return $_ENV[$key];
  }

  if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
    return $_SERVER[$key];
  }

  return $default;
}

function appIsDevelopment() {
  return in_array(strtolower((string) appEnvValue('APP_ENV', 'production')), ['local', 'development'], true);
}

function appIsSecureRequest() {
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    return true;
  }

  return (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function applySecurityHeaders() {
  if (PHP_SAPI === 'cli' || headers_sent()) {
    return;
  }

  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: DENY');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');
  header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; form-action 'self'; frame-ancestors 'none'; script-src 'self' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https://www.google.com https://www.gstatic.com; frame-src https://www.google.com https://www.gstatic.com https://www.googletagmanager.com; media-src 'self'; upgrade-insecure-requests");

  if (appIsSecureRequest()) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
  }
}

//mostrar errores
  ini_set('display_errors', appIsDevelopment() ? '1' : '0');
  ini_set('display_startup_errors', appIsDevelopment() ? '1' : '0');
  ini_set('log_errors', '1');
  error_reporting(E_ALL);
//cookies de sesión seguras
  ini_set('session.cookie_httponly', 1);
  ini_set('session.cookie_secure', 1);
  ini_set('session.use_only_cookies', 1);

  applySecurityHeaders();
// cargar partials y estilos
class Functions {
  public static function partial($partial){
    require_once('./views/partials/' . $partial . '.php');
  }
  public static function svg(){
    return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke-width=\"1\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"icon icon-tabler icons-tabler-outline  dark:stroke-dark stroke-white icon-tabler-arrow-narrow-right-dashed\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M5 12h.5m3 0h1.5m3 0h6\" /><path d=\"M15 16l4 -4\" /><path d=\"M15 8l4 4\" /></svg>";
  }

}
?>