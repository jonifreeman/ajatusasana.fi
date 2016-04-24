$(function() {

  if (!isAdmin()) {
    var addAppointmentPopup = setupAddAppointmentPopup()
    $('#calendar').fullCalendar({
      lang: 'fi',
      events: 'times.php?vacant=true',
      defaultView: 'agendaWeek',
      minTime: '8:00:00',
      maxTime: '22:00:00',
      allDaySlot: false,
      height: 'auto',
      eventRender: function(event, element) {
        element.find('.fc-time').hide()
      },
      eventClick: function(calEvent, jsEvent, view) {
        var times = []
        var first = calEvent.start.clone()
        var last = calEvent.end.clone().subtract(90, 'minutes')
        for (i = first; i <= last; first.add(15, 'minutes')) {
          var t = i.format('HH:mm')
          var range = t + ' - ' + i.clone().add(90, 'minutes').format('HH:mm')
          times.push('<option class="time" value="' + t + '">' + range + '</option>')
        }
        addAppointmentPopup.formFields.start().html(times.join(''))

        addAppointmentPopup.open(jsEvent, {
          date: calEvent.start,
          onSuccess: function() {
            // TODO: thank you message
            $('#calendar').fullCalendar('refetchEvents')
          }
        })
      }
    })
  } else {
    $('.logout').show()
    $('.logout .logout-button').click(function() {
      document.cookie = 'session_id=; expires=Thu, 01 Jan 1970 00:00:01 GMT;'
      window.location = 'ajanvaraus.html'
    })

    var editTimePopup = setupEditTimePopup()
    $('#calendar').fullCalendar({
      lang: 'fi',
      events: 'times.php',
      defaultView: 'agendaWeek',
      minTime: '8:00:00',
      maxTime: '22:00:00',
      allDaySlot: false,
      height: 'auto',
      eventRender: function(event, element) {
        if (event.className == 'enrollment') {
          element.qtip({
            content: '<p>' + event.comment + '</p><p>' + event.email + '</p><p>' + event.phone + '</p>'
          })
        }
      },
      dayClick: function(date, jsEvent, view) {
        editTimePopup.formFields.start().val(date.format('HH:mm'))
        editTimePopup.formFields.end().val('')
        editTimePopup.open(jsEvent, {
          date: date,
          onSuccess: function() {
            $('#calendar').fullCalendar('refetchEvents')
          }
        })
      },
      eventClick: function(calEvent, jsEvent, view) {
        if (calEvent.className.indexOf('time') != -1) {
          editTimePopup.formFields.start().val(calEvent.start.format('HH:mm'))
          editTimePopup.formFields.end().val(calEvent.end.format('HH:mm'))
          editTimePopup.open(jsEvent, {
            id: calEvent.id,
            date: calEvent.start,
            end: calEvent.end,
            onSuccess: function() {
              $('#calendar').fullCalendar('refetchEvents')
            }
          })
        }
      }
    })
  }

})

function isAdmin() {
  return getCookie('session_id') != ''
}

function setupAddAppointmentPopup() {
  function validate() {
    // TODO: implement
  }

  var $container = $('.add-appointment-popup')
  function start() { return $container.find('.start') }
  function name() { return $container.find('.name') }
  function email() { return $container.find('.email') }
  function phone() { return $container.find('.phone') }
  function comment() { return $container.find('.comment') }

  $container.find('.add-appointment-button').click(function(e) {
    var containerData = $container.data().data
    var date = containerData.date
    var s = date.clone().time(start().val())
    data = {
      start: s.format(),
      name: name().val(),
      email: email().val(),
      phone: phone().val(),
      comment: comment().val()
    }
    $.post("enrollments.php", data, function() {
      $container.css('visibility', 'hidden')
      if (containerData.onSuccess)
        containerData.onSuccess()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  return setupPopup($container, validate, {start: start})
}

function setupEditTimePopup() {
  function validate() {
    // TODO: implement
  }

  var $container = $('.add-time-slot-popup')

  function start() { return $container.find('.start') }
  function end() { return $container.find('.end') }

  $container.find('.add-time-slot-button').click(function(e) {
    var containerData = $container.data().data
    var date = containerData.date
    var s = date.clone().time(start().val())
    var e = date.clone().time(end().val())
    data = {
      id: containerData.id,
      start: s.format(),
      end: e.format()
    }
    $.post("times.php", data, function() {
      $container.css('visibility', 'hidden')
      if (containerData.onSuccess)
        containerData.onSuccess()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  return setupPopup($container, validate, {start: start, end: end})
}

function setupPopup($container, validate, formFields) {
  var open = function(e, containerData) {
    $container.data('data', containerData)
    setTimeout(function() {
      $container.find('input:nth(1)').focus()
    }, 0)
    validate()
    $('.popup').css('visibility', 'hidden')
    $container.offset($(e.currentTarget).offset())
    $container.css('visibility', 'visible')
  }

  $container.find('input').bind("keyup blur", function(event) {
    validate()
  })

  $container.find('.close').click(function() {
    $container.css('visibility', 'hidden')
  })

  return {open: open, formFields: formFields}
}

function getCookie(cname) {
  var name = cname + "="
  var ca = document.cookie.split(';')
  for (var i = 0; i <ca.length; i++) {
    var c = ca[i]
    while (c.charAt(0) == ' ') {
      c = c.substring(1)
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length,c.length);
    }
  }
  return ""
}
