<?php

include ('common.php');

function cancel($id, $date, $reason) {
  $sql = function($conn) use ($id, $date, $reason) {
    $d = mysqli_real_escape_string($conn, $date);
    $r = mysqli_real_escape_string($conn, $reason);    
    return "INSERT INTO cancelled_class(group_class_id, when_date, reason) VALUES ($id, '$d', '$r')";
  };
  sql_set($sql);

  echo "{}";
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// TODO access control

if ($method == 'POST') {
  cancel($_POST['id'], $_POST['date'], $_POST['reason']);
}
