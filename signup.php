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
  $variables = array("course" => $course);
  $html = file_get_contents("mail/kiitos_ilmoittautuminen.html");

  foreach($variables as $key => $value) {
    $html = str_replace('{{ '.$key.' }}', $value, $html);
  }

  $boundary = uniqid('np');

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "From: Stephanie Freeman <stephanie@ajatusasana.fi>\r\n";
  $headers .= "To: ".$email."\r\n";
  $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

  $message = "This is a MIME encoded message.";
  $message .= "\r\n\r\n--" . $boundary . "\r\n";
  $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

  $message .= "Kiitos ilmoittautumisesta!\r\n\r\nTervetuloa tunnille: ".$course."\r\n\r\nAjatus & Asana\r\nhttp://www.ajatusasana.fi";
  $message .= "\r\n\r\n--" . $boundary . "\r\n";
  $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

  $message .= $html;

  mail('', 'Ajatus & Asana, varaus', $message, $headers);
}

?>
