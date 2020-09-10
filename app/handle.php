<?php
include 'pagename.php';
session_start();
extract($_POST);
if (isset($token)) { //check token value for csrf protection;
    //verify imported email addresses
    function verifyEmail($toemail, $fromemail, $getdetails = false) {
      $details = '';

      // Remove all illegal characters from email
      $email = filter_var($toemail, FILTER_SANITIZE_EMAIL);
      // Validate e-mail
      if (filter_var($toemail, FILTER_VALIDATE_EMAIL)) {
        // Get the domain of the email recipient
        $email_arr = explode('@', $toemail);
        $domain = array_slice($email_arr, -1);
        $domain = $domain[0];


        // Trim [ and ] from beginning and end of domain string, respectively
        $domain = ltrim($domain, '[');
        $domain = rtrim($domain, ']');


        if ('IPv6:' == substr($domain, 0, strlen('IPv6:'))) {
            $domain = substr($domain, strlen('IPv6') + 1);
        }

        $mxhosts = array();
            // Check if the domain has an IP address assigned to it
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            $mx_ip = $domain;
        } else {
            // If no IP assigned, get the MX records for the host name
            getmxrr($domain, $mxhosts, $mxweight);
        }

        if (!empty($mxhosts)) {
            $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
        } else {
            // If MX records not found, get the A DNS records for the host
            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $record_a = dns_get_record($domain, DNS_A);
                 // else get the AAAA IPv6 address record
            } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $record_a = dns_get_record($domain, DNS_AAAA);
            }

            if (!empty($record_a)) {
                $mx_ip = $record_a[0]['ip'];
            } else {
              $mxhosts = array();
                  // Check if the domain has an IP address assigned to it
              $domain = 'mail.'.$domain;
              if (filter_var($domain, FILTER_VALIDATE_IP)) {
                  $mx_ip = $domain;
              } else {
                  // If no IP assigned, get the MX records for the host name
                  getmxrr($domain, $mxhosts, $mxweight);
              }

              if (!empty($mxhosts)) {
                  $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
              } else {
                  // If MX records not found, get the A DNS records for the host
                  if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                      $record_a = dns_get_record($domain, DNS_A);
                       // else get the AAAA IPv6 address record
                  } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                      $record_a = dns_get_record($domain, DNS_AAAA);
                  }

                  if (!empty($record_a)) {
                      $mx_ip = $record_a[0]['ip'];
                  } else {
                    $domain = 'email.'.$domain;
                    $mxhosts = array();
                        // Check if the domain has an IP address assigned to it
                    if (filter_var($domain, FILTER_VALIDATE_IP)) {
                        $mx_ip = $domain;
                    } else {
                        // If no IP assigned, get the MX records for the host name
                        getmxrr($domain, $mxhosts, $mxweight);
                    }

                    if (!empty($mxhosts)) {
                        $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
                    } else {
                        // If MX records not found, get the A DNS records for the host
                        if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                            $record_a = dns_get_record($domain, DNS_A);
                             // else get the AAAA IPv6 address record
                        } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                            $record_a = dns_get_record($domain, DNS_AAAA);
                        }

                        if (!empty($record_a)) {
                            $mx_ip = $record_a[0]['ip'];
                        } else {
                            // Exit the program if no MX records are found for the domain host
                            $result = 'fail';
                            $details .= 'No suitable MX records found';

                            return ((true == $getdetails) ? array($result, $details) : $result);
                        }
                    }

                  }
              }

            }
        }

        // Open a socket connection with the hostname, smtp port 25
        $connect = @fsockopen($mx_ip, 25);

        if ($connect) {

                  // Initiate the Mail Sending SMTP transaction
            if (preg_match('/^220/i', $out = fgets($connect, 1024))) {

                          // Send the HELO command to the SMTP server
                fputs($connect, "HELO $mx_ip\r\n");
                $out = fgets($connect, 1024);

                // Send an SMTP Mail command from the sender's email address
                fputs($connect, "MAIL FROM: <$fromemail>\r\n");
                $from = fgets($connect, 1024);

                            // Send the SCPT command with the recepient's email address
                fputs($connect, "RCPT TO: <$toemail>\r\n");
                $to = fgets($connect, 1024);
                $details .= $to."\n";
                $test = $to;

                // Close the socket connection with QUIT command to the SMTP server
                fputs($connect, 'QUIT');
                fclose($connect);

                // The expected response is 250 if the email is valid
                if (!preg_match('/^250/i', $from)) {
                    $result = 'invalid';
                }elseif(!preg_match('/^250/i', $to)){
                  if(strpos($test, 'cannot find your hostname') !== false){
                    $result = 'fail';
                  }else{
                    $result = 'invalid';
                  }

                }
                 else {
                    $result = 'valid';
                }
            }
        } else {
            $result = 'invalid';
            $details .= 'Could not connect to server';
        }
        if ($getdetails) {
            return array($result, $details);
        } else {
            return $result;
        }
      } else {
        $result = 'syntax';
        $details .= 'formate not correct';
        return ((true == $getdetails) ? array($result, $details) : $result);
      }



    }
    $toemail = $email;
    $fromemail = $_SESSION['email']; //get admin email
    $results = verifyEmail($toemail, $fromemail, $getdetails = true);
    if ($results[0] == 'valid') {
        $result = 1;
    } elseif ($results[0] == 'invalid') {
        $result = 0;
    } elseif ($results[0] == 'syntax') {
        $result = 3;
    } else {
        $result = 2;
    }
    header('Content-Type: application/json');
    echo json_encode(['code' => $result, 'index' => $index, 'status' => $results[1], 'mail' => $results[2]]);
} else {
    header("Location: ../".$index);
}
?>
