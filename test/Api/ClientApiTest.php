<?php
/**
 * ClientApiTest
 */

namespace OpenAPI\Client\Test\Api;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenAPI\Client\Api\ClientApi;
use OpenAPI\Client\ApiException;
use OpenAPI\Client\Model\AuthenticationRequest;
use OpenAPI\Client\Model\AuthenticationRequestTarget;
use OpenAPI\Client\Model\AuthenticationResponse;
use OpenAPI\Client\Model\Evaluation;
use OpenAPI\Client\Model\FeatureConfig;
use OpenAPI\Client\Model\Segment;

/**
 * @covers \OpenAPI\Client\Api\ClientApi
 */
class ClientApiTest extends ApiTestCase
{
    private const ENV = 'env-uuid';

    private function api(array $queue): ClientApi
    {
        return new ClientApi($this->client($queue), $this->config());
    }

    private function featureConfigJson(string $feature = 'flag1'): array
    {
        return [
            'project' => 'proj',
            'environment' => 'env',
            'feature' => $feature,
            'state' => 'on',
            'kind' => 'boolean',
            'variations' => [
                ['identifier' => 'true', 'value' => 'true'],
                ['identifier' => 'false', 'value' => 'false'],
            ],
            'defaultServe' => ['variation' => 'true'],
            'offVariation' => 'false',
        ];
    }

    public function testConstructorDefaults(): void
    {
        $api = new ClientApi();

        $this->assertSame(0, $api->getHostIndex());
        $this->assertNotNull($api->getConfig());
        $api->setHostIndex(1);
        $this->assertSame(1, $api->getHostIndex());
    }

    public function testAuthenticate(): void
    {
        $api = $this->api([$this->json(['authToken' => 'tok'])]);

        $request = new AuthenticationRequest([
            'api_key' => 'my-key',
            'target' => new AuthenticationRequestTarget(['identifier' => 'user1', 'name' => 'User 1']),
        ]);
        $response = $api->authenticate($request);

        $this->assertInstanceOf(AuthenticationResponse::class, $response);
        $this->assertSame('tok', $response->getAuthToken());

        $sent = $this->assertRequest('POST', '/client/auth');
        $this->assertStringContainsString('application/json', $sent->getHeaderLine('Content-Type'));
        $body = json_decode((string) $sent->getBody(), true);
        $this->assertSame('my-key', $body['apiKey']);
        $this->assertSame('user1', $body['target']['identifier']);
    }

    public function testAuthenticateWithoutBody(): void
    {
        $api = $this->api([$this->json(['authToken' => 'tok'])]);

        $this->assertSame('tok', $api->authenticate()->getAuthToken());
        $this->assertRequest('POST', '/client/auth');
    }

