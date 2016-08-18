<?php

include ('common.php');

function query_group_class($id) {
  $sql = function($conn) use ($id) {
    return "SELECT * FROM group_class WHERE id=$id";
  };
  return sql_query($sql);
}

function get_group_class($id) {
  $group_class = query_group_class($id);

  echo json_encode($group_class);
}

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// TODO access control

if ($method == 'GET') {
  get_group_class($_GET['id']);
}
