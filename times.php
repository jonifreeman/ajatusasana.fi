<?php

include ('common.php');

function query_times($start, $end) {
  $times_sql = function($conn) use ($start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    return "SELECT *, 'time' AS className FROM times WHERE start >= '$s' AND end <= '$e' ORDER BY start";
  };
  return sql_query($times_sql);
}

function query_enrollments($start, $end) {
  $enrollments_sql = function($conn) use ($start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    return "SELECT *, name AS title, 'enrollment' AS className FROM enrollments WHERE start >= '$s' AND end <= '$e' ORDER BY start";
  };
  return sql_query($enrollments_sql);
}

function is_valid_slot($start, $end) {
  $now = new DateTime();
  return ($end > $now) && (($end->getTimestamp() - $start->getTimestamp()) / 60) >= 90;
}

function make_start_date($date) {
  $next_quarter = ceil(time()/900)*900;
  $now = new DateTime();
  $now->setTimestamp($next_quarter);
  return max($now, $date);
}

function get_vacant_times($start, $end) {
  $times = query_times($start, $end);
  $enrollments = query_enrollments($start, $end);
  $vacant_times = array_map(function($time) use ($enrollments) {
      $date_format = 'Y-m-d H:i:s';
      $time_slot_start = new DateTime($time['start']);
      $time_slot_end = new DateTime($time['end']);
      $vacants = array();
      foreach ($enrollments as $enrollment) {
        $before15m = new DateInterval("PT15M");
        $before15m->invert = 1;
        $enrollment_start = (new DateTime($enrollment['start']))->add($before15m);
        $enrollment_end = (new DateTime($enrollment['end']))->add(new DateInterval('PT15M'));
        if ($enrollment_start <= $time_slot_end && $enrollment_end >= $time_slot_start) {
          $start = make_start_date($time_slot_start);
          if (is_valid_slot($start, $enrollment_start)) {
            array_push($vacants, array('start' => $start->format($date_format), 'end' => $enrollment_start->format($date_format)));
          }
          $time_slot_start = min($time_slot_end, $enrollment_end);
        }
      }
      $start = make_start_date($time_slot_start);
      if (is_valid_slot($start, $time_slot_end)) {
        array_push($vacants, array('start' => $start->format($date_format), 'end' => $time_slot_end->format($date_format)));
      }
      return $vacants;
    }, $times);
  echo json_encode(call_user_func_array('array_merge', $vacant_times));
}

function get_times_and_enrollments($start, $end) {
  verify_auth_token();
  $times = query_times($start, $end);
  $enrollments = query_enrollments($start, $end);
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

function create_time($start, $end) {
  if (new DateTime($start) < new DateTime($end)) {
    verify_auth_token();
    $sql = function($conn) use ($start, $end) {
      $s = mysqli_real_escape_string($conn, $start);
      $e = mysqli_real_escape_string($conn, $end);
      return "INSERT INTO times(start, end) VALUES ('$s', '$e')";
    };
    sql_set($sql);
  }
}

function update_time($id, $start, $end) {
  verify_auth_token();
  $sql = function($conn) use ($id, $start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    if (new DateTime($start) >= new DateTime($end)) {
      return "DELETE FROM times WHERE id=$id";
    } else {
      return "UPDATE times SET start='$s', end='$e' WHERE id=$id";
    }
  };
  sql_set($sql);
}

function create_or_update_times() {
  $id = $_POST['id'];
  $start = $_POST['start'];
  $end = $_POST['end'];
  if ($id === NULL) {
    create_time($start, $end);
  } else {
    update_time(intval($id), $start, $end);
  }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  create_or_update_times();
} else if ($method == 'GET') {
  get_times();
}

?>
