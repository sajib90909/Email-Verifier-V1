<?php
include 'pagename.php';
session_start();
if (empty($_SESSION['key'])) {
  // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
    $bytes = openssl_random_pseudo_bytes(32, $cstrong);
    $_SESSION['key']   = bin2hex($bytes);
}
?>
<?php
$action = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) { //check token value for csrf protection;
        if (isset($_POST['configuration'])) {
            $action = 2;
        } elseif (isset($_POST['done'])) {
            include '../config/'.$config;
            include "../config/".$database;
            $db = new database();
            function test_input($data) {
                $db = new database();
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                $data = mysqli_real_escape_string($db->link, $data);
                return $data;
            }
            $name = test_input($_POST['name']);
            $email = test_input($_POST['email']);
            $password = test_input($_POST['pass']);
            if(!empty($name) && !empty($email) && !empty($password)){
              $password = md5($password);
              $user_query = "INSERT INTO admin (name, email, password) VALUES ('$name', '$email', '$password')";
              $user_read = $db->insert($user_query);
              $installer_query = "INSERT INTO installation (validation) VALUES ('true')";
              $installer_read = $db->insert($installer_query);
              if ($user_read && $installer_read) {
                  header("Location: ".$login."?installetion=success");
              }
            }

        }
        //write config file with host, user, pass, db name;
        if (isset($_POST['dbcheck'])) {
            if (!empty($_POST['dbhost']) && !empty($_POST['dbuser']) && !empty($_POST['dbname'])) {
                $action = 3;
                $host = $_POST['dbhost'];
                $user = $_POST['dbuser'];
                $pass = $_POST['dbpass'];
                $dbname = $_POST['dbname'];
                // --------------------
                $error = false;
                // -------------------------------------------------------Database
                $connection = true;
                // try to connect to the DB, if not display error
                if (!mysqli_connect($host, $user, $pass, $dbname)) {
                    $error = true;
                    $connection = false;
                    $error_msg = "Sorry, database details are not correct." . mysqli_error();
                }
                if ($connection) {
                    $config_std = false;
                    // try to create the config file and let the user continue
                    $connect_code = "<?php
              define('DB_HOST','$host');
              define('DB_USER','$user');
              define('DB_PASS','$pass');
              define('DB_NAME','$dbname');
              ?>";
                    if (!is_writable("../config/".$config)) {
                        $error2_msg = "<p>Sorry, I can't write to <b>config/".$config."</b>.
          You will have to edit the file yourself. Here is what you need to insert in that file:<br /><br />
          <textarea rows='5' cols='50' onclick='this.select();'>$connect_code</textarea></p>";
                    } else {
                        $config_std = true;
                        $fp = fopen('../config/'.$config, 'wb');
                        fwrite($fp, $connect_code);
                        fclose($fp);
                        chmod('../config/'.$config, 0666);
                    }
                }

                //----------------------------------------------------------------

            }
        } elseif (isset($_POST['createtb'])) {
            //table create sector
            include '../config/'.$config;
            include "../config/".$database;
            $db = new database();
            $admin_drop = " DROP TABLE IF EXISTS admin "; //delete existing table
            $admin_d_read = $db->create($admin_drop);
            //admin table create
            $admin_query = "CREATE TABLE admin (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL,
        email VARCHAR(30) NOT NULL,
        password VARCHAR(50)
        )";
            $admin_q_read = $db->create($admin_query);
            $data_drop = " DROP TABLE IF EXISTS data "; //delete existing table
            $data_d_read = $db->create($data_drop);
            //email listing table
            $data_query = "CREATE TABLE data (
        id INT(255) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        company_name VARCHAR(100),
        email VARCHAR(50) NOT NULL,
        validation	VARCHAR(50) NOT NULL,
        status	VARCHAR(200)
        )";
            $data_q_read = $db->create($data_query);
            $installation_drop = " DROP TABLE IF EXISTS  installation "; //delete existing table
            $installation_d_read = $db->create($installation_drop);
            //installation check table
            $installation_query = "CREATE TABLE installation (
        id INT(1) AUTO_INCREMENT PRIMARY KEY,
        validation VARCHAR(10) NOT NULL
        )";
            $installation_q_read = $db->create($installation_query);
            $timer_drop = " DROP TABLE IF EXISTS timer "; //delete existing table
            $timer_d_read = $db->create($timer_drop);
            //mail renge and time check table
            $timer_query = "CREATE TABLE timer (
        id INT(255) AUTO_INCREMENT PRIMARY KEY,
        user_id	INT(255)	 NOT NULL,
        e_range	INT(100) NOT NULL,
        time_range	INT(100),
        last_send	INT(100),
        time_count	DATETIME
        )";
            $timer_q_read = $db->create($timer_query);
            if ($admin_q_read && $data_q_read && $installation_q_read && $installation_q_read) {
                $action = 4;
            }
        } else {
        }
    } else {
        $action = 6;
    }
} else {
    $action = 1;
    $error = false;
    $phpversion = true;
    $mail = true;
    $mysql_error = '';
    $session = true;
    // -------------------------------------------------------php vertion
    $php_version = phpversion();
    if ($php_version < 5) {
        $error = true;
        $phpversion = false;
        $php_error = "PHP version is $php_version - too old!";
    }
    // -------------------------------------------------------sql version
    // declare function
    function find_SQL_Version() {
        $output = shell_exec('mysql -V');
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
        return @$version[0] ? $version[0] : -1;
    }
    $mysql_version=find_SQL_Version();
    if($mysql_version<5)
    {

      if($mysql_version==-1){
        $mysql_error="MySQL version will be checked at the next step.";
      }
      else{
        $error=true;
        $mysql_error="MySQL version is $mysql_version. Version 5 or newer is required.";
      }
    }
    // -------------------------------------------------------mail
    if (!function_exists('mail')) {
        $error = true;
        $mail = false;
        $mail_error = "PHP Mail function is not enabled!";
    }
    // -------------------------------------------------------Session
    $_SESSION['myscriptname_sessions_work'] = 1;
    if (empty($_SESSION['myscriptname_sessions_work'])) {
        $error = true;
        $session = false;
        $session_error = "Sessions must be enabled!";
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>EMail Verifer Lite - Installation</title>
    <link rel='canonical' href="../app/<?php echo $installer;?>">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">   <!-- bootstrap -->
    <link rel="stylesheet" href="../assets/css/style.css">   <!-- style css -->
  </head>
  <body>
    <div id="wrapper">
      <section class="install">
        <h1>Installation</h1>

        <div class="container">
      		<div class="row">
        		<div class="col-md-12">
              <?php
        if ($action == 1) { //1st check all vertion and show the result
            if ($phpversion) { ?>
                    <div style="color:green" class="alert alert-light" role="alert">
                      Your php version <?php echo $php_version; ?> is ok.
                    </div>
                  <?php
            } else { ?>
                    <div style="color:red" class="alert alert-light" role="alert">
                      <?php echo $php_error; ?>
                    </div>
                  <?php
            } ?>
                  <?php if ($mysql_error == '') { ?>
                    <div style="color:green" class="alert alert-light" role="alert">
                      MySQL version is ok.
                    </div>
                  <?php
            } else { ?>
                    <div style="color:red" class="alert alert-light" role="alert">
                      <?php echo $mysql_error; ?>
                    </div>
                  <?php
            } ?>
                  <?php if ($mail) { ?>
                    <div style="color:green" class="alert alert-light" role="alert">
                      Your mail function is enabled.
                    </div>
                  <?php
            } else { ?>
                    <div style="color:red" class="alert alert-light" role="alert">
                      <?php echo $mail_error; ?>
                    </div>
                  <?php
            } ?>
                  <?php if ($session) { ?>
                    <div style="color:green" class="alert alert-light" role="alert">
                      Session is enabled.
                    </div>
                  <?php
            } else { ?>
                    <div style="color:red" class="alert alert-light" role="alert">
                      <?php echo $session_error; ?>
                    </div>
                  <?php
            } ?>

                  <?php if ($error) { ?>
                    <button type="button" disabled class="btn btn-primary float-right">Next</button>
                  <?php
            } else { ?>
                    <form action="#" method="post">
                      <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['key'] ?>">
                    <button type="submit" name="configuration"  class="btn btn-primary float-right">Next</button>
                  </form>
                  <?php
            } ?>

              <?php
        } elseif ($action == 3) { //configuration page ?>
                <?php if ($connection) { ?>
                  <div style="color:green" class="alert alert-light" role="alert">
                    Database connection successful.<?php ?>
                  </div>
                <?php
            } else { ?>
                  <div style="color:red" class="alert alert-light" role="alert">
                    <?php echo $error_msg; ?>
                  </div>
                <?php
            } ?>
                <?php if ($connection) {
                if ($config_std) { ?>
                    <div style="color:green" class="alert alert-light" role="alert">
                      configuration successful.<?php ?>
                    </div>

                  <?php
                } else { ?>
                    <div style="color:red" class="alert alert-light" role="alert">
                      <?php echo $error2_msg; ?>
                    </div>
                    <div style="color:green" class="alert alert-light" role="alert">
                      Database configuration fail.<?php ?>
                    </div>
                  <?php
                } ?>
                <?php
            } else { ?>
                  <div style="color:red" class="alert alert-light" role="alert">
                    Database configuration fail. Check you settings!<?php ?>
                  </div>
                <?php
            } ?>
                <?php if ($error) { ?>

                  <button type="button" disabled class="btn btn-info float-right">Next</button>
                  <a style="margin-right:20px" href="<?php echo $installer;?>" class="btn btn-light float-right">back</a>
                <?php
            } else {
        ?>
                  <p style='color:red'>Warnning: if you continue installation then all existing data will be removed from this database.</p>
                  <form action="#" method="post">
                    <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['key'] ?>">
                  <button type="submit" name="createtb"  class="btn btn-primary float-right" onClick="javascript: return confirm('Warnning: if you continue installation then all existing data will be removed from this database.');">Next</button>
                </form>

                <?php
            }
        } elseif ($action == 4) { //user create page
        ?>
                <div style="color:green" class="alert alert-light" role="alert">
                  Data Tables created successfully.<?php ?>
                </div>
                <h2>Account Setup ...</h2>
                <form action="#" method="post">
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                      <div class="col-sm-10">
                        <input type="text" required name="name" class="form-control" id="inputEmail3" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" required name="email" class="form-control" id="inputEmail3" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail3" required class="col-sm-2 col-form-label">Password</label>
                      <div class="col-sm-10">
                        <input type="password"  name="pass" class="form-control" id="inputEmail3" placeholder="Password">
                      </div>
                    </div>
                    <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['key'] ?>">
                    <div class="form-group row">
                      <div class="col-sm-12 col-md-12">
                        <button type="submit" name="done" class="btn btn-primary float-right">Complete</button>
                      </div>
                    </div>
                  </form>
              <?php
        } elseif ($action == 2) { //configuration info page ?>

        	 <h2>Database Config ...</h2>
                <form action="#" method="post">
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">DB HOST</label>
                      <div class="col-sm-10">
                        <input type="text" required name="dbhost" class="form-control" id="inputEmail3" placeholder="db host">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">DB USER</label>
                      <div class="col-sm-10">
                        <input type="text" required name="dbuser" class="form-control" id="inputEmail3" placeholder="db user">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">DB PASSWORD</label>
                      <div class="col-sm-10">
                        <input type="text"  name="dbpass" class="form-control" id="inputEmail3" placeholder="db password">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">DB NAME</label>
                      <div class="col-sm-10">
                        <input type="text" required name="dbname" class="form-control" id="inputEmail3" placeholder="db name">
                      </div>
                    </div>
                    <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['key'] ?>">
                    <div class="form-group row">
                      <div class="col-sm-12 col-md-12">
                        <button type="submit" name="dbcheck" class="btn btn-primary float-right">Next</button>
                      </div>
                    </div>
                  </form>
                <?php
        }elseif($action == 6){ ?>
                <div style="color:red" class="alert alert-light" role="alert">
                  Some problem occurred in installation. your session token is not work! please try again later;
                </div>
                <h4>Possible Solution</h4>
                <div style="color:green" class="alert alert-light" role="alert">
                  <ul>
                    <li>Clear your browser cache.</li>
                    <li>Make sure your session enabled in php.ini</li>
                    <li>Try another browser.</li>
                  </ul>
                </div>
                <a style="margin-right:20px" href="<?php echo $installer;?>" class="btn btn-light float-right">back</a>
                <?php
        } ?>

          </div>
        </div>
      </div>
    </section> <!-- /.install -->
  </div>  <!-- /.warrper -->

  <footer class="footer-area">
    <p>Copyright @ 2019</p>
  </footer>
  </body>
</html>
