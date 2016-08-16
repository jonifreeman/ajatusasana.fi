<?php

include ('common.php');

function create_or_update_group_class() {
  $sql = function($conn) {
    $id = $_POST['id'];
    $ds = mysqli_real_escape_string($conn, $_POST['display_start']);
    $s = mysqli_real_escape_string($conn, $_POST['start']);
    $d = mysqli_real_escape_string($conn, $_POST['day']);
    $st = mysqli_real_escape_string($conn, $_POST['start_time']);
    $et = mysqli_real_escape_string($conn, $_POST['end_time']);
    $n = mysqli_real_escape_string($conn, $_POST['name']);
    $max = (int) $_POST['max_size'];
    $ct = mysqli_real_escape_string($conn, $_POST['class_type']);
    $a = mysqli_real_escape_string($conn, $_POST['anchor']);
    $h = mysqli_real_escape_string($conn, $_POST['highlight']);
    $e = $_POST['end'] == '' ? 'NULL' : "'".mysqli_real_escape_string($conn, $_POST['end'])."'";
    if ($id) {
      return "UPDATE group_class SET display_start='$ds', start='$s', end=$e, day='$d', start_time='$st', end_time='$et', name='$n', max_size=$max, class_type='$ct', anchor='$a', highlight='$h' WHERE id=$id";
    } else {
      return "INSERT INTO group_class(display_start, start, end, day, start_time, end_time, name, max_size, class_type, anchor, highlight) VALUES ('$ds', '$s', $e, '$d', '$st', '$et', '$n', $max, '$ct', '$a', '$h')";
    }
  };
  sql_set($sql);

  // TODO save requlars
}

// TODO access control

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  create_or_update_group_class();
}

?>
