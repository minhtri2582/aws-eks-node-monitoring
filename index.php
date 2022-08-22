<?php
error_reporting(E_ERROR | E_PARSE);
require "bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /person
// everything else results in a 404 Not Found

$secret = getenv("SECRET_URL");

if ($uri[1] !== $secret) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode([
        'result'    =>  false,
        'message'   =>  "Not authorize"
    ]);
    exit();
}

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

// pass the request method and user ID to the PersonController and process the HTTP request:
$input = (array) json_decode(file_get_contents('php://input'), TRUE);

$subject = $input['subject'];
$to = $input['to'];
$content = $input['content'];

if (empty($to)) {

    header("HTTP/1.1 408 No Email");
    echo json_encode([
        'result' => false,
        'message' => "Email is empty"
    ]);
    exit();
}

foreach ($to as $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("HTTP/1.1 408 Wrong Email");
        echo json_encode([
            'result' => false,
            'message' => "Email is invalid"
        ]);
        exit();
    }
}

sendMail($subject, $content, $to);

function sendMail($subject, $content, $to)
{
    $smtp = getenv("SMTP_HOST");
    $username = getenv("USERNAME");
    $password = getenv("PASSWORD");
    $from = getenv("SMTP_FROM");

    // Create the Transport
    $transport = (new Swift_SmtpTransport($smtp, 587, 'tls'))
        ->setUsername($username)
        ->setPassword($password)
        ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false)));

    try {
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($subject))
            ->setFrom([$from])
            ->setTo($to)
            ->setBody($content, 'text/html');

        // Send the message
        $result = $mailer->send($message);
        if ($result) {
            echo json_encode([
                'result' => true,
                'message' => "Sent email. Subject: $subject. To: " . json_encode($to),
                'result'    =>  print_r($result)
            ]);
        } else {
            echo json_encode([
                'result' => false,
                'message' => $result
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'result' => false,
            'message' => $e->getMessage()
        ]);
    }
}
