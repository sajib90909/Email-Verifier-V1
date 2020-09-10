<?php
include 'pagename.php';
include $session;
Session::checkSession();
if (empty($_SESSION['key'])) {
  // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
    $bytes = openssl_random_pseudo_bytes(32, $cstrong);
    $_SESSION['key']   = bin2hex($bytes);
}
?>
<?php if (isset($_GET['action']) && $_GET['action'] == "logout") {
    Session::destroy();
}
?>
<?php
include '../config/'.$config;
include "../config/".$database;
$db = new database();
$std = false;
$std2 = false;
function test_input($data) {
    $db = new database();
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strtolower($data);
    $data = mysqli_real_escape_string($db->link, $data);
    return $data;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) { //check token value for csrf protection;
        if (isset($_POST['range_b'])) { //admin send mail range and time update
            $range = test_input($_POST['range']);
            $time_range = test_input($_POST['time_range']) * 60;
            $query2 = "SELECT * FROM timer WHERE user_id = 1 ";
            $read2 = $db->select($query2);
            if ($read2) {
                $count = mysqli_num_rows($read2);
                if ($count > 0) {
                    $query18 = "UPDATE timer SET e_range = '$range', time_range = '$time_range' WHERE user_id = 1 ";
                    $read18 = $db->update($query18);
                    if ($read18) {
                        $std = true;
                    }
                } else {
                    $query_insert = "INSERT INTO timer ( user_id, e_range, time_range, 	last_send, time_count)
          VALUES ('1','$range','$time_range','0','0')";
                    $read_insert = $db->insert($query_insert);
                    if ($read_insert) {
                        $std = true;
                    }
                }
            }
        } elseif (isset($_POST['basic_b'])) { //admin info update
            $name = test_input($_POST['name']);
            $email = test_input($_POST['email']);
            $pass_std = false;
            if (!empty($_POST['password'])) {
                $pass_std = true;
                $password = md5(test_input($_POST['password']));
            }
            $query3 = "SELECT * FROM admin WHERE id = 1 ";
            $read3 = $db->select($query3);
            if ($read3) {
                $count = mysqli_num_rows($read3);
                if ($count > 0) {
                    if ($pass_std) {
                        $query18 = "UPDATE admin SET name = '$name', email = '$email', password = '$password' WHERE id = 1 ";
                    } else {
                        $query18 = "UPDATE admin SET name = '$name', email = '$email' WHERE id = 1 ";
                    }
                    $read18 = $db->update($query18);
                    if ($read18) {
                        $std2 = true;
                    }
                } else {
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EMail Verifier Lite - Settings</title>
  <link rel='canonical' href="../app/<?php echo $settings;?>">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">  <!-- bootstrap css library call -->
  <script src="../assets/js/jquery.min.js"></script>  <!-- jquery library call -->
  <script src="../assets/js/bootstrap.min.js"></script>  <!-- bootstrap js library call -->
  <link rel="stylesheet" href="../assets/css/all.min.css"> <!-- fontawosome css library call -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
  <header>
    <!-- navigation bar section start -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand logo" href="../"><img src="../assets/img/logo.png" alt="logo" /><span class="mhide">EMail Verifier Lite</span></a>

        <!-- mobile navigation manu bar -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span> Menu
        </button>

        <!-- logout button -->
        <form class="mobile form-inline my-2 my-lg-0"> <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>"> <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i
              class="fas fa-sign-out-alt"></i></a> </form>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
          </ul>

          <!-- menu items -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="../">Email Validator <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href=".<?php echo $sendmail;?>">Send Email <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $email_listing;?>">Lead Management<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="<?php echo $settings;?>">Settings<span class="sr-only">(current)</span></a>
            </li>
          </ul>

          <!-- logout button -->
          <form class="desk form-inline my-2 my-lg-0">
            <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
            <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
          </form> <!-- /.desk -->
        </div>
      </div>
    </nav>
  </header>

  <div id="wrapper">
    <div class="container">
      <div class="page-h">
        <h1>Settings</h1>
      </div>
    </div>
    <section class="settings">
      <div class="container">

        <div class="action-tab">
          <div class="row">
            <div class="col-12 send-email">
              <h2>Send Email</h2>
              <?php
          if ($std) { ?>
              <p class="setting-status">Update success</p>
              <?php }
          ?>
            </div> <!-- /.send_mail -->

            <div class="col-md-7 setting-box">
              <?php
                        $user_query3 =  "SELECT * FROM timer WHERE user_id = 1 ";
                        $user_read3 = $db->select($user_query3);
                        if($user_read3){
                          $row3 = $user_read3->fetch_assoc();
                        ?>
              <form action="#" method="post">
                <div class="form-group row">
                  <label style="color:#FFFFFF" class="col-sm-4 col-form-label">Email Amount</label>
                  <div class="col-sm-8">
                    <input type="number" name="range" class="form-control" value="<?php echo $row3['e_range']?>" placeholder="How many email can send per hour">
                  </div>
                </div>
                <div class="form-group row">
                  <label style="color:#FFFFFF" class="col-sm-4 col-form-label">Time Limit (Hour)</label>
                  <div class="col-sm-8">
                    <input type="number" name="time_range" class="form-control" value="<?php echo $row3['time_range']/60;?>" placeholder="Limit of Hour">
                  </div>
                </div>
                <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
                <div class="form-group">

                  <div class="float-right">
                    <button type="submit" name="range_b" class="btn btn-light">Save</button>
                    <?php }?>
                  </div>
                </div>
              </form>
            </div> <!-- /.setting-box -->

            <div class="col-12 user-profile">
              <h2>User Profile</h2>
              <?php
                    if ($std2) { ?>
              <p class="setting-status">Update success</p>
              <?php }
                    ?>
            </div> <!-- /.user-profile -->

            <div class="col-md-7 setting-box">
              <?php
                        $user_query3 =  "SELECT * FROM admin WHERE id = 1 ";
                        $user_read3 = $db->select($user_query3);
                        if($user_read3){
                          $row3 = $user_read3->fetch_assoc();
                        ?>
              <form action="#" method="post">
                <div class="form-group row">
                  <label style="color:#FFFFFF" class="col-sm-4 col-form-label">Name</label>
                  <div class="col-sm-8">
                    <input type="text" name="name" class="form-control" value="<?php echo $row3['name']?>" placeholder="name">
                  </div>
                </div>

                <div class="form-group row">
                  <label style="color:#FFFFFF" class="col-sm-4 col-form-label">Email</label>
                  <div class="col-sm-8">
                    <input type="email" name="email" class="form-control" value="<?php echo $row3['email']?>" placeholder="email">
                  </div>
                </div>

                <div class="form-group row">
                  <label style="color:#FFFFFF" class="col-sm-4 col-form-label">Password</label>
                  <div class="col-sm-8">
                    <input type="password" name="password" class="form-control" value="" placeholder="password">
                  </div>
                </div>

                <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">

                <div class="form-group">
                  <div class="float-right">
                    <button type="submit" name="basic_b" class="btn btn-light">Change</button>
                    <?php }?>
                  </div>
                </div>
              </form>
            </div> <!-- /.setting-box -->
          </div>
        </div> <!-- /.action-tab -->
      </div>
    </section> <!-- /.settings -->
  </div>  <!-- /.wrapper -->

  <footer class="footer-area">
    <p>Copyright @ 2019</p>
  </footer>
</body>
</html>
