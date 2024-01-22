<?php
 session_start();
 $post["ip"] = @$_SERVER["HTTP_CF_CONNECTING_IP"]? @$_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
 $post["domain"] = $_SERVER["HTTP_HOST"];
 $post["referer"] = @$_SERVER["HTTP_REFERER"];
 $post["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
 $post["headers"] = json_encode(apache_request_headers());
//  $post["land"] = 1; //раскомментировать на в индексном файле лендинга

 $requestPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

 if($_GET)foreach($_GET as $key => $value) $_SESSION[$key] = $value;
 $post["utm"] = json_encode($_SESSION);

 $curl = curl_init("https://metaln4g.beget.tech/api/check_ip");
 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
 curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
 curl_setopt($curl, CURLOPT_ENCODING, "");
 curl_setopt($curl, CURLOPT_TIMEOUT, 5);
 curl_setopt($curl, CURLOPT_POST, true);
 curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

 $json_reqest = curl_exec($curl);
 curl_close($curl);
 $api_reqest = json_decode($json_reqest);

 if($api_reqest)foreach($api_reqest as $key => $value) $_SESSION[$key] = $value;
// Построение полного пути к файлу
$htmlFilePath = __DIR__ . $requestPath;

// Если это директория, добавляем слэш и пытаемся открыть index.html
if (is_dir($htmlFilePath)) {
    $htmlFilePath = rtrim($htmlFilePath, '/') . '/index.html';
}


if(!@$api_reqest || @$api_reqest->white_link || @$api_reqest->result == 0){
   header("HTTP/1.0 404 Not Found");
   echo '<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL ' . htmlspecialchars($requestPath) . ' was not found on this server.</p></body></html>';
} else{
// Если файл существует, возвращаем его
if (file_exists($htmlFilePath)) {
   // Получаем MIME-тип файла
   $mime_type = mime_content_type($htmlFilePath);
   header("Content-Type: $mime_type");
   readfile($htmlFilePath);
   exit();
}
 }

 ?>