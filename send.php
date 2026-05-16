<?php
declare(strict_types=1);

$RECIPIENT      = 'info@bftechnology.cz';
$SUBJECT_PREFIX = '[automatizovany-dum.cz] ';
$REFERER        = $_SERVER['HTTP_REFERER'] ?? '/';
$ORIGIN_PATH    = parse_url($REFERER, PHP_URL_PATH) ?: '/';
$THANKS_URL     = $ORIGIN_PATH . '?sent=1#kontakt';
$ERROR_URL      = $ORIGIN_PATH . '?sent=0#kontakt';

function pf($data, string $key, int $maxlen = 1000): string {
    if (!is_array($data)) return '';
    if (!isset($data[$key])) return '';
    $v = is_string($data[$key]) ? $data[$key] : '';
    $v = trim($v);
    if (strlen($v) > $maxlen) $v = substr($v, 0, $maxlen);
    return $v;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// Honeypots: support both new (top-level) and legacy WP form (nested array)
$hp1 = pf($_POST, 'website');
$hp2 = '';
$hp3 = '';
if (isset($_POST['kt-contact-form']) && is_array($_POST['kt-contact-form'])) {
    $hp2 = pf($_POST['kt-contact-form'], 'kt-contact-form-favourite');
    $hp3 = pf($_POST['kt-contact-form'], 'kt-honey');
}
if ($hp1 !== '' || $hp2 !== '' || $hp3 !== '') {
    header('Location: ' . $THANKS_URL);
    exit;
}

// Support both NEW form (top-level fields) and LEGACY WP form (nested)
$name = pf($_POST, 'name', 100);
$email = pf($_POST, 'email', 150);
$phone = pf($_POST, 'phone', 50);
$interest = pf($_POST, 'interest', 200);
$message = pf($_POST, 'message', 5000);

if (isset($_POST['kt-contact-form']) && is_array($_POST['kt-contact-form'])) {
    $form = $_POST['kt-contact-form'];
    if ($name === '')    $name    = pf($form, 'kt-contact-form-name', 100);
    if ($phone === '')   $phone   = pf($form, 'kt-contact-form-phone', 50);
    if ($email === '')   $email   = pf($form, 'kt-contact-form-email', 150);
    if ($message === '') $message = pf($form, 'kt-contact-form-message', 5000);
}

if ($name === '' || $email === '' || $phone === '') {
    header('Location: ' . $ERROR_URL);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $ERROR_URL);
    exit;
}

$subject = $SUBJECT_PREFIX . 'Poptávka od ' . $name;

$body  = "Nová poptávka z webu automatizovany-dum.cz\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "Jméno:     $name\n";
$body .= "Telefon:   $phone\n";
$body .= "E-mail:    $email\n";
if ($interest !== '') {
    $body .= "Zájem o:   $interest\n";
}
$body .= "\nZpráva:\n" . ($message !== '' ? $message : '(bez textu)') . "\n\n";
$body .= str_repeat('-', 50) . "\n";
$body .= "IP:        " . ($_SERVER['REMOTE_ADDR'] ?? 'n/a') . "\n";
$body .= "UA:        " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'n/a', 0, 200) . "\n";
$body .= "Referer:   " . $REFERER . "\n";
$body .= "Čas:       " . date('Y-m-d H:i:s') . "\n";

$replyEmail = preg_replace('/[\r\n]/', '', $email);

$headers  = "From: web@automatizovany-dum.cz\r\n";
$headers .= "Reply-To: " . $replyEmail . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$ok = @mail($RECIPIENT, $encodedSubject, $body, $headers);

header('Location: ' . ($ok ? $THANKS_URL : $ERROR_URL));
