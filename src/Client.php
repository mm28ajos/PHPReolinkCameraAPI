<?php
/**
 * This file is part of the mm28ajos/php-reolink-camera-api package
 *
 * This Reolink API client is based on the work done by the following developer:
 *   klin34970: https://www.domoticz.com/forum/viewtopic.php?t=28721
 */
namespace Reolink_API;

/**
 * the Reolink API client class
 */
class Client {
    /**
    * private properties
    */
    private $user;
    private $password;
    private $ip;

    private $token;

    private $is_loggedin;

    private $debug;

    /**
     * Construct the Reolink Camera API client class
     * @param string user the user used to login to the Reolink webinterface
     * @param string password the password used to login to the Reolink webinterface
     * @param string user the ip address of the Reolink webinterface
     */
    public function __construct($user, $password, $ip)
    {
        $this->ip = trim($ip);

        if (!filter_var($ip, FILTER_VALIDATE_IP))
        {
            throw new Exception("No valid IP address: " . $this->ip);
        }

        $this->user = trim($user);
        $this->password = trim($password);

        $this->is_loggedin = false;
        $this->debug = false;
    }

    /**
     * Destruct the Reolink Camera API client class
     */
    public function __destruct()
    {
        // if the there still is an active session, logout
        if ($this->is_loggedin)
        {
            $this->logout();
        }
    }

    /**
    * Outputs a message to stdout if debug mode is enabled
    * @param message Message to output
     */
    private function outputStdout($message)
    {
        //If debug option is not set, don't output anything on stdout
        if (!$this->debug) {
            return;
        }

        $date = date("Y/m/d H:i:s O");
        $output = sprintf("[%s][NOTICE] %s\n", $date, $message);
        echo $output;
    }


    /**
     * Set the debug mode.
     * @param boolean enable boolean indicating if the debug mode should be enabled
     * @return boolean a boolean indicating if setting the debug mode was successful
     */
    public function setDebug($enable)
    {
        if (!isset($enable))
        {
            trigger_error('The parameter for setDebug() is missing.');
            return false;
        }

        if (is_bool($enable) === true)
        {
            $this->debug = $enable;
            return true;
        }
        else
        {
          trigger_error('The parameter for setDebug() must be boolean.');
          return false;
        }
    }

    /**
     * Send a request to the camera and return the result.
     * @param string method http method type e.g. POST or GET
     * @param string cmd command for the URL
     * @param string payload json payload
     * @return response the response object
     */
    private function request($method, $cmd, $payload)
    {
        $client = new \GuzzleHttp\Client([
                                            'base_uri' => 'http://' . $this->ip,
                                            'exceptions' => false,
                                            'CURLOPT_SSL_VERIFYPEER' => false,
                                            'headers'  => [
                                                            'content-type' => 'application/json',
                                                            'Accept' => 'application/json'],
        ]);
        $response = $client->request($method, '/cgi-bin/api.cgi' . '?cmd=' . $cmd, $payload);
        // do some logging
        $this->outputStdout(print_r($response->getBody()->getContents(), true));
        // return response
        return $response;
    }

    /**
     * Login to the camera.
     * @return boolean a boolean indicating if the login was successful
     */
    public function login()
    {
        if ($this->is_loggedin)
        {
            trigger_error("Already logged in");
            return false;
        }

        $loginParameters = array('userName' => $this->user,
                                'password' => $this->password);

        // query camera with parameters and return true if successful else false
        $response = $this->queryCamera($loginParameters, 'User');

        if ($this->checkResponse($response))
        {
            $this->token = json_decode($response->getBody())[0]->value->Token->name;
            $this->is_loggedin = true;
            return true;
        } else {
            return false;
        }
    }

    /**
    * Logout form camera.
    * @return boolean a boolean indicating if the logout was successful
    */
    public function logout()
    {
        if (!$this->is_loggedin)
        {
            // do some logging
            $this->outputStdout("Not logged in");
            return true;
        }

        $logoutParameters = array();

        // query camera with parameters and return true if successful else false
        if ($this->checkResponse($this->queryCamera($logoutParameters, 'Logout')))
        {
            $this->is_loggedin = false;
            unset($this->token);
            return true;
        } else {
            return false;
        }
    }

