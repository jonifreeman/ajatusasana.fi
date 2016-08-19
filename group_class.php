<?php

include ('common.php');

function query_group_class($id) {
  $sql = function($conn) use ($id) {
    return "SELECT gc.*, GROUP_CONCAT(rc.email) AS regulars FROM group_class gc LEFT JOIN regular_client rc ON gc.id=rc.group_class_id WHERE gc.id=$id";
  };
  return sql_query_one($sql);
}

function query_bookings($id, $date) {
  $sql = function($conn) use ($id, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    return "SELECT email, phone FROM booking WHERE group_class_id=$id AND when_date='$d'";
  };
  return sql_query($sql);
}

function query_cancellations($id, $date) {
  $sql = function($conn) use ($id, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    return "SELECT r.email FROM cancelled_regular c JOIN regular_client r ON c.regular_client_id=r.id WHERE c.group_class_id=$id AND c.when_date='$d'";
  };
  return sql_query($sql);
}

function get_group_class($id, $date) {
  $group_class = query_group_class($id);
  if ($group_class['regulars']) {
    $group_class['regulars'] = explode(",", $group_class['regulars']);
  } else {
    $group_class['regulars'] = array();
  }

  $group_class['bookings'] = query_bookings($id, $date);
  $group_class['cancellations'] = query_cancellations($id, $date);
  echo json_encode($group_class);
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// TODO access control

if ($method == 'GET') {
  get_group_class($_GET['id'], $_GET['date']);
}