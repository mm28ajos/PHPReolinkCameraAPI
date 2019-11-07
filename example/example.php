<?php
require_once('vendor/autoload.php');

require_once('config.php');

// create a new Reolink_API object
$reolink_connection = new \Reolink_API\Client($user, $password, $camera_ip);

// You may enable debugging mode for a verbose object
//$reolink_connection->setDebug(true);

// login to the camera
$loginresult = $reolink_connection->login();

if ($loginresult)
{
      // Here, the e-mail parameters array is created
      $emailParameters = array("smtpServer" => $smtpServer,
                              "senderNickname" => $senderNickname,
                              "smtpPort" => $smtpPort,
                              "senderAddress" => $senderAddress,
                              // You may omit some of the settings as you only have to add those to the array you want to change
                              //"smtpPassword" => $smtpPassword,
                              "recipientAddress1" => $recipientAddress1,
                              //"recipientAddress2" => $recipientAddress2,
                              //"recipientAddress3" => $recipientAddress3,
                              "interval" => $interval,
                              "ssl" => $ssl,
                              "attachment" => $attachment);

      // pass the parameters array to the setEmailSettings function
      $reolink_connection->setEmailSettings($emailParameters);

      // disable the e-mail send on a detected motion
      $reolink_connection->toggleMotionEmail(false);

      // disable the push notification to the Reolink app on a detected motion
      $reolink_connection->toggleMotionPush(false);

      // disable the FTP upload on a detected motion
      $reolink_connection->toggleFTPUpload(false);

      // disable the near infrared lights
      $reolink_connection->toggleInfraredLight(false);

      // get email settings from camera
      $emailSettings = $reolink_connection->getEmailSettings();

      // get FTP settings from camera
      $FTPsettings = $reolink_connection->getFTPSettings();

      // get FTP settings from camera
      $pushSettings = $reolink_connection->getPushSettings();

      // logout from the camera
      $reolink_connection->logout();
}
