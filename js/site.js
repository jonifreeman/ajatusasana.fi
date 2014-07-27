$(function() {

  var i = document.location.pathname.lastIndexOf("/")
  var page = document.location.pathname.substring(i+1)
  if (page == "") {
    $('.navi a:first').addClass('selected')
  } else {
    $('.navi a[href$="' + page + '"]').addClass('selected')
  }

  $('.images').cycle({
    timeout: 10000
  })

  $('.schedule .signup').click(function(e) {
    validate()
    var time = $(e.currentTarget).parent('div').find('strong').text()
    var course = $(e.currentTarget).next().text()
    $('.signup-popup .course').text(time + " " + course)
    $('.signup-popup').offset($(e.currentTarget).offset())
    $('.signup-popup').css('visibility', 'visible')
  })

  function name() { return $('.signup-popup .name').val() }
  function contact() { return $('.signup-popup .contact').val() }

  function validate() {
    if (name().length == 0 || contact().length == 0) {
      $('.signup-popup .signup-button').attr('disabled', 'disabled')
    } else {
      $('.signup-popup .signup-button').removeAttr('disabled')
    }

    if (name().length == 0) {
      $('.signup-popup .name').addClass('invalid')
    } else {
      $('.signup-popup .name').removeClass('invalid')
    }

    if (contact().length == 0) {
      $('.signup-popup .contact').addClass('invalid')
    } else {
      $('.signup-popup .contact').removeClass('invalid')
    }
  }

  $('.signup-popup input').bind("keyup blur", function(event) {
    validate()
  })

  $('.signup-popup .signup-button').click(function(e) {
    data = {
      course:  $('.signup-popup .course').text(),
      name:    name(),
      contact: contact()
    }
    $.post("signup.php", data, function() {
      $('.signup-popup').css('visibility', 'hidden')
      $('.signup-popup-ok').fadeIn(500).delay(10000).fadeOut(500)
    })
    .fail(function() {
      $('.signup-popup .error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  $('.signup-popup .close').click(function() {
    $('.signup-popup').css('visibility', 'hidden')
  })
})
