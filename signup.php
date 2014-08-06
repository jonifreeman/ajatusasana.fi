<?php
$to      = 'camisteph@gmail.com';
$course  = $_POST['course'];
$subject = 'Ilmoittautuminen: ' . $course;
$message = 'Kurssi: ' . $course . "\r\n\r\nNimi: " . $_POST['name'] . "\r\n\r\nKontakti: " . $_POST['contact'];
$headers = 'From: webmaster@ajatusasana.fi' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

if ($fd = @fopen("/home/ajatusas/signup.csv", "a")) {
  $date = date("Y-m-d H:i:s", time());
  $result = fputcsv($fd, array($date, $remote_addr, $request_uri, $message));
  fclose($fd);
}

?>
