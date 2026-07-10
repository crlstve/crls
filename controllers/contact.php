<?php
header('Allow: POST');
header('Content-Type: application/json; charset=UTF-8');

require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/recaptcha_helper.php');

use ReCaptcha\ReCaptcha;

const CONTACT_RATE_LIMIT_ATTEMPTS = 5;
const CONTACT_RATE_LIMIT_WINDOW = 600;

function contactJsonResponse(int $statusCode, string $status, string $message): void
{
    http_response_code($statusCode);
    echo json_encode([
        'status' => $status,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function contactEnvValue(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    return $default;
}

function contactRequestOriginAllowed(string $allowedOrigin): bool
{
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? trim((string) $_SERVER['HTTP_ORIGIN']) : '';
    $referer = isset($_SERVER['HTTP_REFERER']) ? trim((string) $_SERVER['HTTP_REFERER']) : '';

    if ($origin !== '') {
        return rtrim($origin, '/') === rtrim($allowedOrigin, '/');
    }

    if ($referer === '') {
        return false;
    }

    $allowedParts = parse_url($allowedOrigin);
    $refererParts = parse_url($referer);

    if (!is_array($allowedParts) || !is_array($refererParts)) {
        return false;
    }

    $allowedScheme = $allowedParts['scheme'] ?? '';
    $allowedHost = $allowedParts['host'] ?? '';
    $allowedPort = $allowedParts['port'] ?? null;

    return ($refererParts['scheme'] ?? '') === $allowedScheme
        && ($refererParts['host'] ?? '') === $allowedHost
        && (($refererParts['port'] ?? null) === $allowedPort);
}

function contactRateLimitExceeded(string $ipAddress): bool
{
    $rateLimitDirectory = sys_get_temp_dir() . '/portafolio-rate-limit';

    if (!is_dir($rateLimitDirectory) && !mkdir($rateLimitDirectory, 0700, true) && !is_dir($rateLimitDirectory)) {
        error_log('Could not create rate limit directory: ' . $rateLimitDirectory);
        return false;
    }

    $rateLimitFile = $rateLimitDirectory . '/' . hash('sha256', $ipAddress) . '.json';
    $currentTime = time();
    $windowStart = $currentTime - CONTACT_RATE_LIMIT_WINDOW;
    $attempts = [];

    if (is_file($rateLimitFile)) {
        $storedAttempts = json_decode((string) file_get_contents($rateLimitFile), true);

        if (is_array($storedAttempts)) {
            $attempts = array_values(array_filter($storedAttempts, static function ($attemptTime) use ($windowStart): bool {
                return is_int($attemptTime) && $attemptTime >= $windowStart;
            }));
        }
    }

    $attempts[] = $currentTime;

    if (file_put_contents($rateLimitFile, json_encode($attempts), LOCK_EX) === false) {
        error_log('Could not persist rate limit data: ' . $rateLimitFile);
    }

    return count($attempts) > CONTACT_RATE_LIMIT_ATTEMPTS;
}

function contactCleanText(string $value, int $maxLength): string
{
    $cleanValue = trim($value);
    $cleanValue = preg_replace('/\s+/u', ' ', $cleanValue) ?? '';

    return mb_substr($cleanValue, 0, $maxLength);
}

function contactSafeHtml(string $value, bool $preserveLineBreaks = false): string
{
    $escapedValue = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    return $preserveLineBreaks ? nl2br($escapedValue, false) : $escapedValue;
}

$allowedOrigin = contactEnvValue('APP_URL', 'https://carlesteve.dev');
$resendApiKey = contactEnvValue('RESEND_API_KEY');
$ipAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
$contentType = trim((string) ($_SERVER['CONTENT_TYPE'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contactJsonResponse(405, 'error', 'Método no permitido.');
}

if (stripos($contentType, 'application/json') !== 0) {
    contactJsonResponse(415, 'error', 'Tipo de contenido no soportado.');
}

if (!contactRequestOriginAllowed($allowedOrigin)) {
    contactJsonResponse(403, 'error', 'Solicitud no autorizada.');
}

if (contactRateLimitExceeded($ipAddress)) {
    contactJsonResponse(429, 'error', 'Has enviado demasiadas solicitudes. Inténtalo de nuevo más tarde.');
}

$json = file_get_contents('php://input');

try {
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    contactJsonResponse(400, 'error', 'Datos inválidos.');
}

if (!is_array($data)) {
    contactJsonResponse(400, 'error', 'Datos inválidos.');
}

$honeypot = trim((string) ($data['company'] ?? ''));

if ($honeypot !== '') {
    contactJsonResponse(202, 'success', 'Formulario enviado correctamente.');
}

$nombre = contactCleanText((string) ($data['nombre'] ?? ''), 80);
$email = trim((string) ($data['email'] ?? ''));
$phone = contactCleanText((string) ($data['phone'] ?? ''), 30);
$subject = contactCleanText((string) ($data['subject'] ?? ''), 120);
$message = trim((string) ($data['message'] ?? ''));
$recaptchaToken = trim((string) ($data['g-recaptcha-response'] ?? ''));

if ($nombre === '' || $email === '' || $phone === '' || $subject === '' || $message === '') {
    contactJsonResponse(422, 'error', 'Todos los campos son obligatorios.');
}

if (!preg_match('/^[\p{L}\s\-\'\.]+$/u', $nombre)) {
    contactJsonResponse(422, 'error', 'El nombre contiene caracteres no válidos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    contactJsonResponse(422, 'error', 'El email no es válido.');
}

if (!preg_match('/^[0-9+()\-\s]{6,30}$/', $phone)) {
    contactJsonResponse(422, 'error', 'El teléfono no es válido.');
}

if (mb_strlen($message) < 10 || mb_strlen($message) > 4000) {
    contactJsonResponse(422, 'error', 'El mensaje debe tener entre 10 y 4000 caracteres.');
}

if ($recaptchaToken === '') {
    contactJsonResponse(422, 'error', 'Por favor, completa la verificación reCAPTCHA.');
}

$recaptcha = new ReCaptcha(getRecaptchaSecretKey());
$recaptchaVerify = $recaptcha->verify($recaptchaToken, $ipAddress);

if (!$recaptchaVerify->isSuccess()) {
    error_log('reCAPTCHA verification failed: ' . implode(', ', $recaptchaVerify->getErrorCodes()));
    contactJsonResponse(422, 'error', 'La verificación reCAPTCHA falló. Por favor, inténtalo de nuevo.');
}

if ($resendApiKey === null) {
    error_log('RESEND_API_KEY is not configured.');
    contactJsonResponse(500, 'error', 'El servicio de contacto no está disponible en este momento.');
}

$emailBody = sprintf(
    '<p>de: <strong>%s</strong><br>email: <strong>%s</strong><br>tlf: <strong>%s</strong></p><p>%s</p>',
    contactSafeHtml($nombre),
    contactSafeHtml($email),
    contactSafeHtml($phone),
    contactSafeHtml($message, true)
);

try {
    $resend = Resend::client($resendApiKey);
    $resend->emails->send([
        'from' => 'info@carlesteve.dev',
        'to' => ['ecarles10@gmail.com'],
        'subject' => $subject,
        'reply_to' => $email,
        'html' => $emailBody,
    ]);
} catch (Throwable $throwable) {
    error_log('Contact form send failed: ' . $throwable->getMessage());
    contactJsonResponse(502, 'error', 'No se pudo enviar el formulario. Inténtalo de nuevo en unos minutos.');
}

contactJsonResponse(200, 'success', 'Formulario enviado correctamente.');
