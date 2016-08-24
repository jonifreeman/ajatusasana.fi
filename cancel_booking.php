<?php

include ('common.php');

function cancel($id, $date, $email) {
  $sql = function($conn) use ($id, $date, $email) {
    $d = mysqli_real_escape_string($conn, $date);
    $e = mysqli_real_escape_string($conn, $email);
    return "DELETE FROM booking WHERE email='$e' AND group_class_id=$id AND when_date='$d'";
  };
  sql_set($sql);

  echo "{}";
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

verify_auth_token();

if ($method == 'POST') {
  cancel($_POST['id'], $_POST['date'], $_POST['email']);
}
