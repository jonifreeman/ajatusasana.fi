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

// TODO access control

header('Content-Type: text/html; charset=utf-8');
?>

<table>
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
    <tr>
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
      <td><textarea name="name"><?= $group_class['regulars'] ?></textarea></td>
    </tr>
    <?php endforeach; ?>
  </tbody>

</table>