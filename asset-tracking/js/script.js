// open form inlog page
function openForm() {
  $("#myForm").css({ display: "block" });
}
// close form inlog page
function closeForm() {
  $("#myForm").css({ display: "none" });
}
$(document).ready(function(){
    $('.modal').modal();
    $('.dropdown-trigger').dropdown();
    $('.sidenav').sidenav();
    $('.collapsible').collapsible();
    const Url ='https://api.opencagedata.com/geocode/v1/json?q=53.281844,5.268157&key=5b104f01c9434e3dad1e2d6a548445da&language=nl&pretty=1';
    $('.btn').click(function(){
    	$.ajax({
    		url: Url,
    		type: "GET",
    		succes: function(result){
    			console.log(result);
    		},
    		error: function(error){
    			console.log('error ${error}')
    		}
    	})
    });
});