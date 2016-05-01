<?php

include ('env.php');

$db_server = "127.0.0.1";
$db_database = "ajatusas";
$db_username = "ajatusas_user";

date_default_timezone_set('Europe/Helsinki');

function internal_server_error($msg) {
  var_dump(http_response_code(500));
  echo $msg;
  die();
}

function sql_query($sql) {
  global $db_server, $db_database, $db_username, $db_password;
  
  $conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
  if (!$conn) {
    internal_server_error("Connection failed: " . mysqli_connect_error());
  }
  $query_result = mysqli_query($conn, $sql($conn)) or internal_server_error(mysqli_error($conn));
  $rows = array();
  while ($row = mysqli_fetch_assoc($query_result)) {
    array_push($rows, $row);
  }
  mysqli_close($conn);
  return $rows;
}

function sql_set($sql) {
  global $db_server, $db_database, $db_username, $db_password;
  
  $conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
  if (!$conn) {
    internal_server_error("Connection failed: " . mysqli_connect_error());
  }
  mysqli_query($conn, $sql($conn)) or internal_server_error(mysqli_error($conn));
  mysqli_close($conn);
}

function cleanup_auth_tokens() {
  $sql = function($conn) {
    return "DELETE FROM auth_token WHERE valid_until<NOW()";
  };
  return sql_set($sql);
}

function verify_auth_token() {
  cleanup_auth_tokens();
  $auth_token = $_COOKIE['session_id'];
  $sql = function($conn) use ($auth_token) {
    $token = mysqli_real_escape_string($conn, $auth_token);
    return "SELECT COUNT(1) AS count FROM auth_token WHERE token='$token' and valid_until>NOW()";
  };
  if (sql_query($sql)[0]['count'] == 0) {
    var_dump(http_response_code(403));
    die();
  } else {
    $update_validity_sql = function($conn) use ($auth_token) {
      $token = mysqli_real_escape_string($conn, $auth_token);
      return "UPDATE auth_token SET valid_until=NOW()+INTERVAL 3 HOUR WHERE token='$token'";
    };
    sql_set($update_validity_sql);
  }
}

function human_date($date) {
  return $date->format('d.m.Y H:i');
}

function human_time($date) {
  return $date->format('H:i');
}

function send_mail_to_client($to, $subject, $template_file, $variables) {
  $template = file_get_contents($template_file);

  foreach($variables as $key => $value) {
    $template = str_replace('{{ '.$key.' }}', $value, $template);
  }
  mail($to, $subject, $template, "From: Stephanie Freeman <stephanie@ajatusasana.fi>\r\nContent-Type: text/html");
}

function send_mail_to_aa($subject, $message) {
  mail('stephanie@ajatusasana.fi', $subject, $message, 'From: webmaster@ajatusasana.fi');
}


?>
