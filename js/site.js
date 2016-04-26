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

function setupSignup() {
  $('.schedule .signup').click(function(e) {
    validate()
    $('.popup').css('visibility', 'hidden')
    var time = $(e.currentTarget).parent('div').find('strong').text()
    var course = $(e.currentTarget).next().text()
    $('.signup-popup .course').text(time + " " + course)
    $('.signup-popup').css('visibility', 'visible')
  })

  $('.schedule .cancelled').click(function(e) {
    $('.popup').css('visibility', 'hidden')
    $('.cancelled-popup').css('visibility', 'visible')
  })

  function name() { return $('.signup-popup .name').val() }
  function email() { return $('.signup-popup .email').val() }
  function phone() { return $('.signup-popup .phone').val() }

  function validate() {
    if (name().length == 0 || (email().length == 0 && phone().length == 0)) {
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

  $('.signup-popup input').bind("keyup blur", function(event) {
    validate()
  })

  $('.signup-popup .signup-button').click(function(e) {
    data = {
      course:  $('.signup-popup .course').text(),
      name:    name(),
      email:   email(),
      phone:   phone()
    }
    $.post("signup.php", data, function() {
      $('.signup-popup').css('visibility', 'hidden')
      window.scrollTo(0, 0)
      $('.signup-popup-ok').fadeIn(500).delay(10000).fadeOut(500)
    })
    .fail(function() {
      $('.signup-popup .error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  $('.signup-popup .close').click(function() {
    $('.signup-popup').css('visibility', 'hidden')
  })

  $('.cancelled-popup .close').click(function() {
    $('.cancelled-popup').css('visibility', 'hidden')
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
