$(function() {

  $('.schedule').on('click', '.save', function(e) {
    var groupClass = $(e.currentTarget).parents('.group_class')
    var fields = groupClass.find('input, textarea').serializeArray()
    var data = {id: groupClass.attr('data-id') ? parseInt(groupClass.attr('data-id')) : undefined}
    for (var i = 0, len = fields.length; i < len; i++) {
      var field = fields[i]
      var name = field.name.indexOf('type') == -1 ? field.name : 'class_type'
      data[name] = field.value
    }
    $.post("update_schedule.php", data, function() {
    })
  })
  
})
