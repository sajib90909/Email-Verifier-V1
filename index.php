<?php
      include 'app/pagename.php';
      include 'app/'.$session;
      Session::checkSession_d();
      if(empty($_SESSION['key'])){
        // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
          $bytes = openssl_random_pseudo_bytes(32, $cstrong);
          $_SESSION['key']   = bin2hex($bytes);
      }
      ?>
<?php if(isset($_GET['action']) && $_GET['action'] == "logout"){
    Session:: destroy_d();
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EMail Verifier Lite</title>
  <link rel="canonical" href="../">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css"> <!-- bootstrap css library call -->
  <script src="assets/js/jquery.min.js"></script> <!-- jquery library call -->
  <script src="assets/js/bootstrap.min.js"></script> <!-- bootstrap js library call -->
  <link rel="stylesheet" href="assets/css/all.min.css"> <!-- fontwasome library call -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <header>
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand logo" href="../"><img src="assets/img/logo.png" alt="logo" /><span class="mhide">EMail Verifier Lite</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>Menu
        </button>
        <!-- logout button -->
        <form class="mobile form-inline my-2 my-lg-0">
          <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
          <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
        </form>
        <!-- menu item -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <a class="nav-link" href="">Email Validator <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="app/<?php echo $sendmail;?>">Send Email <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="app/<?php echo $email_listing;?>">Lead Management<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="app/<?php echo $settings;?>">Settings<span class="sr-only">(current)</span></a>
            </li>
          </ul>
          <!-- logout button -->
          <form class="desk form-inline my-2 my-lg-0">
            <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
            <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
          </form>
        </div>
      </div>
    </nav>
  </header>

  <div id="wrapper">
    <div class="container">
      <h1>Email Validator</h1>
    </div>

    <!-- csv extract section -->
    <section class="interface-btn">
      <div class="container">
        <h2>Step 1.</h2>
        <form class="csv_import" action="#" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="input-group col-md-6">
              <div class="custom-file">
                <input type="file" name="csv" class="csv custom-file-input" id="fileInput">
                <label id="filename" class="custom-file-label">Extract email from .csv file</label>
              </div>

              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
              <div class="input-group-append">
                <button type="submit" name="extract" class="btn btn-outline-secondary">Extract</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section><!-- /.interface-btn -->

    <section class="email-check">
      <div class="container">
        <div class="row">
          <div class="sec-div col-md-6">
            <h2>Step 2.</h2>
            <div class="card">
              <div class="card-header">
                <h6 style="display:inline-block">Email Addresses Found:</h6>
                <h6 class="email-count float-right">0</h6>
              </div>

              <!-- input email body -->
              <form class="" method="post" id="check-email-form">
                <div class="found-email-list card-body">
                  <textarea class="text-area" name="email" id="email" placeholder="list of email addresses" <?php
                      $cont_std = false;
                      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                          if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) { //extract email from csv
                              if (isset($_POST['extract'])) {
                                  $statment = false;
                                  $filename = $_FILES['csv']['name'];
                                  $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                  $fname = $_FILES['csv']['tmp_name'];
                                  if ($ext == 'csv') {
                                      $file = fopen($fname, 'r');
                                      $fistrow = fgetcsv($file);
                                      $count = count($fistrow);
                                      if ($count > 1) {
                                          echo 'readonly';
                                          $cont_std = true;
                                      }
                      ?> <?php
                                  }
                              }
                          }
                      }
                      ?>><?php
                        $h = 0;
                        if ($cont_std) {
                          for ($i = 0; $i < $count; $i++){
                            $rowname = strtolower($fistrow[$i]);
                            if($rowname == 'email' || $rowname == 'mail' || $rowname == 'gmail'){
                              while ($row = fgetcsv($file)) {
                                $h++;
                                ob_start();
                                $email = $row[$i];
                                echo $email.'&#013;';
                                ob_end_flush();
                              }
                            }
                          }
                        }

                          ?></textarea>
                  <script>
                    e_counts = <?php echo $h; ?> ;
                    $('.email-count').html(e_counts);
                  </script>
                </div>

                <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
                <!-- check velidation button -->
                <div class="step-one-btn card-footer text-muted">
                  <button type="submit" disabled id='checkmail' class="btn btn-success">Check</button>
                  <a href="<?php echo $index;?>" class="btn btn-info">Clear</a>
                  <a id="ceckmail_w">please wait....</a>
                </div>
              </form>
            </div>
          </div>
          <!-- validation mail status table -->
          <div class="col-md-6">
            <h2>Step 3.</h2>

            <div class="card">
              <div class="found-email-tool card-header">
                <button type="button" class="all-btn btn btn-secondary btn-sm">All</button>
                <button type="button" class="valid-btn btn btn-success btn-sm">Valid</button>
                <button type="button" class="invalid-btn btn btn-warning btn-sm">Invalid</button>
              </div>

              <div class="found-email-result card-body">
                <table style="" class="table">
                  <tbody class="tbody-data">

                  </tbody>
                </table>
              </div>

              <div class="step-two-btn container btn-box">
                <div class="row">
                  <div class="download-sec col-7 col-sm-7 col-md-7">
                    <div class="found-email-download text-muted">
                      <button disabled class="download btn btn-info" id="csv">Download List</button>
                      <i class="start fas fa-circle-notch fa-spin float-right"></i>
                      <i class="end fas fa-check float-right"></i>
                    </div>
                  </div>

                  <div class="store-sec col-5 col-sm-5 col-md-5">
                    <div class="found-email-store text-muted">
                      <button style="" name="store" disabled class="store btn btn-outline-secondary float-right" type="button">Save List</button>
                      <i class="start_store fa fa-circle-notch fa-spin float-right"></i>
                      <p class="end_store float-right">Done</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!--/.email-check-->

    <section class="email-status">
      <div class="container">
        <h2>Step 4.</h2>
        <div class="row">
          <div class="sec-div col-md-6">
            <div class="card">
              <h6 class="card-header">Analyzed Report</h6>
              <div style="padding-bottom:40px;" class="card-body">
                <div class="txt">
                  <p class="item">Total email:</p>
                  <p style="color:purple;" class="item-c" id="total">0</p>
                </div>

                <div class="txt">
                  <p class="item">Duplicates:</p>
                  <p style="color:#4a4949;" class="item-c" id="duplicate">0</p>
                </div>

                <div class="txt">
                  <p class="item">Syntax errors:</p>
                  <p style="color:#ffc107;" class="item-c" id="syntax">0</p>
                </div>

                <div class="txt">
                  <p class="item">Valid:</p>
                  <p style="color:#28a745;" class="item-c" id="valid">0</p>
                </div>

                <div class="txt">
                  <p class="item">Invalid:</p>
                  <p style="color:#ff2d42;" class="item-c" id="invalid">0</p>
                </div>

                <div class="txt">
                  <p class="item">Unknown:</p>
                  <p style="color:#c3c3c3;" class="item-c" id="unknown">0</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <canvas id="myChart"></canvas>
          </div>
        </div>
      </div>
    </section>
  </div> <!-- /.wrapper -->

  <script src="assets/js/chart.js"></script>

  <script src="assets/js/main.js"></script>

  <script>
