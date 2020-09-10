<!DOCTYPE html>

<?php
include 'pagename.php';
include '../config/'.$config;
include "../config/".$database;
?>

<?php include '../config/'.$format; ?>
<?php
$db = new database();
$fm = new Format();
?>

<?php
$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbname = DB_NAME;
if (!mysqli_connect($host, $user, $pass, $dbname)) //check database configaration
{
    header("Location: ".$installer);
} else {
    $query = "SELECT * FROM installation ";
    $read = $db->check($query);
    if ($read != false) {
        $row = $read->fetch_assoc();
        if ($row['validation'] == 'false') {
            header("Location: ".$installer);
        } else {
        }
    } else {
        header("Location: ".$installer);
    }
}
?>

<?php
include $session;
Session::init(); // session start
if (empty($_SESSION['key'])) {
  // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
    $bytes = openssl_random_pseudo_bytes(32, $cstrong);
    $_SESSION['key']   = bin2hex($bytes);
}
?>

<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EMail Verifier Lite - Sign In</title>
  <link rel='canonical' href="../app/<?php echo $login;?>">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">  <!-- bootstrap css -->
  <link rel="stylesheet" href="../assets/css/style.css">  <!-- style css -->
  <!-- internal css -->
  <style media="screen">
    .log {
      margin: 10% 30%;
      text-align: center;
      padding: 20px 50px;
    }

    .int {
      margin-bottom: 30px;
    }

    @media only screen and (max-width: 1100px) {
      .log {
        margin: 0%;
        padding: 20px 0px;
      }

    }
  </style>
</head>

<body>
  <?php
    //check user email and password
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) {
            $email = $fm->validation($_POST['email']);
            $password = $fm->validation($_POST['password']);
            $email = mysqli_real_escape_string($db->link, $email);
            $password = mysqli_real_escape_string($db->link, $password);
            $password = md5($password);
            $query = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
            $read = $db->select($query);
            if ($read != false) {
                $value = mysqli_fetch_array($read);
                $row = mysqli_num_rows($read);
                if ($row > 0) {
                    Session::set("login", true);
                    Session::set("name", $value['name']); //get user name
                    Session::set("email", $value['email']); //get user email
                    Session::set("id", $value['id']);
                    header("location:../".$index);
                } else {
                    header('location:'.$login);
                    Session::set("error", 1);
                }
            }
        }
    }
    ?>
  <?php if(isset($_GET['installetion'])){ ?>
  <div class="alert alert-success int-success-alert" role="alert">     <!-- conformation installation alert -->
    Installation Successful. Please Sign in.
  </div>
  <?php } ?>

  <div id="wrapper" class="login-body-bg">
    <section>
      <div class="login-box card card-m">
        <div class="card-header">
          <h1 class="login-logo"><img src="../assets/img/logo.png" alt="logo" />EMail Verifier Lite</h1>
        </div>

        <div class="card-body">
          <div class="container">
            <form class="" action="#" method="post">
              <div class="int input-group flex-nowrap">
                <div class="input-group-prepend">
                  <span class="input-group-text">Email</span>
                </div>
                <input required type="text" class="form-control" name="email" aria-label="Username">
              </div>

              <div class="int input-group flex-nowrap">
                <div class="input-group-prepend">
                  <span class="input-group-text">Password</span>
                </div>
                <input required type="password" class="form-control" name="password" aria-label="Username">
              </div>

              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">

              <button type="submit" class="submit log-in-btn btn btn-primary">login</button>
            </form>

            <?php if(isset($_SESSION['error'])){
            echo '<div style="margin-top:20px;" class="alert alert-danger" role="alert">
              <p>your email or password is not matching!!! please try again !!</p>
            </div>';
          }?>
          </div>
        </div>
      </div>
    </section>

	<footer class="footer-area">
		<p>Copyright @ 2019</p>
	</footer><!--/.footer-->

  </div> <!--/.wrapper-->


</body>
</html>
