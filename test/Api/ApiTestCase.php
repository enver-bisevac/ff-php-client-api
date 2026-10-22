<?php
/**
 * ApiTestCase
 *
 * Runs generated API classes against a mocked Guzzle handler and records the
 * outgoing requests.
 */

namespace OpenAPI\Client\Test\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use OpenAPI\Client\Configuration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

abstract class ApiTestCase extends TestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    protected $history = [];

    protected function config(): Configuration
    {
        return (new Configuration())
            ->setHost('https://example.test/api/1.0')
            ->setAccessToken('jwt-token');
    }

    /**
     * @param Response[]|\Throwable[] $queue
     */
    protected function client(array $queue): Client
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::history($this->history));

        return new Client(['handler' => $stack]);
    }

    protected function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'no request was sent');

        return end($this->history)['request'];
    }

    protected function json($data, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
    }

    protected function assertRequest(string $method, string $path, array $query = []): RequestInterface
    {
        $request = $this->lastRequest();
        $this->assertSame($method, $request->getMethod());
        $this->assertSame('/api/1.0' . $path, $request->getUri()->getPath());
        parse_str($request->getUri()->getQuery(), $actual);
        $this->assertEquals($query, $actual);

        return $request;
    }
}