// store csv data to database
$( document ).ready(function() {
  $('#checkmail').removeAttr("disabled");
  $('#ceckmail_w').css('display','none');
});
document.getElementById('fileInput').onchange = function () {
  f = this.value.replace(/.*[\/\\]/, '');
  $('#filename').html(f);
};
  $('.store').click(function(e){
    $('.start_store').css('display','inline-block');
    $('.end_store').css('display','none');
          var row1 = $("table tr");
          var rows1 = new Array();
          row1.each(function(){
              if($(this).css('display') == 'none'){

              }else{
                rows1.push($(this));
              }
          });
          var k = 0;
          var d = 0;
          var arr = [];
            for (var i = 0; i < rows1.length; i++) {
            var row1 = [], cols = rows1[i].children("td, th");

                for (var j = 0; j < cols.length; j++){
                    row1.push(cols[j].innerText);
                }
                arr.push(row1);
            }
            // console.log(arr);



    var allcsv = [];
    <?php
    if($_SERVER['REQUEST_METHOD']=='POST'){
      if(!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']){
        $statment = false;
        $filename =  $_FILES['csv']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $fname =  $_FILES['csv']['tmp_name'];
        if($ext == 'csv'){
        $file = fopen($fname , 'r');
        $fistrow = fgetcsv($file);
        $count = count($fistrow);
        $email = '999999999';
        $name = '999999999';
        $companyname = '999999999';
        for ($i = 0; $i < $count; $i++){
          $rowname = strtolower($fistrow[$i]);
          if($rowname == 'email' || $rowname == 'mail' || $rowname == 'gmail'){
            $email = $i;
          }
          if($rowname == 'name'){
            $name = $i;
          }
          if($rowname == 'company name' || $rowname == 'company' || $rowname == 'companyname'){
            $companyname = $i;
          }
        }
          $b = 0;
          while ($row = fgetcsv($file)) {
            ob_start();
              $emails = $row[$email];
              if($name == '999999999'){
                $names = '';
              }else{
                $names = $row[$name];
              }
              if($companyname == '999999999'){
                $companynames = '';
              }else{
                $companynames = $row[$companyname];
              }


              if(!empty($email)){
                $b++;
                ?>
                var a = [
                          '<?php echo $b ?>',
                          '<?php echo $emails ?>',
                          '<?php echo $names ?>',
                          '<?php echo $companynames ?>'
                        ];
                allcsv.push(a);
            <?php
            }
          }
        }

    ?>
    // console.log(allcsv);
    //
    // console.log(arr);
    var array3 = [];
    for(var y = 0; y < arr.length;y++){
        var arr2 = [arr[y][0],allcsv[arr[y][0]-1][1],allcsv[arr[y][0]-1][2],allcsv[arr[y][0]-1][3],arr[y][2],arr[y][3]];
        array3.push(arr2);
    }
    // console.log(array3);
    var final_c = array3.length;
    var z = 0;
    var token = '<?php echo $_SESSION['key']?>';
    for(f = 0; f < final_c; f++){
      mail = array3[f][1];
      name = array3[f][2];
      companyname = array3[f][3];
      status = array3[f][5];
      validation = array3[f][4];
      $.ajax({
          url: "app/storeverify.php",
          type: "post",
          data: {
            email: mail,
            name: name,
            companyname: companyname,
            status: status,
            validation: validation,
            token: token
          },
        }).done(function(result){
          z++;
          if(z == final_c){
            $('.start_store').css('display','none');
            $('.end_store').css('display','inline-block');
            setTimeout(function(){ $('.end_store').css('display','none'); }, 2000);
          }
        })
    }
    <?php } }else{
        ?>
        var email_count = arr.length;
        console.log(email_count);
        var c = 0;
        var token = '<?php echo $_SESSION['key']?>';
        for (var i = 0; i < arr.length; i++) {
          mail = arr[i][1];
          name = '';
          companyname = '';
          status = arr[i][3];
          validation = arr[i][2];
          $.ajax({
              url: "app/storeverify.php",
              type: "post",
              data: {
                email: mail,
                name: name,
                companyname: companyname,
                status: status,
                validation: validation,
                token: token
              },
            }).done(function(result){
              c++;
              if(c == email_count){
                $('.start_store').css('display','none');
                $('.end_store').css('display','inline-block');
                setTimeout(function(){ $('.end_store').css('display','none'); }, 2000);
              }
            })
        }

        <?php
      } ?>
  })
