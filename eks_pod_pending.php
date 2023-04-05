<?php
error_reporting(E_ALL ^ E_DEPRECATED);
exec("kubectl get pod --all-namespaces", $out);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$check = true;
$to = getenv("TO_EMAIL");
$appName = getenv("APP_NAME");
$date = date("[Y-m-d H:i:s]");
$content = '';
for ($i=2;$i<count($out);$i++) {
    $cols = preg_split('/\s+/', $out[$i]);
    $status = $cols[3];
    if ($status == "Running") {
        $namespace = $cols[0];
        $pod = $cols[1];
        $check = false;
        $content .= "Pod $pod ($namespace) is Pending at $date <br>";
    }
}

if ($check) {
    $date = date("[Y-m-d H:i:s]");
    echo "$date Everything is OK.\n";
} else {
    $subject = "[$appName] Pod Pending detect at $date";
    echo $subject . "\n";
    echo shell_exec("php send_mail.php '$subject' '$to' '$content'") . "\n";
}
