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

function add_enrollment($start, $name, $email, $phone, $comment) {
  $endDate = new DateTime($start);
  $endDate->modify('+90 minutes');
  $end = $endDate->format('Y-m-d H:i:s');
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
