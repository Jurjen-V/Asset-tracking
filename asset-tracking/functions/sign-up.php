<?php
include_once 'db.php';

$database = db_connect();

function signUp($database){
	// set error to 0 if a if statement is not succes the error var will increase by one. There will also be a specific errormessage assigned to the error.
    // in the end there will be a check if error is 0 if not show error message.
    $error = 0;
    // check in database if the email is already in use
    $email = $_POST['email'];
    $query = "SELECT * FROM user WHERE email= :email LIMIT 1";
    $stmt = $database->prepare($query);
    $results = $stmt->execute(array(":email" => $email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) { // if user exists
      if ($user['email'] == $email) {
        $error++;
        $errorMessage= "Email already excist";
      }
    }
    // check if input fields are filled in
    if (!empty($_POST['email'])){ // check if email is empty
        $email = htmlspecialchars($_POST['email']);
    }else{
        $error++;
        $errorMessage= "Email is empty";
    }
    if (!empty($_POST['password_1'])){ // check if password_1 is empty
        $password_1 = htmlspecialchars($_POST['password_1']);

    }else{
        $error++;
        $errorMessage= "Password is empty";
    }
    if (!empty($_POST['password_2'])){ // check if password_2 is empty 
        $password_2 = htmlspecialchars($_POST['password_2']);
    }else{
        $error++;
        $errorMessage= "Please confirm the password";
    }
    // if the passwords are shorter than 10 
    if(strlen($password_1) < 10 || strlen($password_2) < 10){
      $error++;
      $errorMessage= "Password needs to me longer than 10 characters.";
    }
    // if password_1 and password_2 are the same
    // make variable $password_3 (hash variant of $password_1) 
    // from here $password_3 will be used.
    if($password_1 == $password_2){ // check if passwords are the same hash the password
      $password_3 = password_hash($password_1, PASSWORD_DEFAULT);
    }else{
      $error++;
      $errorMessage= "Password needs to be the same";
    }
    if ($error === 0) { //if error = 0 insert the user 
      $query = "INSERT INTO user (email, Password) VALUES (?, ?)";
      $insert = $database->prepare($query);

      $data = array("$email", "$password_3");
      try {
        $insert->execute($data);
      }
      catch (PDOException $e) {
        throw $e;
      }
      // start session and send user to his home page
      session_start();
      $user_id =$database->lastInsertId();
      // These variables will be used to make sure the user is logged in.
      $_SESSION['id'] = $user_id;
      $_SESSION['email'] = $email;
      $_SESSION['level'] = $Level;
      $_SESSION['msg'] = "You are now logged in";
      // all the data is handled succesfully send user to assets.php.
      header('Location:assets.php');
    }else{// else show alert box
      ?>    
      <!-- error was not 0 so there was a error -->
      <!-- show a html box that will contain the specified erromessage -->
      <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
        <strong>Let op!</strong> <?php echo $errorMessage ?>
      </div><?php
    }
}