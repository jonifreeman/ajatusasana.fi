<?php

include ('common.php');

function signup() {
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
}

function query_group_class($id) {
  $sql = function($conn) use ($id) {
    return "SELECT * FROM group_class WHERE id = $id";
  };
  return sql($sql);
}

function query_group_class_cancellations($id) {
  $sql = function($conn) use ($id) {
    return "SELECT * FROM cancelled_class WHERE group_class_id = $id and (when_date between now() and now() + interval 1 month";
  };
  return sql($sql);
}

function count_bookings($id, $when) {
  $count_sql = function($conn) use ($id) {
    $w = mysqli_real_escape_string($conn, $when);
    return "SELECT count(1) FROM booking WHERE group_class_id = $id and when_date = $w";
  };
  $count_regulars_sql = function($conn) use ($id) {
    $w = mysqli_real_escape_string($conn, $when);
    return "SELECT count(1) FROM regular_client AS rc LEFT JOIN cancelled_regular AS c ON (rc.id = c.regular_client_id and rc.group_class_id = c.group_class_id and c.when_date = $w) WHERE rc.group_class_id = $id and c.regular_client_id IS NULL";
  };
  $bookings = sql($count_sql);
  $regulars = sql($count_regulars_sql);
  return $bookings + $regulars;
}

function query_miniretreats() {
  // TODO
}

function get_classes($id) {
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  signup();
} else if ($method == 'GET') {
  $id = $_GET['course'];
  get_classes($id);
}

?>