    public function testGetAllSegments(): void
    {
        $api = $this->api([$this->json([
            ['identifier' => 's1', 'name' => 'Segment 1', 'environment' => 'env'],
            ['identifier' => 's2', 'name' => 'Segment 2', 'environment' => 'env'],
        ])]);

        $segments = $api->getAllSegments(self::ENV, 'cluster-1');

        $this->assertContainsOnlyInstancesOf(Segment::class, $segments);
        $this->assertSame(['s1', 's2'], array_map(fn ($s) => $s->getIdentifier(), $segments));

        $request = $this->assertRequest('GET', '/client/env/env-uuid/target-segments', ['cluster' => 'cluster-1']);
        $this->assertSame('Bearer jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testGetSegmentByIdentifier(): void
    {
        $api = $this->api([$this->json(['identifier' => 's1', 'name' => 'Segment 1'])]);

        $segment = $api->getSegmentByIdentifier('s1', self::ENV);

        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertSame('Segment 1', $segment->getName());
        $this->assertRequest('GET', '/client/env/env-uuid/target-segments/s1');
    }

    public function testGetFeatureConfig(): void
    {
        $api = $this->api([$this->json([$this->featureConfigJson('f1'), $this->featureConfigJson('f2')])]);

        $configs = $api->getFeatureConfig(self::ENV);

        $this->assertContainsOnlyInstancesOf(FeatureConfig::class, $configs);
        $this->assertCount(2, $configs);
        $this->assertSame('f2', $configs[1]->getFeature());
        $this->assertRequest('GET', '/client/env/env-uuid/feature-configs');
    }

    public function testGetFeatureConfigByIdentifier(): void
    {
        $api = $this->api([$this->json($this->featureConfigJson('flag1'))]);

        $config = $api->getFeatureConfigByIdentifier('flag1', self::ENV, 'c1');

        $this->assertInstanceOf(FeatureConfig::class, $config);
        $this->assertSame('flag1', $config->getFeature());
        $this->assertSame('boolean', $config->getKind());
        $this->assertRequest('GET', '/client/env/env-uuid/feature-configs/flag1', ['cluster' => 'c1']);
    }

    public function testGetEvaluations(): void
    {
        $api = $this->api([$this->json([
            ['flag' => 'f1', 'value' => 'true', 'kind' => 'boolean', 'identifier' => 'true'],
        ])]);

        $evaluations = $api->getEvaluations(self::ENV, 'target1');

        $this->assertCount(1, $evaluations);
        $this->assertInstanceOf(Evaluation::class, $evaluations[0]);
        $this->assertSame('f1', $evaluations[0]->getFlag());
        $this->assertRequest('GET', '/client/env/env-uuid/target/target1/evaluations');
    }

    public function testGetEvaluationByIdentifier(): void
    {
        $api = $this->api([$this->json(['flag' => 'f1', 'value' => 'true', 'kind' => 'boolean', 'identifier' => 'true'])]);

        $evaluation = $api->getEvaluationByIdentifier(self::ENV, 'f1', 'target1');

        $this->assertInstanceOf(Evaluation::class, $evaluation);
        $this->assertSame('true', $evaluation->getValue());
        $this->assertRequest('GET', '/client/env/env-uuid/target/target1/evaluations/f1');
    }

    public function testStreamSendsApiKeyHeader(): void
    {
        $api = $this->api([new Response(200)]);

        $api->stream('my-api-key', 'c1');

        $request = $this->assertRequest('GET', '/stream', ['cluster' => 'c1']);
        $this->assertSame('my-api-key', $request->getHeaderLine('API-Key'));
    }

    public function testPathParametersAreEncoded(): void
    {
        $api = $this->api([$this->json(['flag' => 'f', 'value' => 'v', 'kind' => 'string', 'identifier' => 'i'])]);

        $api->getEvaluationByIdentifier(self::ENV, 'my flag', 'a/b');

        $this->assertStringContainsString(
            '/target/a%2Fb/evaluations/my%20flag',
            $this->lastRequest()->getUri()->getPath()
        );
    }

    /**
     * @dataProvider requiredParameterCalls
     */
    public function testMissingRequiredParameterThrows(string $method, array $args): void
    {
        $api = $this->api([]);

        $this->expectException(\InvalidArgumentException::class);
        $api->$method(...$args);
    }

    public static function requiredParameterCalls(): array
    {
        return [
            'getAllSegments' => ['getAllSegments', [null]],
            'getSegmentByIdentifier identifier' => ['getSegmentByIdentifier', [null, 'env']],
            'getSegmentByIdentifier env' => ['getSegmentByIdentifier', ['s', null]],
            'getFeatureConfig' => ['getFeatureConfig', [null]],
            'getFeatureConfigByIdentifier identifier' => ['getFeatureConfigByIdentifier', [null, 'env']],
            'getEvaluations env' => ['getEvaluations', [null, 't']],
            'getEvaluations target' => ['getEvaluations', ['env', null]],
            'getEvaluationByIdentifier' => ['getEvaluationByIdentifier', ['env', null, 't']],
            'stream' => ['stream', [null]],
        ];
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $api = $this->api([$this->json(['code' => '404', 'message' => 'not found'], 404)]);

        try {
            $api->getFeatureConfigByIdentifier('missing', self::ENV);
            $this->fail('ApiException expected');
        } catch (ApiException $e) {
            $this->assertSame(404, $e->getCode());
            $this->assertStringContainsString('not found', (string) $e->getResponseBody());
        }
    }

    public function testUnauthorizedAuthenticateThrowsApiException(): void
    {
        $api = $this->api([$this->json(['code' => '401', 'message' => 'bad key'], 401)]);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);
        $api->authenticate(new AuthenticationRequest(['api_key' => 'bad']));
    }

    public function testServerErrorThrowsApiException(): void
    {
        $api = $this->api([new Response(500, [], 'boom')]);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);
        $api->getAllSegments(self::ENV);
    }

    public function testConnectionFailureThrowsApiException(): void
    {
        $api = $this->api([new ConnectException('down', new Request('GET', 'https://example.test'))]);

        $this->expectException(ApiException::class);
        $api->getAllSegments(self::ENV);
    }

    public function testAsyncVariantResolves(): void
    {
        $api = $this->api([$this->json(['identifier' => 's1', 'name' => 'Segment 1'])]);

        $segment = $api->getSegmentByIdentifierAsync('s1', self::ENV)->wait();

        $this->assertInstanceOf(Segment::class, $segment);
    }

    public function testWithHttpInfoReturnsStatusAndHeaders(): void
    {
        $api = $this->api([$this->json(['identifier' => 's1', 'name' => 'Segment 1'])]);

        [$segment, $status, $headers] = $api->getSegmentByIdentifierWithHttpInfo('s1', self::ENV);

        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertSame(200, $status);
        $this->assertArrayHasKey('Content-Type', $headers);
    }
}
