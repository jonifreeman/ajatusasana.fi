$(function() {

  var i = document.location.pathname.lastIndexOf("/")
  var page = document.location.pathname.substring(i+1)
  if (page == "") {
    $('.navi a[href$="index.html"]').addClass('selected')
  } else {
    $('.navi a[href$="' + page + '"]').addClass('selected')
  }

  $('.images').cycle({
    timeout: 10000
  })

  setupSignup()
  setupMailinglist()

  var menu = $('.navi ul')

  $('.navi .pull').on('click', function(e) {
    e.preventDefault()
    menu.slideToggle()
  })

  $('#ananda').cycle({ 
    fx:     'fade',
    speed:  'fast',
    timeout: 0,
    next:   '#ananda-next',
    prev:   '#ananda-prev'
  });
})

function formatDate(date) {
  var parts = date.split('-')
  var month = parts[1]
  var day = parts[2]
  return day + '.' + month
}

function formatTime(t) {
  var parts = t.split(':')
  return parts[0] + ':' + parts[1]
}

function setupSignup() {
  $('.schedule-container').on('click', '.signup', function(e) {
    var id = $(e.currentTarget).attr('data-id')
    $.get('/signup.php?course=' + id, function(classes) {
      $('.popup').hide()
      var time = $(e.currentTarget).parent('div').find('strong').text()
      var course = $(e.currentTarget).next().text()
      $('.signup-popup .course').text(time + " " + course)
      $('.signup-popup .course-id').val(id)
      $('.signup-popup').show()
      $('.signup-popup').find('.main-content').show()
      $('.signup-popup .class-info').show()
      $('.signup-popup').find('.error').hide()
      $('.signup-popup').find('.success').hide()
      var savedData = loadNameAndEmail()
      $('.signup-popup').find('.name').val(savedData.name)
      $('.signup-popup').find('.email').val(savedData.email)

      var classHtml = $('.signup-popup').find('.classes')
      classHtml.html('')
      for (var i = 0; i < classes.length; ++i) {
        var group_class = classes[i]
        var dateString = group_class.start_time ? (formatDate(group_class.date) + " klo " + formatTime(group_class.start_time) + "-" + formatTime(group_class.end_time)) : formatDate(group_class.date)
        var $row = $('<div class="available-class"><label><input type="checkbox" value="' + group_class.date + '"></input>' + dateString + '</label><span class="booking-attention"></span><span class="cancellation-reason"></span><span class="date-info">' + (group_class.info || '') + '</span></div>')
        if (group_class.hide) {
          $row.addClass("hidden")
        }
        else if (group_class.cancelled) {
          $row.addClass("disabled")
          $row.find('input').attr("disabled", true)
          $row.find('.cancellation-reason').text(group_class.reason || '')
          $row.find('.booking-attention').remove()
        }
        else if (group_class.available < 1) {
          $row.addClass("disabled")
          $row.find('input').attr("disabled", true)
          $row.find('.booking-attention').text('Täynnä')
        }
        else if (group_class.available < 3) {
          $row.find('.booking-attention').text('Paikkoja jäljellä: ' + group_class.available)
        }
        if (! $row.hasClass('disabled') && i == 0) {
          $row.find('input').prop('checked', true)
        }
        classHtml.append($row)
        if (group_class.class_type == 'course') {
          if (! $row.hasClass('disabled')) {
            $row.find('input').prop('checked', true)
          }
          $row.find('label').hide()
          $('.signup-popup .class-info').hide()
          break;
        }
      }
      validate()
    })
  })

  $('.schedule .cancelled').click(function(e) {
    $('.popup').hide()
    $('.cancelled-popup').show()
  })

  function name() { return $('.signup-popup .name').val() }
  function email() { return $('.signup-popup .email').val() }
  function phone() { return $('.signup-popup .phone').val() }
  function dates() {
    return $('.signup-popup .available-class input:checked').map(function() {
      return $(this).val();
    }).get()
  }

  function validate() {
    if (name().length == 0 || email().length == 0 || dates().length == 0) {
      $('.signup-popup .signup-button').attr('disabled', 'disabled')
    } else {
      $('.signup-popup .signup-button').removeAttr('disabled')
    }

    if (name().length == 0) {
      $('.signup-popup .name').addClass('invalid')
    } else {
      $('.signup-popup .name').removeClass('invalid')
    }

    if (email().length == 0 && phone().length == 0) {
      $('.signup-popup .email').addClass('invalid')
      $('.signup-popup .phone').addClass('invalid')
    } else {
      $('.signup-popup .email').removeClass('invalid')
      $('.signup-popup .phone').removeClass('invalid')
    }
  }

  $('.signup-popup').on("keyup blur change", "input", function(event) {
    validate()
  })

  $('.signup-popup .signup-button').click(function(e) {
    data = {
      course:  parseInt($('.signup-popup .course-id').val()),
      name:    name(),
      email:   email(),
      phone:   phone(),
      dates:   dates().join(',')
    }
    saveNameAndEmail(data.name, data.email)
    $.post("/signup.php", data, function() {
      $('.signup-popup').find('.main-content').hide()
      $('.signup-popup-ok').fadeIn(500)
    })
    .fail(function() {
      $('.signup-popup .error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  $('.signup-popup .close').click(function() {
    $('.signup-popup').hide()
  })

  $('.cancelled-popup .close').click(function() {
    $('.cancelled-popup').hide()
  })
}

function setupMailinglist() {
  function name() { return $('.mailinglist-form .name').val() }
  function email() { return $('.mailinglist-form .email').val() }

  function validate() {
    if (name().length == 0 || email().length == 0) {
      $('.mailinglist-form .join-button').attr('disabled', 'disabled')
    } else {
      $('.mailinglist-form .join-button').removeAttr('disabled')
    }

    if (name().length == 0) {
      $('.mailinglist-form .name').addClass('invalid')
    } else {
      $('.mailinglist-form .name').removeClass('invalid')
    }

    if (email().length == 0) {
      $('.mailinglist-form .email').addClass('invalid')
    } else {
      $('.mailinglist-form .email').removeClass('invalid')
    }
  }

  $('.mailinglist-form input').bind("keyup blur", function(event) {
    validate()
  })

  $('.mailinglist-form .join-button').click(function(e) {
    data = {
      name:  name(),
      email: email(),
    }
    $.post("/join.php", data, function() {
      $('.mailinglist-form').hide()
      $('.mailinglist-ok').fadeIn(500).delay(10000).fadeOut(500)
    })
    .fail(function() {
      $('.mailinglist-form .error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })
}

function saveNameAndEmail(name, email) {
  if (typeof (window.localStorage) != 'undefined') {
    localStorage.setItem('user-data', JSON.stringify({name: name, email: email}))
  }
}

function loadNameAndEmail() {
  if (typeof (window.localStorage) != 'undefined') {
    var data = localStorage.getItem('user-data')
    if (data) {
      try {
        return JSON.parse(data)
      } catch (err) {}
    }
  }
  return {name: '', email: ''}
}
