<?php

include ('common.php');

function add_enrollment($start, $name, $email, $phone, $comment) {
  // TODO validate
  $sql = function($conn) use ($start, $name, $email, $phone, $comment) {
    $s = mysqli_real_escape_string($conn, $start);
    $end = new DateTime($s);
    $end->modify('+90 minutes');
    $e = $end->format('Y-m-d H:i:s');
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
