<?php

include ('common.php');

function store_auth_token($auth_token) {
  $sql = function($conn) use ($auth_token) {
    $token = mysqli_real_escape_string($conn, $auth_token);
    return "INSERT INTO auth_token(token, valid_until) VALUES ('$token', NOW()+INTERVAL 3 HOUR)";
  };
  sql_set($sql);
}

function auth($username, $password) {
  $sql = function($conn) use ($username, $password) {
    $u = mysqli_real_escape_string($conn, $username);
    return "SELECT password FROM auth WHERE username='$u'";
  };
  $hash = sql_query($sql)[0]['password'];
  if (password_verify($password, $hash)) {    
    $auth_token = md5(rand());
    store_auth_token($auth_token);
    setcookie('session_id', $auth_token);
    header("location: ajanvaraus.html");
  } else {
    var_dump(http_response_code(403));
    echo "Login failed";
    die();
  }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
  auth($_POST['username'], $_POST['password']);
}

?>
