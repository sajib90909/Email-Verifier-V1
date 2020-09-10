<?php
      include 'pagename.php';
      include $session;
      Session::checkSession();
      if(empty($_SESSION['key'])){
        // $_SESSION['key'] = bin2hex(random_bytes(32)); //generate random token //only run in php7
          $bytes = openssl_random_pseudo_bytes(32, $cstrong);
          $_SESSION['key']   = bin2hex($bytes);
      }
      ?>
<?php if(isset($_GET['action']) && $_GET['action'] == "logout"){ //logout
    Session:: destroy();
  }
?>
<?php
include '../config/'.$config;
include "../config/".$database;
$db = new database();
$dates = new DateTime('now', new DateTimeZone('UTC') ); //php UTC timezone
$dates = $dates->format('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EMail Verifier Lite - Send Email</title>
  <link rel='canonical' href="../app/<?php echo $sendmail;?>">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/jquery.min.js"></script> <!-- jquery library call -->
  <script src="../assets/js/bootstrap.min.js"></script>  <!-- bootstrap library call -->
  <link rel="stylesheet" href="../assets/css/all.min.css">  <!-- fontwasome library call -->
  <script src="../assets/ckeditor/ckeditor.js"></script>  <!-- ckeditor library call -->
  <link rel="stylesheet" href="../assets/css/style.css">  <!-- style css -->
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand logo" href="../"><img src="../assets/img/logo.png" alt="logo" /><span class="mhide">EMail Verifier Lite</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>Menu
        </button>
        <form class="mobile form-inline my-2 my-lg-0"> <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>"> <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="Logout" role="button"><i
              class="fas fa-sign-out-alt"></i></a> </form>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="../">Email Validator <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="<?php echo $sendmail;?>">Send Email <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $email_listing;?>">Lead Management<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $settings;?>">Settings<span class="sr-only">(current)</span></a>
            </li>
          </ul>
          <form class="desk form-inline my-2 my-lg-0">
            <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">
            <a href="?action=logout" class="log-out-btn btn btn-outline-success my-2 my-sm-0" title="logout" role="button"><i class="fas fa-sign-out-alt"></i></a>
          </form>

        </div>
      </div>
    </nav>
  </header>

  <div id="wrapper">
    <div class="container">
      <h1>Send Bulk Email</h1>
    </div>

    <!-- csv extract section -->
    <section class="interface-btn">
      <div class="container">
        <h2>step 1.</h2>
        <form class="csv_import" action="#" method="post" enctype="multipart/form-data">
          <div style="margin-bottom:40px" class="row">
            <div class="input-group col-md-6">
              <div class="custom-file">
                <input type="file" name="csv" class="csv custom-file-input" id="fileInput">
                <label id="filename" class="custom-file-label">Extract email from .csv file</label>
              </div>

              <input type="hidden" name="token" value="<?php echo $_SESSION['key']?>">

              <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary">Extract</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section> <!-- /.interface-btn -->

    <section class="send-box">
      <div class="container">
        <h2>step 2.</h2>
        <div class="row">
          <div class="col-md-7">
            <form action="<?php echo $mailsend;?>" method="post" id="send_mail">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">From</label>
                <div class="col-sm-9">
                  <input type="email" required name="from" class="form-control" id="from" placeholder="your email">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label">To</label>
                <div class="col-sm-9">
                  <input type="text" name="to" class="form-control" placeholder="where to?" id="tomail" required value="<?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        if (!empty($_POST['token']) && $_POST['token'] == $_SESSION['key']) {
                            $statment = true;
                            $statmenttwo = false;
                            $filename = $_FILES['csv']['name'];
                            $ext = pathinfo($filename, PATHINFO_EXTENSION);
                            $fname = $_FILES['csv']['tmp_name'];
                            if ($ext == 'csv') {
                                $file = fopen($fname, 'r');
                                $fistrow = fgetcsv($file);
                                $count = count($fistrow);
                                for ($x = 0;$x < $count;$x++) {
                                    $rowname = strtolower($fistrow[$x]);
                                    if ($rowname == 'email' || $rowname == 'mail' || $rowname == 'gmail') {
                                        while ($row = fgetcsv($file)) {
                                            if (!$statment) {
                                                $statment = true;
                                            } else {
                                                $email = $row[$x];
                                                if (!$statmenttwo) {
                                                    $statmenttwo = true;
                                                    echo $email;
                                                } else {
                                                    echo ',' . $email;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Subject</label>
                <div class="col-sm-9">
                  <input type="text" name="subject" class="form-control" id="subject" required placeholder="your subject">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Message</label>
                <div class="col-sm-9">
                  <textarea required id="message" name="message"></textarea>
                </div>
              </div>

              <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['key']?>">

              <div class="form-group row">
                <div class="col-3">
                </div>
                <div class="col-sm-9">
                  <?php
                  $user_query3 =  "SELECT * FROM timer WHERE user_id = 1 ";
                  $user_read3 = $db->select($user_query3);
                  if($user_read3){
                    $row3 = $user_read3->fetch_assoc();
                    $time_count = $row3['time_count'];
                    $t_dates = strtotime($dates);
                    $start_date = strtotime($time_count);
                    $time_left =($t_dates - $start_date)/60;

                    $range = $row3['e_range'];
                    $send = $row3['last_send'];
                    $time_range = $row3['time_range'];
                    $email_left = $range - $send;
                    $time_remining = $time_range - $time_left;
                  ?>
                  <button type="submit" class="btn btn-primary">Send</button>
                  <i style="margin-left: 10px;font-size:20px;color:#EE800C;display:none;" class="start fas fa-circle-notch fa-spin"></i>
                  <i style="margin-left: 10px;font-size:20px;color:#0EBE30;display:none;" class="end fas fa-check"></i>
                  <?php
                  if($email_left == 0){ ?>
                  <p class="warn-box">You have no sending limit left! Please set your sending limit <a href="../app/<?php echo $settings;?>" target="_self">here</a></p>
                  <?php }else{
                  ?>
                  <p id="email_check" style="color:red;display:none">you can't send more than <?php echo $email_left;?> email within <?php echo round($time_remining, 2);?>min</p>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-3">
                </div>
                <label class="col-form-label col-9">You can send <?php echo $range;?> mail within <?php echo $time_range/60;?> hours</label>
                <?php
              }
                ?>
              </div>
            </form>
          </div>

          <div class="col-md-5 ">
            <h2>Delivery Report</h2>
            <div class="row sent-status">
              <div class="col-6">
                <p>Total Email : </p>
                <p>Duplicates : </p>
                <p>Sendable : </p>
                <p>Sent : </p>
                <p>Failed :</p>
              </div>

              <div class="col-6">
                <p id="total">0</p>
                <p id="duplicate">0</p>
                <p id="sendable">0</p>
                <p id="send">0</p>
                <p id="notsend">0</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /.send-box -->
  </div>

  <footer class="footer-area">
    <p>Copyright @ 2019</p>
  </footer>

  <script>
  	document.getElementById('fileInput').onchange = function ()
  	{
  		f = this.value.replace(/.*[\/\\]/, '');
  		$('#filename').html(f);
  	};
  $(function ()
  {
  	// mail validation check
  	$("#send_mail").submit(function (e)
  	{
  		e.preventDefault();
  		var token = $("#token").val();
  		if (token == '<?php echo $_SESSION['key'] ?>')
  		{
  			var temails = $("#tomail").val().split(",");
  			var from = $("#from").val();
  			var subject = $("#subject").val();
  			var message = CKEDITOR.instances['message'].getData();

  			temails = temails.filter(Boolean);
  			if (temails != '')
  			{
  				$('.start').css('display', 'inline-block');
  				$('.end').css('display', 'none');
  			}
  			emails = temails.filter(function (elem, index, self)
  			{
  				return index === self.indexOf(elem);
  			});
  			var tcount = temails.length;
  			var count = emails.length;
  			var duplicate = tcount - count;

  			$("#total").html(tcount);
  			$("#duplicate").html(duplicate);
  			$("#sendable").html(count);
  			var token = '<?php echo $_SESSION['key'] ?>';
  			var i = 0;
  			var success = 0;
  			var error = 0;
  			$.ajax(
  			{
  				url: "<?php echo $timecheck;?>",
  				type: "post",
  				data:
  				{
  					emails: count,
  					token: token
  				},
  			}).done(function (data)
  			{
  				if (data == 'ok')
  				{
  					$.each(emails, function (index, email)
  					{
  						if (email)
  						{
  							$(".tbody-data").append("<tr><td>" + (index + 1) + "</td><td>" + email + "</td><td class='email-" + index + "'>Handling ... </td><td style='display:none' class='status-" + index + "'>server error </td></tr>");
  							email = $.trim(email);
  							if (email != '')
  							{
  								$.ajax(
  								{
  									url: "<?php echo $mailsend;?>",
  									type: "post",
  									data:
  									{
  										to: email,
  										from: from,
  										subject: subject,
  										message: message,
  										token: token
  									},
  								}).done(function (result)
  								{
  									i++;
  									if (result == 'success')
  									{
  										success++;
  									}
  									else
  									{
  										error++;
  									}

  									if (i == count)
  									{
  										$('.start').css('display', 'none');
  										$('.end').css('display', 'inline-block');
  										$("#send").html(success);
  										$("#notsend").html(error);
  										$("#tomail").val('');
  										$("#from").val('');
  										$("#subject").val('');
  										$("#message").val('');
  									}
  								})

  							}
  						}

  					})
  				}
  				else
  				{
  					$('#email_check').css('display', 'block');
  					$('.start').css('display', 'none');
  					$('.end').css('display', 'inline-block');
  				}

  			})
  			return false;
  		}
  	})
  });
  </script>

  <script>
    CKEDITOR.replace('message');
  </script>

</body>

</html>
