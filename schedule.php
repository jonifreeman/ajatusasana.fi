<?php

include ('common.php');

// TODO use start + end
function query_group_classes() {
  $sql = function($conn) {
    return "SELECT *, HOUR(start_time) as start_time_hour, 'session' as 'session_type' FROM group_class WHERE is_saturday_miniretreat is false";
  };
  return sql_query($sql);
}

// TODO add miniretreat placeholder

$weekdays = array('ma', 'ti', 'ke', 'to', 'pe', 'la', 'su');
// create array of arrays binned by start time: 8, 11, 14, 18
$bins = array(18, 14, 11, 8);
$all_group_classes = query_group_classes();
$group_classes_bins = array();
foreach ($bins as $bin) {
  $bin_array = array();
  foreach ($all_group_classes as $group_class_key => $group_class) {
    if ($group_class['start_time_hour'] > $bin) {
      array_push($bin_array, $group_class);
      unset($all_group_classes[$group_class_key]);
    }
  }
  array_unshift($group_classes_bins, $bin_array);
}
?>


<div class="schedule-container">
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
<?php foreach ($weekdays as $day): ?>
<tr>
<?php foreach ($group_classes_bins as $group_classes): ?>
<?php foreach ($group_classes as $group_class): ?>
 <td>
<?php if ($group_class['day'] == $day): ?>
 <div class="<?= $group_class['session_type'] ?>">
  <strong><?= $group_class['start_time'] ?> - <?= $group_class['end_time'] ?></strong> <br /> <img class="signup" src="/img/signup.png"></img>
  <a href="#<?= $group_class['anchor'] ?>"><?= $group_class['name'] ?></a>
 </div>
<?php endif; ?>
 </td>
<?php endforeach; ?>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
