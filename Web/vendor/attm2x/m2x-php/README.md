[![Latest Stable Version](https://poser.pugx.org/attm2x/m2x-php/v/stable.svg)](https://packagist.org/packages/attm2x/m2x-php)
[![License](https://poser.pugx.org/attm2x/m2x-php/license.svg)](https://packagist.org/packages/attm2x/m2x-php)

# AT&T's M2X PHP Client

[AT&T M2X](http://m2x.att.com) is a cloud-based fully managed time-series data storage service for network connected machine-to-machine (M2M) devices and the Internet of Things (IoT). 

The [AT&T M2X API](https://m2x.att.com/developer/documentation/overview) provides all the needed operations and methods to connect your devices to AT&T's M2X service. This library aims to provide a simple wrapper to interact with the AT&T M2X API for [PHP](http://php.net). Refer to the [Glossary of Terms](https://m2x.att.com/developer/documentation/glossary) to understand the nomenclature used throughout this documentation.

Getting Started
==========================
1. Signup for an [M2X Account](https://m2x.att.com/signup).
2. Obtain your _Master Key_ from the Master Keys tab of your [Account Settings](https://m2x.att.com/account) screen.
2. Create your first [Device](https://m2x.att.com/devices) and copy its _Device ID_.
3. Review the [M2X API Documentation](https://m2x.att.com/developer/documentation/overview).

## Installation

Simply add a dependency on attm2x/m2x-php to your project's composer.json file if you use Composer to manage the dependencies of your project.

```json
{
  "require": {
    "attm2x/m2x-php": "~3.0"
  }
}
```

## Usage

In order to communicate with the M2X API, you need an instance of [M2X:](src//M2X.php). You need to pass your API key in the constructor to access your data.

```php
use Att\M2X\M2X;

$m2x = new M2X("<YOUR-API-KEY>"");
```

This provides an interface to your data in M2X

- [Distribution](src/Distribution.php)
  ```php
  $distribution = $m2x->distribution("<DISTRIBUTION-ID>");

  $distributions = $m2x->distributions();
  ```

- [Device](src/Device.rb)
  ```php
  $device = $m2x->device("<DEVICE-ID>");

  $devices = $m2x->devices();
  ```

- [Key](src/Key.rb)
  ```ruby
  $key = $m2x->key("<KEY-TOKEN>");

  $keys = $m2x->keys();
  ```

Refer to the documentation on each class for further usage instructions.

## Example

In order to run this example, you will need a `Device ID` and `API Key`. If you don't have any, access your M2X account, create a new [Device](https://m2x.att.com/devices), and copy the `Device ID` and `API Key` values. The following script will send your CPU load average to three different streams named `load_1m`, `load_5m` and `load_15`. Check that there's no need to create a stream in order to write values into it.

In order to execute this script, run:

> API_KEY=<YOUR-API-KEY> DEVICE=<YOUR-DEVICE-ID> php examples/uptime.php

```php
use Att\M2X\M2X;
use Att\M2X\Error\M2XException;

$apiKey = getenv("API_KEY");
$deviceId  = getenv("DEVICE");

$m2x = new M2X($apiKey);

# Get the device
$device = $m2x->device($deviceId);

# Create the streams if they don't exist yet
$device->updateStream('load_1m');
$device->updateStream('load_5m');
$device->updateStream('load_15m');

while (true) {
  list($load_1m, $load_5m, $load_15m) = sys_getloadavg();
  $now = date('c');

  $values = array(
    'load_1m'  => array(array('value' => $load_1m,  'timestamp' => $now)),
    'load_5m'  => array(array('value' => $load_5m,  'timestamp' => $now)),
    'load_15m' => array(array('value' => $load_15m, 'timestamp' => $now))
  );

  try {
    $device->postUpdates($values);
  } catch (M2XException $ex) {
    echo 'Error: ' . $ex->getMessage();
    echo $ex->response->raw;
    break;
  }

  sleep(10);
}
```

## Versioning

This lib aims to adhere to [Semantic Versioning 2.0.0](http://semver.org/). As a summary, given a version number `MAJOR.MINOR.PATCH`:

1. `MAJOR` will increment when backwards-incompatible changes are introduced to the client.
2. `MINOR` will increment when backwards-compatible functionality is added.
3. `PATCH` will increment with backwards-compatible bug fixes.

Additional labels for pre-release and build metadata are available as extensions to the `MAJOR.MINOR.PATCH` format.

**Note**: the client version does not necessarily reflect the version used in the AT&T M2X API.

## License

This lib is provided under the MIT license. See [LICENSE](LICENSE) for applicable terms.
