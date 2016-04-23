<?php

// TODO include ('common.php');
function internalServerError($msg) {
  echo $msg;
  var_dump(http_response_code(500));
  die($msg);
}

function sql_query($sql, $params, $query_handler = 'all_rows_handler') {
  $db_server = "127.0.0.1";
  $db_database = "ajatusas";
  $db_username = "ajatusas_user";
  $db_password = "secret";
  $conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
  if (!$conn) {
    internalServerError("Connection failed: " . mysqli_connect_error());
  }
  $stmt = mysqli_prepare($conn, $sql);
  foreach ($params as $param) {
    mysqli_stmt_bind_param($stmt, $param, $params[$param]);
  }
  mysqli_stmt_execute($stmt) or internalServerError(mysqli_stmt_error($stmt));
  $query_result = mysqli_stmt_get_result($stmt);
  $result = $query_handler($query_result);
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
  return $result;
}

function all_rows_handler($results) {
  $rows = array();
  while ($row = mysqli_fetch_assoc($results)) {
    array_push($rows, $row);
  }
  return $rows;
}

function verify_auth_token() {
  // TODO: implement
}

function get_vacant_times($start, $end) {
}

function get_times_and_enrollments($start, $end) {
  verify_auth_token();
  // TODO add where
  $times_sql = "SELECT *, 'time' AS type FROM times WHERE start>=? AND end <= ?";
  $times = sql_query($times_sql, array('start' => $start, 'end' => $end));
  $enrollments_sql = "SELECT *, 'enrollment' AS type FROM enrollments WHERE start>=? AND end <= ?";
  $enrollments = sql_query($enrollments_sql, array('start' => $start, 'end' => $end));
  $result_json = json_encode(array_merge($times, $enrollments));
  echo $result_json;
}

function get_times() {
  $vacant = $_GET['vacant'];
  $start = $_GET['start'];
  $end = $_GET['end'];
  if ($vacant == 'true') {
    get_vacant_times($start, $end);
  } else {
    get_times_and_enrollments($start, $end);
  }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  create_or_update_times();
} else if ($method == 'GET') {
  get_times();
}

?>
