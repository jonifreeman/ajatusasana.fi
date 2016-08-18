<?php

include ('common.php');

function query_group_classes($start, $end) {
  $sql = function($conn) use ($start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    return "SELECT * FROM group_class WHERE start <= '$e' AND (end >= '$s' OR end IS NULL) ORDER BY start";
  };
  return sql_query($sql);
}

function create_next_datetime($from, $day, $time) {
  $date = new DateTime();
  if ($day != 'mon') {
    $date->setTimestamp(strtotime('next '.$day, strtotime($from)));
  } else {
    $date->setTimestamp(strtotime($from));
  }
  date_time_set($date, parse_hour($time), parse_minute($time));
  return $date;
}

function get_bookings($start, $end) {
  $group_classes = query_group_classes($start, $end);

  $bookings = array_map(function($group_class) use ($start, $end) {
      $date_format = 'Y-m-d H:i:s';
      $s = create_next_datetime($start, $group_class['day'], $group_class['start_time']);
      $e = create_next_datetime($start, $group_class['day'], $group_class['end_time']);
      return array(
        'id' => $group_class['id'],
        'title' => $group_class['name'],
        'start' => $s->format($date_format),
        'end' => $e->format($date_format)
      );
    }, $group_classes);

  $result_json = json_encode($bookings);
  echo $result_json;
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// TODO access control

if ($method == 'GET') {
  $start = $_GET['start'];
  $end = $_GET['end'];
  get_bookings($start, $end);
}

?>
