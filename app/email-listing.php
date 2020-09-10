<?php
include 'pagename.php';
include $session;
Session::checkSession(); //check login and start session
if (empty($_SESSION['key'])) {
    // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
      $bytes = openssl_random_pseudo_bytes(32, $cstrong);
      $_SESSION['key']   = bin2hex($bytes);

}
?>

<?php if (isset($_GET['action']) && $_GET['action'] == "logout") { // logout and destroy all session
    Session::destroy();
}
?>

<?php
if (isset($_GET["validation"]) && ($_GET["validation"] == 'valid' || $_GET["validation"] == 'invalid')) {
    $validation = $_GET["validation"];
} else {
    $validation = 'selected';
}
include '../config/'.$config;
include "../config/".$database;
$db = new database();
function test_input($data) { //filter value function
    $db = new database();
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strtolower($data);
    $data = mysqli_real_escape_string($db->link, $data);
    return $data;
}
if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
    $limit = test_input($_GET["limit"]);
} else {
    $limit = 10;
};
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) { //check token
        if (isset($_POST['validation'])) {
            $validation = test_input($_POST['validation']);
        } else {
            if (isset($_POST['row'])) {
                if ($_POST['row'] != 0) {
                    $limit = test_input($_POST['row']);
                }
            }
        }
    }
}
if (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0) {
    $pn = test_input($_GET["page"]);
} else {
    $pn = 1;
};
$start_from = ($pn - 1) * $limit;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EMail Verifier Lite - Lead Management</title>
  <link rel='canonical' href="../app/<?php echo $email_listing;?>">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">  <!-- bootstrap css library call -->
  <script src="../assets/js/jquery.min.js"></script>  <!-- jquery library call -->
  <script src="../assets/js/bootstrap.min.js"></script>  <!-- bootstrap js library call -->
  <link rel="stylesheet" href="../assets/css/all.min.css">  <!-- fontawosome library call -->
  <link rel="stylesheet" href="../assets/css/style.css">  <!-- style css -->
</head>

