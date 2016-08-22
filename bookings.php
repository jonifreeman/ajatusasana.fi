<?php

include ('common.php');

function query_group_classes($start, $end) {
  $sql = function($conn) use ($start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    return "SELECT gc.*, cc.group_class_id IS NOT NULL AS is_cancelled 
            FROM group_class gc
            LEFT JOIN cancelled_class cc ON gc.id=cc.group_class_id AND (cc.when_date >= '$s' AND cc.when_date < '$e')
            WHERE gc.start <= '$e' AND (gc.end >= '$s' OR gc.end IS NULL) ORDER BY gc.start";
  };

  $group_classes = sql_query($sql);

  foreach ($group_classes as $key => $group_class) {
    $group_classes[$key]['is_cancelled'] = (bool)$group_class['is_cancelled'];
  }

  return $group_classes;
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
        'is_cancelled' => $group_class['is_cancelled'],
        'backgroundColor' => $group_class['is_cancelled'] ? 'red' : NULL,
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
