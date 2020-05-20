// open form inlog page
function openForm() {
    // when open form button is pressed show the form and disable zoom functions
    document.getElementById("myForm").style.display = "block";
    map.dragging.disable(); //disable dragging on map
    map.touchZoom.disable(); // disable touch zooming on map
    map.doubleClickZoom.disable(); // disable double click zoom on map
    map.scrollWheelZoom.disable(); // disable scroll zoom on map
}
// close form inlog page
function closeForm() {
    // when close form button is pressed hide the form and enable zoom functions
    document.getElementById("myForm").style.display = "none";
    map.dragging.enable(); //enable dragging on map
    map.touchZoom.enable();// enable touch zooming on map 
    map.doubleClickZoom.enable();// enable double click zoom on map
    map.scrollWheelZoom.enable();// enable scroll zoom on map
}
// eventhandlers for materialize triggers and dropdowns
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
// close function for alert boxes
function Close(){
    // close alert box 
    var close = document.getElementsByClassName("closebtn");
    var i;

    for (i = 0; i < close.length; i++) {
        close[i].onclick = function(){
            var div = this.parentElement;
            div.style.opacity = "0";
            div.style.display = "none";
            document.getElementById("map").style.height = "100%"; 
        }
    }    
}
