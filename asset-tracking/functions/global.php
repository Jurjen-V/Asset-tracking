<?php  
// include db file
include_once 'db.php';

$database = db_connect();

function loginUser($database, $IOS){
  // set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
  // in the end there will be a check if error is 0 if not show error message.
  $error = 0;
  // check if input fields are filled in
  if(!empty($_POST['email'])){ // check if email is not empty
    $email = htmlspecialchars($_POST['email']); 
  }else{
    $error++;
    $errorMessage = "Email is leeg";
  }
  if(!empty($_POST['psw'])){ // check if password is not empty
    $enterd_password = htmlspecialchars($_POST['psw']);
  }else{
    $error++;
    $errorMessage = "Wachtwoord is leeg";
  }
  //check if password is correct.
  // Get Old Password from Database using the email addres to get the password form database
  $query ="SELECT * FROM user WHERE email =:email";
  $stmt = $database->prepare($query);
  $results = $stmt->execute(array(":email" => $email));
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  // variables that the user account with the email addres has.
  $id = $user['ID'];
  $email = $user['email'];
  $Level = $user['level'];
  $password= $user['password'];
  // compare the filled in password with the database password.
  if(password_verify($enterd_password, $password)){
  }else{
    $error++;
    $errorMessage= "Wachtwoorden zijn niet juist";
  }
  if($error == 0){
    // These variables will be used to make sure the user is logged in.
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $id;
    $_SESSION['level'] = $Level;
    $_SESSION['msg'] = "You are now logged in";
    if(isset($IOS)){
      header('location: index.php');
    }elseif($level === 0){
    }else{
      // If the user has a admin account 
      // send the user to admin page.
      header('location: admin.php');
    }
  }else{?>
    <!-- error was not 0 so there was a error -->
    <!-- show a html box that will contain the specified erromessage -->
    <div style="display: block" class="alert" id="alert">
      <span class="closebtn" onclick="Close()">&times;</span> 
      <strong>Let op!</strong> <?php echo $errorMessage ?>
    </div>
    <?php
  }
}
function logOut(){
  // destroy session
  session_destroy();
  unset($_SESSION['email']);
  // redirect to login page
  header("location: index.php");
}
function checkSessionLevel(){
  // If a admin account tries to get acces to regular user page
  // send the admin accounts to the admin page.
  if (!empty($_SESSION['level'])){ //if session is not set 
    if($_SESSION['level'] == 1) {
      $_SESSION['msg'] = "You belong at the admin page";
      header('location: admin.php');
    }
  }else{
    $_SESSION['level'] = 0;
  }
}
function checkSessionUser(){
  // if there is no session or level is 1 redirect user to login page
  if (empty($_SESSION['email']) || $_SESSION['level'] == 1) {
    $_SESSION['msg'] = "You must log in first";
      header('location: index.php');
  }
  // if session level is 1 redirect user to admin page
  if ($_SESSION['level'] == 1) {
    $_SESSION['msg'] = "You belong at the admin page";
      header('location: admin.php');
  }
}
function checkSessionAdmin(){
  // if there is no session or level is 1 redirect user to login page
  if(empty($_SESSION['email']) || $_SESSION['level'] == 0) {
    $_SESSION['msg'] = "You must log in first";
      header('location: index.php');
  }
  // if session level is 0 redirect user to user page
  if ($_SESSION['level'] == 0) {
    $_SESSION['msg'] = "You belong at the user page";
      header('location: index.php');
  }
  // if logout is pressed
  if (isset($_GET['logout'])) {
    // destroy session
    session_destroy();
      unset($_SESSION['email']);
      // redirect to login page
      header("location: index.php");
  }
}