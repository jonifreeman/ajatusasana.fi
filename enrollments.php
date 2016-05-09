<?php

include ('common.php');

function validate_enrollment_time($start, $end) {
  $sql = function($conn) use ($start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    return "SELECT COUNT(1) as count FROM enrollments WHERE (start between '$s' and '$e') or (end between '$s' and '$e') or (start <= '$s' and end >= '$e')";
  };
  if (sql_query($sql)[0]['count'] > 0) {
    var_dump(http_response_code(409));
    die();
  }
}

function validate_enrollment($start, $name, $email, $phone) {
  if (empty($start) || empty($name) || empty($email) || empty($phone)) {
    var_dump(http_response_code(400));
    die();
  }
}

function add_enrollment($start, $name, $email, $phone, $comment) {
  $endDate = new DateTime($start);
  $endDate->modify('+90 minutes');
  $end = $endDate->format('Y-m-d H:i:s');
  validate_enrollment($start, $name, $email, $phone);
  validate_enrollment_time($start, $end);

  $sql = function($conn) use ($start, $end, $name, $email, $phone, $comment) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    $n = mysqli_real_escape_string($conn, $name);
    $em = mysqli_real_escape_string($conn, $email);
    $p = mysqli_real_escape_string($conn, $phone);
    $c = mysqli_real_escape_string($conn, $comment);
    return "INSERT INTO enrollments(start, end, name, email, phone, comment) VALUES ('$s', '$e', '$n', '$em', '$p', '$c')";
  };
  sql_set($sql);

  $thank_you_message = "Kiitos ajanvarauksesta!\r\n\r\nLämpimästi tervetuloa varaamanasi ajankohtana:\r\n".human_date(new DateTime($start))." - ".human_time($endDate)."\r\n\r\nJoogaa Ajatuksella - Pintaa Syvemmälle\r\n\r\nAjatus & Asana\r\nhttp://www.ajatusasana.fi";
  send_mail_to_client($email, "Ajatus & Asana, ajanvaraus", $thank_you_message, "mail/kiitos_ajanavaraus.html", array("start" => human_date(new DateTime($start)), "end" => human_time($endDate)));

  $message = "Uusi ajanvaraus.\r\n\r\nNimi: ".$name."\r\nEmail: ".$email."\r\nPuh.: ".$phone."\r\nAjankohta: ".human_date(new DateTime($start))." - ".human_time($endDate)."\r\nLisätietoja: ".$comment."\r\n\r\nAjatus & Asana\r\nhttp://www.ajatusasana.fi";
  send_mail_to_aa('Ajanvaraus '.$name, $message);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  $start = $_POST['start'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $comment = $_POST['comment'];
  add_enrollment($start, $name, $email, $phone, $comment);
}

?>
