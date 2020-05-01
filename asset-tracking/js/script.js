// open form inlog page
function openForm() {
    document.getElementById("myForm").style.display = "block";
    document.getElementById("info").style.display = "block";
    document.getElementById("map").style.height = "calc(100% - 52.5px)"; 
    map.dragging.disable();
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.scrollWheelZoom.disable();
}
// close form inlog page
function closeForm() {
    document.getElementById("myForm").style.display = "none";
    document.getElementById("info").style.display = "none";
    document.getElementById("map").style.height = "100%"; 
    map.dragging.enable();
    map.touchZoom.enable();
    map.doubleClickZoom.enable();
    map.scrollWheelZoom.enable();
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