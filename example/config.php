<?php
/**
 * IP camera configuration
 */
$user = 'user'; // the user name for access to the webinterface of the camera
$password = 'password'; // the password for access to the webinterface of the camera
$camera_ip = '10.1.1.1'; // ip of the webinterface of the camera

/**
 * E-Mail configuration parameters
 */
$smtpServer = 'smtp.provider.com';
$senderNickname = 'Camera1';
$smtpPort = 465;
$senderAddress = 'user@provider.com';
$smtpPassword = 'password';
$recipientAddress1 = 'user@provider1.com';
//$recipientAddress2 = '';
//$recipientAddress3 = '';
$interval = '30 Seconds';
$ssl = 1;
$attachment = 'picture';
