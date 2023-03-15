<?php
error_reporting(E_ALL ^ E_DEPRECATED);
exec("kubectl get node", $out);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$check = true;
$to = getenv("TO_EMAIL");
$appName = getenv("APP_NAME");
for ($i=2;$i<count($out);$i++) {
    $cols = preg_split('/\s+/', $out[$i]);
    $status = $cols[1];
    if ($status == "NotReady") {
        $name = $cols[0];

        // $subject = $argv[1]; $to = $argv[2]; $content = $argv[3];
        $date = date("[Y-m-d H:i:s]");
        $subject = "[$appName] Node $name is not ready at $date";
        $content = "Node $name is not ready at $date";
        echo $subject . "\n";
        echo shell_exec("php send_mail.php '$subject' '$to' '$content'") . "\n";
        $check = false;
    }
}

if ($check) {
    $date = date("[Y-m-d H:i:s]");
    echo "$date Everything is OK.\n";
}