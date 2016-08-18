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
      $('#calendar').fullCalendar('refetchEvents')
    })
  })

  var groupClassPopup = setupGroupClassPopup()

  $('#calendar').fullCalendar({
    lang: 'fi',
    events: 'bookings.php',
    defaultView: 'agendaWeek',
    minTime: '8:00:00',
    maxTime: '22:00:00',
    allDaySlot: false,
    height: 'auto',
    //eventRender: function(event, element) {
    //  element.find('.fc-time').hide()
    //},
    eventClick: function(calEvent, jsEvent, view) {
      var id = calEvent.id
      $.get('group_class.php?id=' + id, function(groupClass) {
        groupClassPopup.open(jsEvent, {
          id: id,
          date: calEvent.start.format('YYYY-MM-DD'),
          onSuccess: function() {
            $('#calendar').fullCalendar('refetchEvents')
          }
        })
      })
    }
  })

})

function setupGroupClassPopup() {
  var $container = $('.group-class-popup')

  function reason() { return $container.find('.reason') }

  function validate() {
  }

  $container.find('.cancel-group-class-button').click(function(e) {
    var containerData = $container.data().data
    data = {
      id: containerData.id,
      date: containerData.date,
      reason: reason().val()
    }
    $.post("cancel_group_class.php", data, function() {
      $container.hide()
      if (containerData.onSuccess)
        containerData.onSuccess()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  return setupPopup($container, validate, {})
}

function setupPopup($container, validate, formFields) {
  var open = function(e, containerData) {
    $container.data('data', containerData)
    $container.find('.main-content').show()
    $container.find('.error').hide()
    $container.find('.success').hide()
    setTimeout(function() {
      $container.find('input:nth(0)').focus()
    }, 0)
    validate()
    $('.popup').hide()
    $container.show()
  }

  $container.find('select').bind("change", function(event) {
    validate()
  })
  $container.find('input').bind("keyup blur change", function(event) {
    validate()
  })

  $container.find('.close').click(function() {
    $container.hide()
  })

  return {open: open, formFields: formFields}
}