    /**
    * Build a request and send it to the camera.
    * @param array parameters the parameters to put in the request as json
    * @param string typeOfRequest the type of the request
    * @return response the response object
    */
    private function queryCamera($parameters, $typeOfRequest)
    {
        // if the request is not of type login and login as not been called yet, return false
        if (!strcmp($typeOfRequest, 'Login') && !$this->is_loggedin) {
            trigger_error("Not logged in.");
            return false;
        }

        // build param json for request
        $params = [
                'cmd' => '',
                'action' => '',
                'param' => [
                        $typeOfRequest => []
                ]
            ];

        // depending on the type of request, set request string and allowed parameters
        switch ($typeOfRequest) {
            case 'User':
                 // set request cmd string
                $cmdType = 'Login';

                // set request action int
                $action = 0;

                // define allowed prameter names for Login request
                $allowdParameters = ['userName', 'password'];
                break;
             case 'Logout':
                 // set request cmd string
                $cmdType = 'Logout';

                // set request action int
                $action = 0;

                // define allowed prameter names for Logout request
                $allowdParameters = [];
                break;
            case 'GetEmail':
                  // set request cmd string
                 $cmdType = 'GetEmail';

                 // set request action int
                 $action = 1;

                 // define allowed prameter names for Logout request
                 $allowdParameters = [];
                 break;
             case 'GetPush':
                   // set request cmd string
                  $cmdType = 'GetPush';

                  // set request action int
                  $action = 1;

                  // define allowed prameter names for Logout request
                  $allowdParameters = ['channel'];
                  break;
             case 'GetFtp':
                   // set request cmd string
                  $cmdType = 'GetFtp';

                  // set request action int
                  $action = 1;

                  // define allowed prameter names for Logout request
                  $allowdParameters = [];
                  break;
            case 'Email':
                // set request cmd string
                $cmdType = 'SetEmail';

                // set request action int
                $action = 0;

                // define allowed prameter names for Email request
                $allowdParameters = ["smtpServer","senderNickname","smtpPort","senderAddress", "smtpPassword", "recipientAddress1", "recipientAddress2", "recipientAddress3", "interval","ssl", "attachment", "schedule"];
                break;
            case 'Push':
                 // set request cmd string
                $cmdType = 'SetPush';

                // set request action int
                $action = 0;

                // define allowed prameter names for Push request
                $allowdParameters = ["schedule"];
                break;
            case 'Ftp':
                 // set request cmd string
                $cmdType = 'SetFtp';

                // set request action int
                $action = 0;

                // define allowed prameter names for Ftp request
                $allowdParameters = ["schedule"];
                break;
            case 'IrLights':
                 // set request cmd string
                $cmdType = 'SetIrLights';

                // set request action int
                $action = 0;

                // define allowed prameter names for Ftp request
                $allowdParameters = ["state"];
                break;
            default:
               trigger_error($typeOfRequest . " not a valid type of request");
               return false;
        }

        // set cmd for request
        $params['cmd'] = $cmdType;

        // set action for request
        $params['action'] = $action;

        // if there are parameters, set them to the param object
        if (count($parameters) > 0)
        {
            // iterate over each parameter and add it to the parameter array
            foreach ($parameters as $key => $value) {
                if (in_array($key, $allowdParameters))
                {
                    // todo: check values depending on type of request
                    $params["param"][$typeOfRequest][$key] = $value;
                }
                else {
                    trigger_error($key . " not an allowed parameter for request type " . $typeOfRequest);
                    return false;
                }
            }
        }

        // do some logging
        $this->outputStdout(print_r($params, true));

        // send request and get response
        $response = $this->request('POST', $cmdType . '&token=' . $this->token, [
                        'debug' => $this->debug,
                        'json' => [
                                $params
                        ]
                ]
        );

        // return response
        return $response;
    }

    /**
    * Check if the response indicates a successful request. Return true if successful, else false.
    * @param response response the response object from the camera
    * @return boolean a boolean indicating if the response was successful
    */
    private function checkResponse($response)
    {
        $data = json_decode($response->getBody());

        if (!$data)
        {
            trigger_error("Error parsing response");
            return false;
        }

        if (!isset($data[0]))
        {
            trigger_error("No data in response");
            return false;
        }

        if (isset($data[0]->error))
        {
            trigger_error("Request failed: " . $data[0]->error->detail);
            return false;
        }
        else
        {
            $this->outputStdout("Request successful");
            return true;
        }
    }

    /**
     * Set e-mail parameters.
     * @param array emailParameters the array with the parameters to set i.e. the array keys are the names of the email parameter and the array values are the values of the e-mail parameter
     * @return boolean a boolean indicating if setting the e-mail settings was successful
     */
    public function setEmailSettings($emailParameters)
    {
        if (!isset($emailParameters)) {
            trigger_error("The parameter 'emailParameters' for setEmailSettings() is missing.");
            return false;
        }

        if (!is_array($emailParameters)) {
            trigger_error("The parameter 'emailParameters' for setEmailSettings() must be an array");
            return false;
        }

        // query camera with parameters and return true if successful else false
        return $this->checkResponse($this->queryCamera($emailParameters, 'Email'));
    }

