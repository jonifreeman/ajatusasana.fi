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

function requiredV(s) {
  return s.val().trim().length > 0
}

function validateField(field, validators) {
  var isValid = _.every(_.map(validators, function(v) {
    return v(field)
  }))
  if (isValid) {
    field.removeClass('invalid')
  } else {
    field.addClass('invalid')
  }
}

function setupAddAppointmentPopup() {
  var $container = $('.add-appointment-popup')

  function start() { return $container.find('.start') }
  function name() { return $container.find('.name') }
  function email() { return $container.find('.email') }
  function phone() { return $container.find('.phone') }
  function comment() { return $container.find('.comment') }

  function validate() {
    if (requiredV(name()) && requiredV(email()) && requiredV(phone())) {
      $container.find('.add-appointment-button').removeAttr('disabled')
    } else {
      $container.find('.add-appointment-button').attr('disabled', 'disabled')
    }
  }

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
      $container.find('.main-content').hide()
      $container.find('.success').fadeIn(500)
      if (containerData.onSuccess)
        containerData.onSuccess()
    })
    .fail(function(err) {
      if (err.status == 409) {
        $('#calendar').fullCalendar('refetchEvents')
        $container.find('.main-content').hide()
        $container.find('.duplicate-booking').show()
      } else {
        $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
      }
    })
  })

  return setupPopup($container, validate, {start: start})
}

function setupEditTimePopup() {
  var $container = $('.add-time-slot-popup')

  function start() { return $container.find('.start') }
  function end() { return $container.find('.end') }

  function timeV(t) {
    var re = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/
    var parts = t.val().split(':')
    return t.val().length == 0 || (re.test(t.val().trim()) && parseInt(parts[1]) % 15 == 0)
  }

  function validate() {
    if (requiredV(start()) && requiredV(end()) && timeV(start()) && timeV(end())) {
      $container.find('.add-time-slot-button').removeAttr('disabled')
    } else {
      $container.find('.add-time-slot-button').attr('disabled', 'disabled')
    }

    validateField(start(), [timeV])
    validateField(end(), [timeV])
  }

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
    $container.find('.main-content').show()
    $container.find('.error').hide()
    $container.find('.success').hide()
    setTimeout(function() {
      $container.find('input:nth(0)').focus()
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
