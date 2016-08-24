$(function() {

  $('.admin-schedule').on('click', '.save', function(e) {
    var groupClass = $(e.currentTarget).parents('.group_class')
    var fields = groupClass.find('input, textarea').serializeArray()
    var data = {id: groupClass.attr('data-id') ? parseInt(groupClass.attr('data-id')) : undefined}
    for (var i = 0, len = fields.length; i < len; i++) {
      var field = fields[i]
      var name = field.name.indexOf('type') == -1 ? field.name : 'class_type'
      data[name] = field.value
    }
    $.post("/update_schedule.php", data, function(resp) {
      $('#calendar').fullCalendar('refetchEvents')
      if (_.isEmpty(groupClass.attr('data-id'))) {
        var $newTr = $('.admin-schedule tbody tr:last').clone()
        groupClass.attr('data-id', resp.id)
        groupClass.find('.save').val('Tallenna')
        $newTr.find('input[type="text"], textarea').val('')
        $('.admin-schedule tbody').append($newTr)
      }
      groupClass.find('td').addClass('highlight-row')
      setTimeout(function() { groupClass.find('td').removeClass('highlight-row') }, 2000)
    }).fail(function() {
      groupClass.find('td').addClass('highlight-row-error')
      setTimeout(function() { groupClass.find('td').removeClass('highlight-row-error') }, 2000)
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
      var date = calEvent.start.format('YYYY-MM-DD')
      $.get('/group_class.php?id=' + id + "&date=" + date, function(groupClass) {
        groupClassPopup.render(groupClass, date)
        groupClassPopup.open(jsEvent, {
          id: id,
          date: date,
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

  function repaint() {
    var containerData = $container.data().data
    var id = containerData.id
    var date = containerData.date
    $.get('/group_class.php?id=' + id + "&date=" + date, function(groupClass) {
      render(groupClass, date)
    })
  }

  $container.on('click', '.toggle-regular-cancellation', function(e) {
    var data = JSON.parse($(e.currentTarget).attr('data-cancellation'))
    $.post('/cancel_regular.php', data, function() {
      repaint()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  $container.on('click', '.booking-cancellation', function(e) {
    var data = JSON.parse($(e.currentTarget).attr('data-cancellation'))
    $.post('/cancel_booking.php', data, function() {
      repaint()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })
  
  function render(groupClass, date) {
    $container.find('.name').text(groupClass.name)
    var regularsHtml = _.map(groupClass.regulars, function(regular) {
      var isCancelled = _.find(groupClass.cancellations, {email: regular}) != undefined
      var cl = isCancelled ? 'cancelled-regular' : ''
      var cancellationData = {id: groupClass.id, email: regular, date: date}
      return '<li><span class="' + cl + '">' + regular + "</span><button data-cancellation='" + JSON.stringify(cancellationData) + "' class='toggle-regular-cancellation'>" + (isCancelled ? 'Palauta' : 'Peruuta') + '</button></li>'
    })
    $container.find('.regulars').html(regularsHtml.join('\n'))
    
    var bookingsHtml = _.map(groupClass.bookings, function(booking) {
      var cancellationData = {id: groupClass.id, email: booking.email, date: date}
      return '<li><span>' + booking.email + "</span><button class='booking-cancellation' data-cancellation='" + JSON.stringify(cancellationData) + "'>Peruuta</button></li>"
    })
    $container.find('.bookings').html(bookingsHtml.join('\n'))

    if (groupClass.is_cancelled) {
      $container.find('.cancel-group-class-info').hide()
      $container.find('.cancel-group-class-input').hide()
      $container.find('.cancel-group-class-button').val('Peruuta peruutus')
    } else {
      $container.find('.cancel-group-class-info').show()
      $container.find('.cancel-group-class-input').show()
      $container.find('.cancel-group-class-button').val('Peruuta tunti')
    }
  }

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
    $.post("/cancel_group_class.php", data, function() {
      repaint()
      containerData.onSuccess()
    })
    .fail(function() {
      $container.find('.error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  return _.extend({}, setupPopup($container, validate, {}), {render: render})
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
