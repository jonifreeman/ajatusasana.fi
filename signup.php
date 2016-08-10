<?php

include ('common.php');

function query_group_class($id) {
  $sql = function($conn) use ($id) {
    return "SELECT * FROM group_class WHERE id = $id";
  };
  return sql_query_one($sql);
}

function addBooking($email, $group_class_id, $date, $phone) {
  $sql = function($conn) use ($email, $group_class_id, $date, $phone) {
    $e = mysqli_real_escape_string($conn, $email);
    $d = mysqli_real_escape_string($conn, $date);
    $p = mysqli_real_escape_string($conn, $phone);
    return "INSERT INTO booking(email, group_class_id, when_date, phone) VALUES ('$e', $group_class_id, '$d', '$p') ON DUPLICATE KEY UPDATE email='$e'";
  };
  sql_set($sql);
}

function signup() {
  $id = $_POST['course'];
  $dates = $_POST['dates'];
  $to = 'stephanie@ajatusasana.fi';
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  $group_class = query_group_class($id);
  $subject = 'Ilmoittautuminen: ' . $group_class['name'];
  
  $datesArray = explode(",", $dates);
  // TODO validate
  foreach ($datesArray as $date) {
    addBooking($email, $group_class['id'], $date, $phone);
  }

  // TODO response JSON
  echo '{}';

  $message = 'Kurssi: ' . $group_class['name'] . "\r\n\r\nNimi: " . $name . "\r\n\r\nEmail: " . $email . "\r\n\r\nPuh: " . $phone;
  $headers = 'From: webmaster@ajatusasana.fi' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  // TODO enable mails
  //mail($to, $subject, $message, $headers);

  if (isset($email)) {
    $variables = array("course" => $group_class['name']);
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

    $message .= "Kiitos ilmoittautumisesta!\r\n\r\nTervetuloa tunnille: ".$group_class['name']."\r\n\r\nAjatus & Asana\r\nhttp://www.ajatusasana.fi";
    $message .= "\r\n\r\n--" . $boundary . "\r\n";
    $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

    $message .= $html;

    //mail('', 'Ajatus & Asana, varaus', $message, $headers);
  }
}

function query_group_class_cancellations($id) {
  $sql = function($conn) use ($id) {
    return "SELECT * FROM cancelled_class WHERE group_class_id = $id and (when_date between now() and now() + interval 1 month)";
  };
  return sql_query($sql);
}

function count_bookings($id, $when) {
  $count_sql = function($conn) use ($id, $when) {
    $w = mysqli_real_escape_string($conn, $when);
    return "SELECT count(1) as count FROM booking WHERE group_class_id = $id and when_date = '$w'";
  };
  $count_regulars_sql = function($conn) use ($id, $when) {
    $w = mysqli_real_escape_string($conn, $when);
    return "SELECT count(1) as count FROM regular_client AS rc LEFT JOIN cancelled_regular AS c ON (rc.id = c.regular_client_id and rc.group_class_id = c.group_class_id and c.when_date = '$w') WHERE rc.group_class_id = $id and c.regular_client_id IS NULL";
  };
  $bookings = sql_query_one($count_sql)['count'];
  $regulars = sql_query_one($count_regulars_sql)['count'];
  return $bookings + $regulars;
}

function query_miniretreats() {
  // TODO
}

function get_classes($id) {
  $group_class = query_group_class($id);
  $next_class = mysql_date(strtotime('next '.$group_class['day']));
  $cancellations = query_group_class_cancellations($id);
  // TODO, start from start date
  // TODO, only until end date
  $dates = array($next_class, mysql_date(strtotime($next_class.' + 1 week')), mysql_date(strtotime($next_class.' + 2 weeks')), mysql_date(strtotime($next_class.' + 3 weeks')));
  $availability = array_map(function($date) use($id, $group_class, $cancellations) {
      $cancelled = array_filter($cancellations, function($cancellation) use($date) { return $cancellation['when_date'] == $date; });
      if ($cancelled) {
        return array('date' => $date, 'cancelled' => true, 'reason' => $cancelled[0]['reason']);
      } else {
        $bookings = count_bookings($id, $date);
        return array('date' => $date, 'available' => ($group_class['max_size'] - $bookings));
      }
    }, $dates);
  $result_json = json_encode($availability);
  echo $result_json;
}

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  signup();
} else if ($method == 'GET') {
  $id = $_GET['course'];
  get_classes($id);
}

?>
