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
  <link rel="stylesheet" type="text/css" href="/css/admin.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.11.2/lodash.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/lang/fi.js"></script>
  <script src="/js/admin_schedule.js"></script>
</head>

<body>

<div class="popup group-class-popup">
  <div class="popup-content">
    <img class="close" src="/img/popup_close.png" />
    <h2 class="name"></h2>
  
    <h3>Vakioasiakkaat</h3>
    <ul class="regulars"></ul>

    <h3>Ilmoittautuneet</h3>
    <ul class="bookings"></ul>

    <div class="error">Muutos epäonnistui. Yritä hetken kuluttua uudestaan.</div>
    <div class="cancel-group-class-info contact-info">Peruutuksen syy: </div><input type="text" class="cancel-group-class-input reason" />
    <input class="cancel-group-class-button" type="button" value="Peruuta tunti" />
  </div>
</div>

<div class="schedule-body">
<div id='calendar'></div>

<table class="admin-schedule">
  <thead>
    <tr>
      <th>Tyyppi<br>N/M/K
      </th>
      <th>Nimi</th>
      <th>Pv</th>
      <th>Kello</th>
      <th>Alkaa</th>
      <th>Päättyy</th>
      <th>Kalenterissa</th>
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
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="normal" <?= ($group_class['class_type'] == 'normal') ? 'checked' : '' ?> ></input>
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="miniretreat" <?= ($group_class['class_type'] == 'miniretreat') ? 'checked' : '' ?> ></input>
        <input type="radio" name="type-<?= $group_class['id'] ?>" value="course" <?= ($group_class['class_type'] == 'course') ? 'checked' : '' ?> ></input>
      </td>
      <td><input type="text" name="name" value="<?= $group_class['name'] ?>"/></td>
      <td><input type="text" name="day" value="<?= $group_class['day'] ?>"/></td>
      <td><input type="text" name="start_time" value="<?= format_timestring($group_class['start_time']) ?>"/> - <input type="text" name="end_time" value="<?= format_timestring($group_class['end_time']) ?>"/</td>
      <td><input type="date" name="start" value="<?= $group_class['start'] ?>"/></td>
      <td><input type="date" name="end" value="<?= $group_class['end'] ?>"/></td>
      <td><input type="date" name="display_start" value="<?= $group_class['display_start'] ?>"/></td>
      <td><input type="text" name="max_size" value="<?= $group_class['max_size'] ?>"/></td>
      <td><input type="text" name="highlight" value="<?= $group_class['highlight'] ?>"/></td>
      <td><input type="text" name="anchor" value="<?= $group_class['anchor'] ?>"/></td>
      <td><textarea name="regulars"><?= $group_class['regulars'] ?></textarea></td>
      <td><input class="save" type="submit" value="<?= $group_class['id'] ? 'Tallenna' : 'Luo uusi' ?>"/></td>
    </tr>
    <?php endforeach; ?>
  </tbody>

</table>

<div class="instructions">

<h1>Ohjeet</h1>

<ul>
  <li><b>Tyyppi</b></li>
  N = normaali viikkotunti, M = miniretriitti, K = kurssi
  <li><b>Pv</b></li>
  mon, tue, wed, thu, fri, sat, sun
  <li><b>Kello</b></li>
  Esim. 12:30
  <li><b>Kalenterissa</b></li>
  Päivämäärä milloin ilmestyy näkyville kalenteriin. Eli voi laittaa kalenteriin näkyville ennen kuin tunnit varsinaisesti alkavat.
  <li><b>Max</b></li>
  Maksimi osallistujamäärä
  <li><b>Huom!</b></li>
  Tällä voi asettaa pienen huomitekstin tunnin kuvaukseen. Esim. 'Alkaa Syyskuussa 2018!'
  <li><b>Linkki</b></li>
  Linkki tuntikuvauksiin.
  <li><b>Vakiokävijät</b></li>
  Pilkulla eroteltu lista vakkarikävijöiden sähköpostiosoitteista.
</ul>
</div>

</div>
</body>
</html>
