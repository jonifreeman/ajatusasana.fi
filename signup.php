<?php
$to      = 'camisteph@gmail.com';
$course  = $_POST['course'];
$subject = 'Ilmoittautuminen: ' . $course;
$message = 'Nimi: ' . $_POST['name'] . '\n\nKontakti: ' . $_POST['contact'];
$headers = 'From: webmaster@ajatusasana.fi' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
