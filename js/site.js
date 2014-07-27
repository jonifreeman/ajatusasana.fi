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
    $('.signup-popup').offset($(e.currentTarget).offset())
    $('.signup-popup').css('visibility', 'visible')
  })

  $('.signup-popup .signup-button').click(function() {
    $('.signup-popup').css('visibility', 'hidden')
  })

  $('.signup-popup .close').click(function() {
    $('.signup-popup').css('visibility', 'hidden')
  })
})
