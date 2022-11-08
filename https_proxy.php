<?php

if (empty($_GET['url'])) {
    echo 'false';
    return;
}
$url = $_GET['url'];
echo getUrl($url);

function getUrl($url)
{
    echo date("Y-m-d H:i:s") . ' : ' . $url."\n";
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cURLConnection, CURLOPT_SSLVERSION, 4);

    $result = curl_exec($cURLConnection);

    if($result === false)
    {
        echo 'Curl error: ' . curl_error($cURLConnection) . "\n";

    }

    curl_close($cURLConnection);
    return $result."\n";

}