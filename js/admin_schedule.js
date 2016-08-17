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
      /*
      var times = ['<option class="time" value="">Valitse ajankohta</option>']
      var first = calEvent.start.clone()
      var last = calEvent.end.clone().subtract(90, 'minutes')
      for (i = first; i <= last; first.add(15, 'minutes')) {
        var t = i.format('HH:mm')
        var range = t + ' - ' + i.clone().add(90, 'minutes').format('HH:mm')
        times.push('<option class="time" value="' + t + '">' + range + '</option>')
      }
      addAppointmentPopup.formFields.start().html(times.join(''))
      addAppointmentPopup.formFields.date().html(calEvent.start.format('dd D.M'))

      addAppointmentPopup.open(jsEvent, {
        date: calEvent.start,
        onSuccess: function() {
          $('#calendar').fullCalendar('refetchEvents')
        }
      })
*/
    }
  })

})