    /**
     * Enable or disable the motion detection e-mail alert.
     * @param boolean enable a boolean which is true if the motion detection e-mail alert should be enabled else false.
     * @return boolean a boolean indicating if toggeling the motion detection e-mail alert setting was successful
     */
    public function toggleMotionEmail($enable)
    {
        if (!isset($enable)) {
            trigger_error('The parameter for toggleMotionEmail() is not set.');
            return false;
        }

        if (is_bool($enable)) {
            if ($enable)
            {
                $action = 1;
            } else {
                $action = 0;
            }
        } else {
            trigger_error('The parameter for toggleMotionEmail() must be boolean');
            return false;
        }

        $emailMotionParameters = array("schedule" => ['enable' => intval($action)]);

        // query camera with parameters and return true if successful else false
        return $this->checkResponse($this->queryCamera($emailMotionParameters, 'Email'));
    }

    /**
     * Enable or disable the motion detection FTP upload.
     * @param boolean enable a boolean which is true if the motion detection FTP upload should be enabled else false.
     * @return boolean a boolean indicating if toggeling the FTP upload on motion detection setting was successful
     */
    public function toggleFTPUpload($enable)
    {
        if (!isset($enable)) {
            trigger_error('The parameter for toggleFTPUpload() is not set.');
            return false;
        }

        if (is_bool($enable)) {
            $action = $enable ? 1 : 0;
        } else {
            trigger_error('The parameter for toggleFTPUpload() must be boolean');
            return false;
        }

        $toggleFtpParameters = array("schedule" => ['enable' => intval($action)]);

        // query camera with parameters and return true if successful else false
        return $this->checkResponse($this->queryCamera($toggleFtpParameters, 'Ftp'));
    }

    /**
     * Enable or disable the the near infrared light.
     * @param boolean enable a boolean which is true if the near infrared light should be enabled automatically if required and switched of if not required else switch off near infrared light permanently.
     * @return boolean a boolean indicating if toggeling the near infrared setting was successful
     */
    public function toggleInfraredLight($enable)
    {
        if (!isset($enable)) {
            trigger_error('The parameter for toggleInfraredLight() is not set.');
            return false;
        }

        if (is_bool($enable)) {
            $state = $enable ? 'Auto' : 'Off';
        } else {
            trigger_error('The parameter for toggleInfraredLight() must be boolean');
            return false;
        }

        $toggleNRParameters = array('state' => $state);

        // query camera with parameters and return true if successful else false
        return $this->checkResponse($this->queryCamera($toggleNRParameters, 'IrLights'));
    }

    /**
     * Enable or disable the the motion detection push notification for Reolink apps.
     * @param boolean enable a boolean which is true if the push notification for a motion detection to a Reolink App should be enabled else false.
     * @return boolean a boolean indicating if toggeling the motion detection push setting was successful
     */
    public function toggleMotionPush($enable)
    {
        if (!isset($enable)) {
            trigger_error('The parameter for toggleMotionPush() is not set.');
            return false;
        }

        if (is_bool($enable)) {
            $table = $enable ? '111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111' : '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
        } else {
            trigger_error('The parameter for toggleMotionPush() must be boolean');
            return false;
        }

        $pushMotionParameters = array("schedule" => ['table' => $table]);

        // query camera with parameters and return true if successful else false
        return $this->checkResponse($this->queryCamera($pushMotionParameters, 'Push'));
    }

    /**
     * Get e-mail settings from camera
     * @return JSON object with the e-mail settings from the camera and false in case of error
     */
    public function getEmailSettings()
    {
        if (!$this->is_loggedin)
        {
            trigger_error("Not logged in");
            return false;
        }

        $getEmailParameters = array();

        // query camera with parameters and return true if successful else false
        $response = $this->queryCamera($getEmailParameters, 'GetEmail');

        if ($this->checkResponse($response))
        {
            return json_decode($response->getBody())[0]->value->Email;
        } else {
            return false;
        }
    }

    /**
     * Get FTP settings from camera
     * @return JSON object with the FTP settings from the camera and false in case of error
     */
    public function getFTPSettings()
    {
        if (!$this->is_loggedin)
        {
            trigger_error("Not logged in");
            return false;
        }

        $getFTPParameters = array();

        // query camera with parameters and return true if successful else false
        $response = $this->queryCamera($getFTPParameters, 'GetFtp');

        if ($this->checkResponse($response))
        {
            return json_decode($response->getBody())[0]->value->Ftp;
        } else {
            return false;
        }
    }

    /**
     * Get push settings from camera
     * @return JSON object with the push settings from the camera and false in case of error
     */
    public function getPushSettings()
    {
        if (!$this->is_loggedin)
        {
            trigger_error("Not logged in");
            return false;
        }

        $getPushParameters = array('channel' => intval(0));

        // query camera with parameters and return true if successful else false
        $response = $this->queryCamera($getPushParameters, 'GetPush');

        if ($this->checkResponse($response))
        {
            return json_decode($response->getBody())[0]->value->Push;
        } else {
            return false;
        }
    }
}
