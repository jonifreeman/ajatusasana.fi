<?php

include ('common.php');

function delete_regulars($group_class_id) {
  $sql = function($conn) use ($group_class_id) {
    return "DELETE FROM regular_client WHERE group_class_id=$group_class_id";
  };
  sql_set($sql);
}

function insert_regular($group_class_id, $regular) {
  $sql = function($conn) use ($group_class_id, $regular) {
    $email = mysqli_real_escape_string($conn, $regular);
    return "INSERT INTO regular_client(email, group_class_id) VALUES ('$email', $group_class_id)";
  };
  sql_set($sql);
}

function create_or_update_group_class() {
  $id = $_POST['id'];
  $sql = function($conn) use ($id) {
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
  $gen_id = sql_set_and_get_id($sql);

  $group_class_id = $id ? $id : $gen_id;
  $regulars = explode(",", $_POST['regulars']);
  delete_regulars($group_class_id);
  foreach ($regulars as $regular) {
    if (trim($regular) != '') {
      insert_regular($group_class_id, trim($regular));
    }
  }

  echo '{"id":'.$group_class_id."}";
}

header('Content-Type: application/json; charset=utf-8');

verify_auth_token();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  create_or_update_group_class();
}

?>
