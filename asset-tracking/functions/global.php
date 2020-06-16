<?php  
/*In this document all global functions are listed. these are functions that are used on multiple pages. */

// include db file to setup database connection
include_once 'db.php';
// give var $database the connection info from db.php
$database = db_connect();

// Function loginUser is used to login user on page 'login.php' and 'index.php' the function is used to login users and send them to the admin or user page.
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
// function logOut is used on every page. The function is triggered when user presses "logout" and destroys user session and sends user to login page.
function logOut(){
  // destroy session
  session_destroy();
  unset($_SESSION['email']);
  // redirect to login page
  header("location: index.php");
}
// function checkSessionLevel is used to check the session level of a user and check if user is on the right page. the function checks if admin is not on user page. 
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
// function checkSessionUser is used to check the session of user. it is checking if there are session variables set. 
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
// function checkSessionAdmin is used to check the session of admin. it is checking if there are session variables set. 
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
// the function getUser is used to get the basic information form the logged in user.
function getUser($database, $User_ID){
  // request basic info from user.
  $result_users = $database->prepare("SELECT * FROM user WHERE ID = ".$User_ID);
  $result_users->execute();
  for($i=0; $row = $result_users->fetch(); $i++){
    $id = $row['ID'];
    $email = $row['email'];
    $password = $row['password']; 
  } 
  return $email;
}
// function updateprofile is used on the home page of the user and on the homepage of the admin. the function updates the profile when update form is submitted 
function updateProfile($database, $User_ID){
  // set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
  // in the end there will be a check if error is 0 if not show error message.
  $error = 0;
  // check in database if the email is already in use
  $email = htmlspecialchars($_POST['email']);
  $query = "SELECT * FROM user WHERE email = :email AND ID !=:ID LIMIT 1";
  $stmt = $database->prepare($query);
  $results = $stmt->execute(array(":email" => $email, ":ID" => $User_ID));
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user) { // if user exists
      if ($user['email'] == $email) {
        $error++;
          $errorMessage= "User already excist";
      }
  } 
  // if email is empty
  if (!empty($_POST['email'])){
      $email = htmlspecialchars($_POST['email']);
  }else{
      $error++;
      $errorMessage = "E-mail is leeg";
  }
  // if password_1 is empty
  if (!empty($_POST['password_1'])){
      $password_1 = htmlspecialchars($_POST['password_1']);
  }else{
      $error++;
      $errorMessage = "Password is empty";
  }
  // if password_2 is empty
  if (!empty($_POST['password_2'])){
      $password_2 = htmlspecialchars($_POST['password_2']);
  }else{
      $error++;
      $errorMessage = "Please confirm the password";
  }
  // if password_1 and password_2 is not 10 characters
  if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMessage= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
    // make variable $password_3 (hash variant of $password_1) 
    // from here $password_3 will be used.
    if($password_1 == $password_2){
      $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMessage= "Password needs to be the same";
    }
    // if there are no errors proceed
  if ($error == 0) {
    // update user data
      $query = "UPDATE user SET email=:email, password=:password_3 WHERE ID =:ID";
      
      $stmt = $database->prepare($query);

      $stmt->bindValue(":ID", $User_ID, PDO::PARAM_STR);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->bindValue(":password_3", $password_3, PDO::PARAM_STR);

      try {
          $stmt->execute();
      }
      catch (PDOException $e) {
          echo $e->getMessage();
      }
      // all the data is handled succesfully send user to assets.php.
      header('location: assets.php');

  }else{?>
    <!-- error was not 0 so there was a error -->
    <!-- show a html box that will contain the specified erromessage -->
      <div class="alert">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
          <strong>Let op!</strong> <?php echo $errorMessage ?>
      </div><?php
  }
}
function selectUserAssets($database){
      $result_trackerID = $database->prepare("SELECT * FROM asset where user_ID = ".$_SESSION['id']);

    $result_trackerID->execute();
    // loop database results
    if ($result_trackerID->rowCount() > 0) {
      while ($trackerID_row = $result_trackerID->fetch(PDO::FETCH_LAZY)) {
        $trackerID[] = $trackerID_row['trackerID'];
      }
    } else {
       $trackerID = null;
    }
    return $trackerID;
}
function selectAllAssets($database){
  $result_trackerID = $database->prepare("SELECT * FROM asset");

  $result_trackerID->execute();
  // loop database results
  while ($trackerID_row = $result_trackerID->fetch(PDO::FETCH_LAZY)) {
    $trackerID[] = $trackerID_row['trackerID'];
  }
  return $trackerID;
}