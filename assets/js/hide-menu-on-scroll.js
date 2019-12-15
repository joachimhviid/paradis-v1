var prevScrollpos = window.pageYOffset;
window.onscroll = () => {
  var currentScrollPos = window.pageYOffset;
  document.getElementById("topbar").style.top = ((prevScrollpos > currentScrollPos) ? "0" : "-70px");
  prevScrollpos = currentScrollPos;
}