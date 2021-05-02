<?php
declare(strict_types=1);

namespace Tests\Feature;

use DI\Container;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use UnexpectedValueException;
use JsonException;

abstract class AppTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var App
     */
    protected $app;

    /**
     * Bootstrap app.
     *
     * @return void
     * @throws UnexpectedValueException
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $bootstrap = require __DIR__ . '/../../app/bootstrap.php';
        $container = $bootstrap['container'];
        $app = $bootstrap['app'];

        if ($container === null) {
            throw new UnexpectedValueException('Container must be initialized');
        }

        $this->container = $container;
        $this->app = $app;
    }

    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }

    /**
     * Create a server request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array $bodyParams Parameters to send with the request
     *
     * @return ResponseInterface
     */
    protected function createRequest(
        string $method,
        $uri,
        array $bodyParams = [],
        array $queryParams = []
    ): ResponseInterface {
        $request = (new ServerRequestFactory())
            ->createServerRequest($method, $uri)
            ->withParsedBody($bodyParams)
            ->withQueryParams($queryParams);
        return $this->app->handle($request);
    }

    public function post(
        $uri,
        array $bodyParams = [],
        array $queryParams = []
    ): ResponseInterface {
        return $this->createRequest('POST', $uri, $bodyParams, $queryParams);
    }

    public function get(
        $uri,
        array $queryParams = []
    ): ResponseInterface {
        return $this->createRequest('GET', $uri, [], $queryParams);
    }

    /**
     * Create a JSON request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array|null $data The json data
     *
     * @return ServerRequestInterface
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Verify that the given array is an exact match for the JSON returned.
     *
     * @param array $expected The expected array
     * @param ResponseInterface $response The response
     *
     * @throws JsonException
     * @return void
     */
    protected function assertJsonData(array $expected, ResponseInterface $response): void
    {
        $actual = (string)$response->getBody();
        $this->assertSame($expected, (array)json_decode($actual, true, 512, JSON_THROW_ON_ERROR));
    }
}
