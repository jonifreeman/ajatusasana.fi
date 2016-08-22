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

function sql_query_one($sql) {
  $res = sql_query($sql);
  if (count($res) != 1) {
    internal_server_error("SQL error");
  }

  return $res[0];
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

function format_timestring($time) {
  $parts = explode(':', $time);
  return $parts[0].':'.$parts[1];
}

function mysql_date($date) {
  return date("Y-m-d", $date);
}

function parse_hour($time) {
  return (int) explode(':', $time)[0];
}

function parse_minute($time) {
  return (int) explode(':', $time)[1];
}

function send_mail_to_client($to, $subject, $plain_text, $template_file, $variables) {
  $html = file_get_contents($template_file);

  foreach($variables as $key => $value) {
    $html = str_replace('{{ '.$key.' }}', $value, $html);
  }
  
  $boundary = uniqid('np');

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "From: Stephanie Freeman <stephanie@ajatusasana.fi>\r\n";
  $headers .= "To: ".$to."\r\n";
  $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

  $message = "This is a MIME encoded message.";
  $message .= "\r\n\r\n--" . $boundary . "\r\n";
  $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

  $message .= $plain_text;
  $message .= "\r\n\r\n--" . $boundary . "\r\n";
  $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

  $message .= $html;

  mail('', $subject, $message, $headers);
}

function send_mail_to_aa($subject, $message) {
  mail('stephanie@ajatusasana.fi', $subject, $message, 'From: webmaster@ajatusasana.fi');
}

$miniretreat = array(
  "id" => -1,
  "name" => "Ajatus & Asana miniretriitit ja teemapäivät kerran kuussa",
  "day" => "sat",
  "anchor" => "retriitti",
  "start_time_hour" => 11,
  "session_type" => "course"
);

?>
