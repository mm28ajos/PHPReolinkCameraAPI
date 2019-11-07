## Reolink IP camera API client class

A PHP class which provides access to Reolink's IP cameras.

This class can be installed manually or using composer/[packagist](https://packagist.org/packages/mm28ajos/PHPReolinkCameraAPI) for easy inclusion in your projects.

## Requirements

- a Reolink camera (test with RLC-420-5MP, Build No. build 19061408, Hardware No. IPC_51516M5M, Configuration Version v2.0.0.0, Firmware Version v2.0.0.448_19061408)
- a web server with PHP installed (tested with PHP cli Version 7.3.11-1~deb10u1)
- network connectivity between this web server and the camera and port (normally TCP port 80)

## Installation ##

You can use [Composer](#composer), [Git](#git) or simply [Download the Release](#download-the-release) to install the API client class.

### Composer

The preferred method is via [composer](https://getcomposer.org). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

Once composer is installed, simply execute this command from the shell in your project directory:

```sh
composer require mm28ajos/php-reolink-camera-api
```

 Or you can manually add the package to your composer.json file:

```javascript
{
    "require": {
        "mm28ajos/php-reolink-camera-api": "^1.0"
    }
}
```

Finally, be sure to include the autoloader in your code:

```php
require_once('vendor/autoload.php');
```

### Git

Execute the following `git` command from the shell in your project directory:

```sh
git clone https://github.com/mm28ajos/PHPReolinkCameraAPI.git
```

When git is done cloning, include the file containing the class like so in your code:

```php
require_once('path/to/src/Client.php');
```

### Download the Release

If you prefer not to use composer or git, you can simply [download the package](https://github.com/mm28ajos/PHPReolinkCameraAPI/archive/master.zip), uncompress the zip file, then include the file containing the class in your code like so:

```php
require_once('path/to/src/Client.php');
```

## Example usage

A basic example how to use the class:

```php
/**
 * load the class using the composer autoloader
 */
require_once('vendor/autoload.php');

/**
 * initialize the Reolink API connection class, log in to the controller and request disable the motion detection e-mail alert
 * (this example assumes you have already assigned the correct values to the variables used)
 */
$reolink_connection = new \Reolink_API\Client($user, $password, $camera_ip);
$login            = $reolink_connection->login();
if ($login)
{
  $results          = $reolink_connection->toggleMotionEmail(false); // returns a PHP boolean to signale the success/failure of toggeling the motion e-mail alert
  $logout            = $reolink_connection->$logout();
}
```

Please refer to the `examples/` directory for some more detailed examples which you can use as a starting point for your own PHP code.

### API Requests Implementation:

GET:
- [X] Login
- [X] Logout
- [ ] Display -> OSD
- [ ] Recording -> Encode (Clear and Fluent Stream)
- [ ] Recording -> Advance (Scheduling)
- [ ] Network -> General
- [ ] Network -> Advanced
- [ ] Network -> DDNS
- [ ] Network -> NTP
- [x] Network -> E-mail
- [x] Network -> FTP
- [x] Network -> Push
- [ ] Network -> WIFI
- [ ] Alarm -> Motion
- [ ] System -> General
- [ ] System -> DST
- [ ] System -> Information
- [ ] System -> Maintenance
- [ ] System -> Performance
- [ ] System -> Reboot
- [ ] User -> Online User
- [ ] User -> Add User
- [ ] User -> Manage User
- [ ] Device -> HDD/SD Card
- [ ] Zoom
- [ ] Focus
- [ ] Image (Brightness, Contrass, Saturation, Hue, Sharp, Mirror, Rotate)
- [x] Near Infraread Light
- [ ] Advanced Image (Anti-flicker, Exposure, White Balance, DayNight, Backlight, 3D-NR)
- [ ] Image Data -> "Snap" Frame from Video Stream

SET:
- [ ] Display -> OSD
- [ ] Recording -> Encode (Clear and Fluent Stream)
- [ ] Recording -> Advance (Scheduling)
- [ ] Network -> General
- [ ] Network -> Advanced
- [ ] Network -> DDNS
- [ ] Network -> NTP
- [x] Network -> E-mail
- [x] Network -> FTP
- [x] Network -> Push
- [ ] Network -> WIFI
- [ ] Alarm -> Motion
- [ ] System -> General
- [ ] System -> DST
- [ ] System -> Reboot
- [ ] User -> Online User
- [ ] User -> Add User
- [ ] User -> Manage User
- [ ] Device -> HDD/SD Card
- [ ] Zoom
- [ ] Focus
- [ ] Image (Brightness, Contrass, Saturation, Hue, Sharp, Mirror, Rotate)
- [x] Near Infraread Light
- [ ] Advanced Image (Anti-flicker, Exposure, White Balance, DayNight, Backlight, 3D-NR)

## Contribute

If you would like to contribute code (improvements), please open an issue and include your code there or else create a pull request.

## Credits

This class is based on the initial work by the following developer:

- klin34970: https://www.domoticz.com/forum/viewtopic.php?t=28721

The Readme is based on:

- https://github.com/Art-of-WiFi/UniFi-API-client/blob/master/README.md
- https://github.com/Benehiko/ReolinkCameraAPI/blob/master/README.md

## Important Note

All of the functions in this API client class are not officially supported by Reolink and as such, may not be supported in future versions of the Reolink cameras.
