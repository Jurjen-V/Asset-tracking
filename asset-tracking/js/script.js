// open form inlog page
function openForm() {
  document.getElementById("myForm").style.display = "block";
}
// close form inlog page
function closeForm() {
  document.getElementById("myForm").style.display = "none";
}
document.addEventListener('DOMContentLoaded', function() {
    // trigger modal
    var modals = document.querySelectorAll('.modal');
    var modal = M.Modal.init(modals);
    // trigger dropdown in nav
    var triggers = document.querySelectorAll('.dropdown-trigger');
    var trigger = M.Dropdown.init(triggers, {coverTrigger: false});
    // trigger mobile nav
    var sidenavs = document.querySelectorAll('.sidenav');
    var sidenav = M.Sidenav.init(sidenavs);
    // trigger collapsible in mobile nav
    var collapsibles = document.querySelectorAll('.collapsible');
    var collapsible = M.Collapsible.init(collapsibles);
  });
// make table row link to route overview page
jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});