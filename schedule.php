<?php

include ('common.php');

function query_group_classes() {
  $sql = function($conn) {
    return "SELECT *, HOUR(start_time) as start_time_hour, 'session' as 'session_type' FROM group_class WHERE NOW() >= display_start AND (NOW() < end OR end IS NULL) AND class_type != 'miniretreat'";
  };
  return sql_query($sql);
}

function format_time($t) {
  return str_replace(":", ".", substr($t, 0, 5));
}

$weekdays = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
// create array of arrays binned by start time: 8, 11, 14, 18
$bins = array(18, 14, 11, 8);
$all_group_classes = query_group_classes();
array_push($all_group_classes, $miniretreat);
$group_classes_bins = array();
foreach ($bins as $bin) {
  $bin_array = array();
  foreach ($all_group_classes as $group_class_key => $group_class) {
    if ($group_class['start_time_hour'] >= $bin) {
      array_push($bin_array, $group_class);
      unset($all_group_classes[$group_class_key]);
    }
  }
  array_unshift($group_classes_bins, $bin_array);
}

header('Content-Type: text/html; charset=utf-8');
?>


<table class="schedule">
<thead>
<tr>
 <th>Ma</th>
 <th>Ti</th>
 <th>Ke</th>
 <th>To</th>
 <th>Pe</th>
 <th>La</th>
 <th>Su</th>
</tr>
</thead>

<tbody>
<?php foreach ($group_classes_bins as $group_classes): ?>
<tr>
<?php foreach ($weekdays as $day): ?>
 <td>
<?php foreach ($group_classes as $group_class): ?>
<?php if ($group_class['day'] == $day): ?>
 <div class="<?= $group_class['session_type'] ?>">
  <?php if ($group_class['start_time']): ?>
  <strong><?= format_time($group_class['start_time']) ?> - <?= format_time($group_class['end_time']) ?></strong>
  <? endif; ?>
  <?php if ($group_class['session_type'] == 'course' || $group_class['max_size'] > 0): ?>
  <img data-id="<?= $group_class['id'] ?>" class="signup" src="/img/signup.png"></img>
  <? endif; ?>
  <div>
    <a href="#<?= $group_class['anchor'] ?>"><?= $group_class['name'] ?></a>
    <?php if ($group_class['highlight']): ?>
    <div class="highlight"><?= $group_class['highlight'] ?></div>
    <? endif; ?>
  </div>
 </div>
<?php endif; ?>
<?php endforeach; ?>
 </td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>

</tbody>
</table>
