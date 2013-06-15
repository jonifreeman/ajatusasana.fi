$(function() {

  var i = document.location.pathname.lastIndexOf("/")
  var page = document.location.pathname.substring(i+1)
  if (page == "") {
    $('.navi a:first').addClass('selected')
  } else {
    $('.navi a[href$="' + page + '"]').addClass('selected')
  }

})