$(function() {

  $('#calendar').fullCalendar({
    lang: 'fi',
    events: 'times.php',
    defaultView: 'agendaWeek',
    minTime: '8:00:00',
    maxTime: '22:00:00',
    allDaySlot: false,
    height: 'auto',
    eventDataTransform: function(event) {
      if (event.className == 'time') {
        event.rendering = 'background'
      }
      return event
    },
    eventRender: function(event, element) {
      if (event.className == 'enrollment') {
        element.qtip({
          content: '<p>' + event.comment + '</p><p>' + event.email + '</p><p>' + event.phone + '</p>'
        })
      }
    }
  })


})
