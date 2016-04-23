$(function() {

  $('#calendar').fullCalendar({
    lang: 'fi',
    events: 'times.php',
    defaultView: 'agendaWeek',
    minTime: '8:00:00',
    maxTime: '22:00:00',
    eventRender: function(event, element) {
      if (event.className == 'enrollment') {
        element.qtip({
          content: '<p>' + event.comment + '</p><p>' + event.email + '</p><p>' + event.phone + '</p>'
        })
      }
    }
  })


})
