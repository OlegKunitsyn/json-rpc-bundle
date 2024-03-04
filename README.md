# JsonRpcBundle
[JSON-RPC 2.0](https://www.jsonrpc.org/specification) implementation for Symfony.

## Installation
Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Applications that use Symfony Flex
Open a command console, enter your project directory and execute:

```console
composer require olegkunitsyn/json-rpc-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle
Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:
```console
composer require olegkunitsyn/json-rpc-bundle
```

#### Step 2: Enable the Bundle
Then, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:
```php
# config/bundles.php
return [
    // ...
    OlegKunitsyn\JsonRpcBundle::class => ['all' => true],
];
```

Start development server:
```console
php -S localhost:8000 -t public
```

## Configuration
Import the main routing file or create a custom route:
```yaml
# config/routes.yaml
rpc:
    path: /api
    controller: json_rpc_bundle.api.rpc
    methods: POST
```  

## Usage
Tag the services you want to expose and send a json-rpc payload to the RPC endpoint.
The method parameter must follow the convention `{serviceKey}.{method}`
 
```yaml
# config/services.yaml
services:
    App\RpcServices\:
        resource: '../src/RpcServices/'
        tags:
          - 'json_rpc_bundle'   
```

```php
namespace App\RpcServices;

use App\Dto\EchoDto;
use OlegKunitsyn\JsonRpcBundle\Service\AbstractRpcService;

class MyService extends AbstractRpcService
{
    public static function getServiceKey(): string
    {
        return 'myService';
    }
    
    public function echo(EchoDto $dto) : EchoDto
    {
        return $dto;
    }
}
```

```php
namespace App\Dto;

readonly class EchoDto
{
    public function __construct(public string $value)
    {
    }
}
```

### By name (object)
Request:
```console
curl -X POST localhost:8000/api -d '{"method": "myService.echo", "params": {"value": "hello"}, "id": "1", "jsonrpc": "2.0"}' -H 'Content-Type: application/json'
```
Response:
```json
{"jsonrpc":"2.0","result":{"value":"hello"},"id":"1"}
```

### By position (array)
Request:
```console
curl -X POST localhost:8000/api -d '{"method": "myService.echo", "params": ["hello"], "id": "second", "jsonrpc": "2.0"}' -H 'Content-Type: application/json'
```
Response:
```json
{"jsonrpc":"2.0","result":{"value":"hello"},"id":"second"}
```

### Batch
Request:
 ```console
 curl -X POST localhost:8000/api -d '[{"method": "myService.echo", "params": {"value": "hello"}, "id": "1", "jsonrpc": "2.0"}, {"method": "myService.echo", "params": ["hello"], "id": "second", "jsonrpc": "2.0"}]' -H 'Content-Type: application/json'
```
Response:
```json
[{"jsonrpc":"2.0","result":{"value":"hello"},"id":"1"},{"jsonrpc":"2.0","result":{"value":"hello"},"id":"second"}]
```

### Response normalization
Responses are processed by a Symfony normalizer. Use the `RpcNormalizationContext` attribute to specify a normalization context:
```php
namespace App\RpcServices;

use App\Dto\DateTimeDto;
use OlegKunitsyn\JsonRpcBundle\Response\RpcNormalizationContext;
use OlegKunitsyn\JsonRpcBundle\Service\AbstractRpcService;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class MyService extends AbstractRpcService
{
    public static function getServiceKey(): string
    {
        return 'myService';
    }

    #[RpcNormalizationContext([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public function echo(DateTimeDto $timestamp): DateTimeDto
    {
        return $timestamp;
    }
}
```
```php
namespace App\Dto;

readonly class DateTimeDto
{
    public function __construct(public readonly \DateTimeImmutable $timestamp)
    {
    }
}
```

Request:
 ```console
 curl -X POST localhost:8000/api -d '{"method": "myService.echo", "params": {"timestamp": "2012-04-23T18:25:43.511Z"}, "id": "1", "jsonrpc": "2.0"}' -H 'Content-Type: application/json'
```
Response:
```json
{"jsonrpc":"2.0","result":{"timestamp":"2012-04-23"},"id":"1"}
```

## Roadmap
- Performance
- Security
- Code coverage

## Credits
- Gabriel Soullier, [NanofelisJsonRpcBundle](https://github.com/nanofelis/NanofelisJsonRpcBundle) 
