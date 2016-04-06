<?php
$name = $_POST['name'];
$email = $_POST['email'];
$url = 'http://ajatusasana.fi/mailman/subscribe/joogavalmennus_ajatusasana.fi';
$data = array('email' => $email, 'fullname' => $name);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
  var_dump(http_response_code(500));
}

?>