<body>
  <header>
    <!-- navigation bar section start -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand logo" href="../"><img src="../assets/img/logo.png" alt="logo" /><span class="mhide">EMail Verifier Lite</span></a>

        <!-- mobile navigation manu bar -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>Menu
        </button>

        <!-- logout button -->
        <form class="mobile form-inline my-2 my-lg-0">
          <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
          <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
        </form>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
          </ul>

          <!-- menu items -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="../">Email Validator <span class="sr-only">(current)</span></a>
            </li>
            <!-- /.nav-item -->
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $sendmail;?>">Send Email <span class="sr-only">(current)</span></a>
            </li>
            <!-- /.nav-item -->
            <li class="nav-item active">
              <a class="nav-link" href="<?php echo $email_listing;?>">Lead Management<span class="sr-only">(current)</span></a>
            </li>
            <!-- /.nav-item -->
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $settings;?>">Settings<span class="sr-only">(current)</span></a>
            </li>
            <!-- /.nav-item -->
          </ul><!-- /.navbar-nav -->

          <!-- logout button -->
          <form class="desk form-inline my-2 my-lg-0">
            <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
            <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
            <!-- /.log-out-btn -->
          </form>
        </div>
      </div><!-- /.container -->
    </nav><!-- /.navbar -->
  </header>

  <div id="wrapper">
    <div class="container">
      <div class="page-h">
        <h1>Lead Management</h1>
      </div>
    </div>
    <section class="email-listing">
      <div class="container">
        <div class="row">
          <!-- row select -->
          <div class="form-group col-md-4 row">
            <form style="padding-left:15px;" class="row" action="<?php echo $email_listing;?>?validation=<?php
            if($validation != 'selected'){
                echo $validation;
              }
            ?>" method="post">
              <div class="col-8">
                <select name="row" class="form-control">
                  <option value="0">Row</option>
                  <option <?php if($limit == 10){echo 'selected';}?> value="10">10</option>
                  <option <?php if($limit == 20){echo 'selected';}?> value="20">20</option>
                  <option <?php if($limit == 30){echo 'selected';}?> value="30">30</option>
                  <option <?php if($limit == 50){echo 'selected';}?> value="50">50</option>
                  <option <?php if($limit == 100){echo 'selected';}?> value="100">100</option>
                </select>
              </div>

              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">

              <div class="col-3">
                <button type="submit" class="btn btn-success btn-sm">Go</button>
              </div>
            </form>
          </div>

          <!-- validation select -->
          <div class="form-group col-md-4 row">
            <form style="padding-left:15px;" class="row" action="<?php echo $email_listing;?>?limit=<?php echo $limit?>" method="post">
              <div class="col-9">
                <select name="validation" class="form-control">
                  <option value="selected">All</option>
                  <option <?php if($validation == 'valid'){echo 'selected';}?> value="valid">Valid</option>
                  <option <?php if($validation == 'invalid'){echo 'selected';}?> value="invalid">Invalid</option>
                </select>
              </div>

              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">

              <div class="col-3">
                <button type="submit" class="btn btn-success btn-sm">Go</button>
              </div>
            </form>
          </div>

          <div class="col-md-4">
            <form class="export-csv-btn" method="post" action="<?php echo $export;?>?start=<?php echo $start_from; ?>&limit=<?php echo $limit; ?>&validation=<?php echo $validation; ?>&token=<?php echo $_SESSION['key']?>">
              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
              <button type="submit" name="export" class="form-control btn">Export as CSV</button>
            </form><!-- /.export-csv-btn -->
          </div>
        </div>

        <!-- email listing table -->
        <div class="email-listing-table">
          <table class="listing-table table table-sm table-dark">
            <thead>
              <tr>
                <th style="padding-left:20px;" scope="col">id</th>
                <th scope="col">Name</th>
                <th scope="col">Company Name</th>
                <th scope="col">Email</th>
                <th scope="col">Status</th>
              </tr>
            </thead>

            <tbody>
              <?php
              if (isset($_GET['page'])) { //get page number from pagination
                  $c = test_input($_GET['page']);
                  $c = ($c - 1) * $limit;
              } else {
                  $c = 0;
              }
              if ($validation != 'selected') {
                  if ($validation == 'valid') {
                      $user_query = "SELECT * FROM data WHERE validation  = 'Valid' LIMIT $start_from,$limit";
                  } else {
                      $user_query = "SELECT * FROM data WHERE validation  != 'Valid' LIMIT $start_from,$limit";
                  }
              } else {
                  $user_query = "SELECT * FROM data LIMIT $start_from,$limit";
              }
              $user_read = $db->select($user_query);
              if ($user_read) {
                  while ($row = $user_read->fetch_assoc()) { //write data from databse
                      $c++; ?>

                          <tr>
                            <th style="padding-left:20px;"><?php echo $c ?></th>
                            <td><?php echo $row['name'] ?></td>
                            <td><?php echo $row['company_name'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['validation'] ?></td>
                          </tr>
                          <?php
                  }
              }
              ?>
            </tbody>
          </table>
        </div>


        <!-- pagination section -->
        <div class="pagination-sec">
          <ul class="pagination pagination-content">
          <?php
            if ($validation != 'selected') {
                if ($validation == 'valid') {
                    $user_query_all = "SELECT * FROM data WHERE validation  = 'Valid'";
                } else {
                    $user_query_all = "SELECT * FROM data WHERE validation  != 'Valid'";
                }
            } else {
                $user_query_all = "SELECT * FROM data"; //select and count all data from pagination
            }

            $rs_result = $db->select($user_query_all);
            $count4 = mysqli_num_rows($rs_result);
            $total_pages = ceil($count4 / $limit);
            $k = (($pn+1>$total_pages)?$total_pages-1:(($pn-1<1)?2:$pn));
            $pagLink = "";
            if($total_pages > 1){

            if($pn>=2){
                echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".($pn-1)."&limit=" . $limit . "&validation=" . $validation . "'> < </a></li>";
                echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=1&limit=" . $limit . "&validation=" . $validation . "'> 1 </a></li>";
                if(($pn-1) > 2){
                  echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".($pn-2)."&limit=" . $limit . "&validation=" . $validation . "'> ... </a></li>";
                }
            }
            if($pn == 1){
              echo "<li class='page-item active'><a class='page-link' href='".$email_listing."?page=1&limit=" . $limit . "&validation=" . $validation . "'> 1 </a></li>";
            }
            for ($i=-1; $i<=1; $i++) {
                if($k+$i != 1 && $k+$i != $total_pages && $k+$i < $total_pages && $k+$i > 0){
                  if($k+$i==$pn)
                    $pagLink .= "<li class='page-item active'><a class='page-link' href='".$email_listing."?page=".($k+$i)."&limit=" . $limit . "&validation=" . $validation . "'>".($k+$i)."</a></li>";
                  else
                    $pagLink .= "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".($k+$i)."&limit=" . $limit . "&validation=" . $validation . "'>".($k+$i)."</a></li>";
                }
            };
            echo $pagLink;
            if($pn == $total_pages){
              echo "<li class='page-item active'><a class='page-link' href='".$email_listing."?page=".$total_pages."&limit=" . $limit . "&validation=" . $validation . "'> ".$total_pages." </a></li>";
            }
            if($pn<$total_pages){
                if(($total_pages-$pn) > 2){
                  echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".($pn+2)."&limit=" . $limit . "&validation=" . $validation . "'> ... </a></li>";
                }
                echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".$total_pages."&limit=" . $limit . "&validation=" . $validation . "'> ".$total_pages." </a></li>";
                echo "<li class='page-item'><a class='page-link' href='".$email_listing."?page=".($pn+1)."&limit=" . $limit . "&validation=" . $validation . "'> > </a></li>";

            }

          }
          ?>
          </ul>
         </div>

      </div><!-- /.container -->
    </section><!-- /.email-listing -->
  </div><!-- /.wrapper -->

  <footer class="footer-area">
    <p>Copyright @ 2019</p>
  </footer><!-- /.footer-area -->
</body>
</html>
