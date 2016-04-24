$(function() {

  var editTimePopup = setupEditTimePopup()

  $('#calendar').fullCalendar({
    lang: 'fi',
    events: 'times.php',
    defaultView: 'agendaWeek',
    minTime: '8:00:00',
    maxTime: '22:00:00',
    allDaySlot: false,
    height: 'auto',
    eventDataTransform: function(event) {
      return event
    },
    eventRender: function(event, element) {
      if (event.className == 'enrollment') {
        element.qtip({
          content: '<p>' + event.comment + '</p><p>' + event.email + '</p><p>' + event.phone + '</p>'
        })
      }
    },
    dayClick: function(date, jsEvent, view) {
      editTimePopup.open(jsEvent, {
        date: date,
        onSuccess: function() {
          $('#calendar').fullCalendar('refetchEvents')
        }
      })
    },
    eventClick: function(calEvent, jsEvent, view) {
      if (calEvent.className.indexOf('time') != -1) {
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

})

// TODO: prefill start on opening
function setupEditTimePopup() {
  function validate() {
    // TODO: implement
  }

  var $container = $('.add-time-slot-popup')

  function id() { return $container.find('.id').text() }
  function start() { return $container.find('.start').val() }
  function end() { return $container.find('.end').val() }

  $container.find('.add-time-slot-button').click(function(e) {
    var containerData = $container.data().data
    var date = containerData.date
    var s = date.clone().time(start())
    var e = date.clone().time(end())
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

  return setupPopup($container, validate)
}

function setupPopup($container, validate) {
  var open = function(e, containerData) {
    $container.data('data', containerData)
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

  return {open: open}
}
