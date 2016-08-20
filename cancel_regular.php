<?php

include ('common.php');

function cancel_regular($id, $date, $regular_client_id) {
  $sql = function($conn) use ($id, $date, $regular_client_id) {
    $d = mysqli_real_escape_string($conn, $date);
    return "INSERT INTO cancelled_regular(regular_client_id, group_class_id, when_date) VALUES ($regular_client_id, $id, '$d')";
  };
  sql_set($sql);
}

function revert_cancellation($id, $date, $regular_client_id) {
  $sql = function($conn) use ($id, $date, $regular_client_id) {
    $d = mysqli_real_escape_string($conn, $date);
    return "DELETE FROM cancelled_regular WHERE regular_client_id=$regular_client_id AND group_class_id=$id AND when_date='$d'";
  };
  sql_set($sql);
}

function is_already_cancelled($id, $date, $regular_client_id) {
  $sql = function($conn) use ($id, $date, $regular_client_id) {
    $d = mysqli_real_escape_string($conn, $date);
    return "SELECT * FROM cancelled_regular WHERE regular_client_id=$regular_client_id AND group_class_id=$id AND when_date='$d'";
  };
  return count(sql_query($sql)) > 0;
}

function query_regular_client($id, $email) {
  $sql = function($conn) use ($id, $email) {
    $e = mysqli_real_escape_string($conn, $email);
    return "SELECT id FROM regular_client WHERE group_class_id=$id AND email='$e'";
  };
  return sql_query_one($sql)['id'];  
}

function cancel($id, $date, $email) {
  $regular_client_id = query_regular_client($id, $email);
  if (is_already_cancelled($id, $date, $regular_client_id)) {
    revert_cancellation($id, $date, $regular_client_id);
  } else {
    cancel_regular($id, $date, $regular_client_id);
  }

  echo "{}";
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// TODO access control

if ($method == 'POST') {
  cancel($_POST['id'], $_POST['date'], $_POST['email']);
}
