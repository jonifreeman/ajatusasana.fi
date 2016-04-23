<?php

// TODO include ('common.php');
function sql_query($sql, $query_handler) {
  $db_server = "127.0.0.1";
  $db_database = "ajatusas";
  $db_username = "ajatusas_user";
  $db_password = "secret";
  $conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  $query_result = mysqli_query($conn, $sql) or die(mysqli_error());
  $result = $query_handler($query_result);
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
  $times_sql = "SELECT *, 'time' as type from times";
  $times = sql_query($times_sql, 'all_rows_handler');
  $enrollments_sql = "SELECT *, 'enrollment' as type from enrollments";
  $enrollments = sql_query($enrollments_sql, 'all_rows_handler');
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
