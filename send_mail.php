<?php
require "bootstrap.php";

// pass the request method and user ID to the PersonController and process the HTTP request:
if (count($argv) < 3) {
    echo json_encode([
        'result' => false,
        'message' => "Not enough variables"
    ]);
    return;
}

$subject = $argv[1];
$to = $argv[2];
$content = $argv[3];

$toArray = explode(',', $to);
foreach ($toArray as $email) {
    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        header("HTTP/1.1 408 Wrong Email");
        echo json_encode([
            'result' => false,
            'message' => "Email is invalid"
        ]);
        exit();
    }
}

sendMail($subject, $content, $toArray);

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
