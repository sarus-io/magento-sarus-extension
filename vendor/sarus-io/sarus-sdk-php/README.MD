# sarus-sdk-php
Sarus SDK for PHP

## Install

The recommended way to install riselms/sarus-sdk-php is [through composer](http://getcomposer.org).

```bash
composer require riselms/sarus-sdk-php
```

## Usage

Don't forget to autoload:

```php
<?php
require 'vendor/autoload.php';
```

### Initialize Sdk object:

```php
use Sarus\SdkFactory;
use Sarus\Config;

$config = new Config(
   'SECRET',               // Requred  - API secret for sarus    
   'https://api.sarus.io', // Optional - base url
   30,                     // Optional - timeout
   true                    // Optional - ssl verify
);
//OR
$config = Config::fromArray([
   'secret'    => 'SECRET',               // Requred  - API secret for sarus    
   'baseUri'   => 'https://api.sarus.io', // Optional - base url
   'timeout'   => 30,                     // Optional - timeout
   'sslVerify' => true                    // Optional - ssl verify
]);
$factory = new SdkFactory();
$sdk = $factory->create($config);
 
// If you want to log request and response:
 $logger = new Logger(); //class implemented \Psr\Log\LoggerInterface
 $logFormat = \GuzzleHttp\MessageFormatter::CLF; \\Formating of log message
 $logLevel = \Psr\Log\LogLevel::INFO; //By default log level is info
 $sdk = $factory->createWithLogger($config, $logger, $logFormat, $logLevel);
```
### Ping and check auth key:
```php
try {

    $sdk->ping();

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Purchase product:
```php

$sarusProductUuids = ['uuid1', 'uuid2'];
$user = new \Sarus\User(
    'test@test.com',
    'test_name',
    'test-last_name',
    96
);
$user
    ->setCity('Milwaukee')
    ->setCountry('USA')
    ->setRegion('Wisconsin')
    ->setAddress1('53 Creek Lane')
    ->setPostalCode('53204')
;
try {

    $sdk->purchaseProduct($sarusProductUuids, $user);

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Unlink product:

```php

$sarusPoductUuid = 'uuid1';
try {

    $sdk->unlinkProduct($sarusPoductUuid);

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Get list of courses where user is enrolled:
```php
$email = 'testemal@user.com';
try {

    $response = $sdk->listEnrollments($email);
    
    foreach($response->get('data') as $enrollment) {
        $enrollment['course_uuid'];
        $enrollment['title'];
        $enrollment['description'];
        $enrollment['image_src'];
        $enrollment['url'];
    }

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Deactivate enrollment of a user:
```php
$email = 'testemal@user.com';
$sarusPoductUuids = ['uuid1', 'uuid2'];
try {

    $sdk->deactivateEnrollments($email, $sarusPoductUuids);

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Run any request through handleRequest method:
```php
$request = \Sarus\Request\CustomRequest::fromArray([
    'path'   => '/v1/ping',
    'method' => 'GET',
    'body'   => null,
]);

try {

    $resposne = $sdk->handleRequest($request);

} catch (\Sarus\Client\Exception\HttpException $e) {
}
```

### Requests Serialization
```php
$request = \Sarus\Request\CustomRequest::fromArray([
    'path'   => '/v1/ping',
    'method' => 'GET',
    'body'   => null,
]);

$serialized = serialize($request);
$unserializedRequest = unserialize($serializaed);
```

### Requests transformation
- json
```php
$request = \Sarus\Request\CustomRequest::fromArray([
    'path'   => '/v1/ping',
    'method' => 'GET',
    'body'   => null,
]);

$serialized = \json_encode($request);

$data = \json_decode($serialized, true);
$unserializedRequest = \Sarus\Request\CustomRequest::fromArray($data);
```

- array
```php
$request = \Sarus\Request\CustomRequest::fromArray([
    'path'   => '/v1/ping',
    'method' => 'GET',
    'body'   => null,
]);

$serialized = $request->toArray();
$unserializedRequest = \Sarus\Request\CustomRequest::fromArray($data);
```

#### Information which could be taken from Sarus\Client\Exception\HttpException:
```php
try {

// request

} catch (\Sarus\Client\Exception\HttpException $e) {
    // The exceptions is thrown if http request is not successful

    $e->getMessage();           // returns either grabbed message from response body or reason phrase
    $e->hasResponse();          // returns bool, if exception holds response object
    $e->getResponse();          // returns \Psr\Http\Message\ResponseInterface
    $e->getRequest();           // returns  \Psr\Http\Message\RequestInterface
}
```

#### Run tests
- Run command `bin/phpunit`

