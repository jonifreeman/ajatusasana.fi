<?php

include ('common.php');

function query_open_group_classes() {
  $sql = function($conn) {
    return "SELECT gc.*, GROUP_CONCAT(rc.email ORDER BY rc.group_class_id) AS regulars 
            FROM group_class gc 
            LEFT JOIN regular_client rc ON gc.id=rc.group_class_id 
            WHERE NOW() < gc.end OR gc.end IS NULL GROUP BY gc.id";
  };
  return sql_query($sql);
}

$group_classes = query_open_group_classes();
$new_row = array('class_type' => 'normal');
array_push($group_classes, $new_row);

// TODO access control

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html>

<head>
  <title>Kalenterin ylläpito</title>
  <meta charset="utf-8"  />
  <link rel="stylesheet" type="text/css" href="/css/reset.css">
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css">
  <link rel="stylesheet" type="text/css" href="/css/styles.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.11.2/lodash.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/lang/fi.js"></script>
  <script src="/js/admin_schedule.js"></script>
</head>

<body>

<table class="schedule">
  <thead>
    <tr>
      <th>Tyyppi</th>
      <th>Nimi</th>
      <th>Viikonpäivä</th>
      <th>Kello</th>
      <th>Alkaa</th>
      <th>Päättyy</th>
      <th>Näkyy kalenterissa alkaen</th>
      <th>Max</th>
      <th>Huom!</th>
      <th>Linkki</th>
      <th>Vakiokävijät</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($group_classes as $group_class): ?>
    <tr class="group_class" data-id="<?= $group_class['id'] ?>">
      <td>
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="normal" <?= ($group_class['class_type'] == 'normal') ? 'checked' : '' ?> >Normaali</input>
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="miniretreat" <?= ($group_class['class_type'] == 'miniretreat') ? 'checked' : '' ?> >Miniretriitti</input>
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="course" <?= ($group_class['class_type'] == 'course') ? 'checked' : '' ?> >Kurssi</input>
      </td>
      <td><input type="text" name="name" value="<?= $group_class['name'] ?>"/></td>
      <td><input type="text" name="day" value="<?= $group_class['day'] ?>"/></td>
      <td><input type="text" name="start_time" value="<?= $group_class['start_time'] ?>"/> - <input type="text" name="end_time" value="<?= $group_class['end_time'] ?>"/</td>
      <td><input type="text" name="start" value="<?= $group_class['start'] ?>"/></td>
      <td><input type="text" name="end" value="<?= $group_class['end'] ?>"/></td>
      <td><input type="text" name="display_start" value="<?= $group_class['display_start'] ?>"/></td>
      <td><input type="text" name="max_size" value="<?= $group_class['max_size'] ?>"/></td>
      <td><input type="text" name="highlight" value="<?= $group_class['highlight'] ?>"/></td>
      <td><input type="text" name="anchor" value="<?= $group_class['anchor'] ?>"/></td>
      <td><textarea name="regulars"><?= $group_class['regulars'] ?></textarea></td>
      <td><input class="save" type="submit" value="Tallenna"/></td>
    </tr>
    <?php endforeach; ?>
  </tbody>

</table>

<div class="popup group-class-popup">
  <div class="popup-content">
    <img class="close" src="/img/popup_close.png" />
    <h2 class="name"></h2>
    <div class="error">Muutos epäonnistui. Yritä hetken kuluttua uudestaan.</div>
     <div class="contact-info">Peruutuksen syy: </div><input type="text" class="reason" />    
    <input class="cancel-group-class-button" type="button" value="Peruuta tunti" />
  </div>
</div>

<div id='calendar'></div>

</body>
</html>
