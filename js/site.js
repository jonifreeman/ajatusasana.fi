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

  $('.signup').click(function(e) {
    console.log($(e.currentTarget).next())
    $('.signup-popup').offset($(e.currentTarget).offset())
    $('.signup-popup').css('visibility', 'visible')
  })

  $('.signup-popup .signup-button').click(function(e) {
    data = {
      course:  $(e.currentTarget).next().text(),
      name:    $('.signup-popup .name').val(),
      contact: $('.signup-popup .contact').val()
    }
    console.log(data)
    $.post("signup.php", data, function() {
      $('.signup-popup').css('visibility', 'hidden')
    })
    .fail(function() {
      $('.signup-popup .error').fadeIn(500).delay(10000).fadeOut(500)
    })
  })

  $('.signup-popup .close').click(function() {
    $('.signup-popup').css('visibility', 'hidden')
  })
})