</script>

  <script>
    //export csv file
    function download_csv(csv, filename) {
      var csvFile;
      var downloadLink;

      // CSV FILE
      csvFile = new Blob([csv], {type: "text/csv"});

      // Download link
      downloadLink = document.createElement("a");

      // File name
      downloadLink.download = filename;

      // We have to create a link to the file
      downloadLink.href = window.URL.createObjectURL(csvFile);

      // Make sure that the link is not displayed
      downloadLink.style.display = "none";

      // Add the link to your DOM
      document.body.appendChild(downloadLink);

      // Lanzamos
      downloadLink.click();
    }

    function export_table_to_csv(html, filename) {
    var csv = [];
    var row = $("table tr");
    var rows = new Array();
    csv.push('Serial,Email,Validation,Status\n');
    row.each(function(){
        if($(this).css('display') == 'none'){

        }else{
          rows.push($(this));
        }
    });
      for (var i = 0; i < rows.length; i++) {
      var row = [], cols = rows[i].children("td, th");

          for (var j = 0; j < cols.length; j++)
              row.push(cols[j].innerText);

      csv.push(row.join(","));
    }

      // Download CSV
      download_csv(csv.join(''), filename);
    }

    document.querySelector("#csv").addEventListener("click", function () {
      var html = document.querySelector("table").outerHTML;
    export_table_to_csv(html, "table.csv");
    });
  </script>

  <footer class="footer-area">
    <p>Copyright @ 2019</p>
  </footer>
</body>
</html>
