<?php
$to      = 'stephanie@ajatusasana.fi';
$course  = $_POST['course'];
$subject = 'Ilmoittautuminen: ' . $course;
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = 'Kurssi: ' . $course . "\r\n\r\nNimi: " . $name . "\r\n\r\nEmail: " . $email . "\r\n\r\nPuh: " . $phone;
$headers = 'From: webmaster@ajatusasana.fi' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

if ($fd = @fopen("/home/ajatusas/signup.csv", "a")) {
  $date = date("Y-m-d H:i:s", time());
  $result = fputcsv($fd, array($date, $name, $email, $phone, $course));
  fclose($fd);
}

if (isset($email)) {
  mail($email, 'Ajatus & Asana, varaus', "Kiitos ilmoittautumisesta!\r\n\r\nTervetuloa tunnille: ".$course."\r\n\r\nAjatus & Asana\r\nhttp://www.ajatusasana.fi", 'From: Stephanie Freeman <stephanie@ajatusasana.fi>');
}

?>
