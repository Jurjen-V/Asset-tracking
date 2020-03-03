 <?php
 $REMOTE_ADDR= $_SERVER['REMOTE_ADDR'];
 $dbhost = 'localhost';
    $dbname = 'leaflet';
    $user = 'root';
    $pass = '';
    // $dbhost = 'localhost:3306';
    // $dbname = 'Leaflet';
    // $user = 'Leaflet';
    // $pass = 'leFr530$';

    try {
      $database = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $user, $pass);
      $database->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
      echo $e->getMessage();
    }
 $error = 0;
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(strpos($actual_link, 'longitude') && strpos($actual_link, 'latitude') && strpos($actual_link, 'REMOTE_ADDR')){
      if (!empty($_GET['REMOTE_ADDR'])){
        $succes= "succes 1";
      }else{
          $error++;
      }
      if (!empty($_GET['longitude'])){
        $longitude = $_GET["longitude"];
        $succes= "succes 2";
      }else{
        $error++;
      }
      if (!empty($_GET['latitude'])){
        $latitude = $_GET["latitude"];
        $succes= "succes 3";
      }else{
        $error++;
      }
      $result = $database->prepare("SELECT * FROM location WHERE REMOTE_ADDR= :parameter  LIMIT 1");
      $result->bindParam(':parameter', $REMOTE_ADDR, PDO::PARAM_STR);
      $result->execute();
      for($i=0; $row = $result->fetch(); $i++){
        $ID = $row['ID'];
        $row_REMOTE_ADDR = $row["REMOTE_ADDR"];
      } 
      if ($error === 0) {
        if ($row_REMOTE_ADDR == $REMOTE_ADDR) {
          $query = "UPDATE location SET latitude=:latitude, longitude=:longitude WHERE REMOTE_ADDR= :REMOTE_ADDR";
          $stmt = $database->prepare($query);
          $stmt->bindValue(":REMOTE_ADDR", $row_REMOTE_ADDR, PDO::PARAM_STR);
          $stmt->bindValue(":latitude", $latitude, PDO::PARAM_STR);
          $stmt->bindValue(":longitude", $longitude, PDO::PARAM_STR);
          try {
            $stmt->execute();
          }
          catch (PDOException $e) {
            echo $e->getMessage();
          }
        }else{
          $query = "INSERT INTO location (latitude, longitude, REMOTE_ADDR) VALUES (?, ?, ?)";
          $insert = $database->prepare($query);
          $data = array("$longitude", "$latitude", "$REMOTE_ADDR");
          try {
            $insert->execute($data);
            $succes = "succes 4";
          }
          catch (PDOException $e) {
            throw $e;
          }
        }
      }
    }