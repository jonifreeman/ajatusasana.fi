<?php

include ('common.php');

function cancel($id, $date, $reason) {
  $sql = function($conn) use ($id, $date, $reason) {
    $d = mysqli_real_escape_string($conn, $date);
    $r = mysqli_real_escape_string($conn, $reason);    
    return "INSERT INTO cancelled_class(group_class_id, when_date, reason) VALUES ($id, '$d', '$r')";
  };
  sql_set($sql);
}

function revert_cancellation($id, $date) {
  $sql = function($conn) use ($id, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    return "DELETE FROM cancelled_class WHERE group_class_id=$id AND when_date='$d'";
  };
  sql_set($sql);
}

function is_already_cancelled($id, $date) {
  $sql = function($conn) use ($id, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    return "SELECT * FROM cancelled_class WHERE group_class_id=$id AND when_date='$d'";
  };
  return count(sql_query($sql)) > 0;
}


$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

verify_auth_token();

if ($method == 'POST') {
  $id = $_POST['id'];
  $date = $_POST['date'];
  $reason = $_POST['reason'];
  if (is_already_cancelled($id, $date)) {
    revert_cancellation($id, $date);
  } else {
    cancel($id, $date, $reason);
  }

  echo "{}";
}
