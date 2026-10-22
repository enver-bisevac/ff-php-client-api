<?php
/**
 * MetricsApiTest
 */

namespace OpenAPI\Client\Test\Api;

use GuzzleHttp\Psr7\Response;
use OpenAPI\Client\Api\MetricsApi;
use OpenAPI\Client\ApiException;
use OpenAPI\Client\Model\Metrics;
use OpenAPI\Client\Model\MetricsData;
use OpenAPI\Client\Model\KeyValue;

/**
 * @covers \OpenAPI\Client\Api\MetricsApi
 */
class MetricsApiTest extends ApiTestCase
{
    private function api(array $queue): MetricsApi
    {
        return new MetricsApi($this->client($queue), $this->config()->setApiKey('api-key', 'sdk-key'));
    }

    public function testPostMetrics(): void
    {
        $api = $this->api([new Response(200)]);

        $metrics = new Metrics([
            'metrics_data' => [new MetricsData([
                'timestamp' => 1700000000,
                'count' => 3,
                'metrics_type' => MetricsData::METRICS_TYPE_FFMETRICS,
                'attributes' => [new KeyValue(['key' => 'featureIdentifier', 'value' => 'flag1'])],
            ])],
        ]);

        $this->assertNull($api->postMetrics('env-uuid', 'cluster-1', $metrics));

        $request = $this->assertRequest('POST', '/metrics/env-uuid', ['cluster' => 'cluster-1']);
        $this->assertSame('Bearer jwt-token', $request->getHeaderLine('Authorization'));
        $this->assertSame('sdk-key', $request->getHeaderLine('api-key'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame(3, $body['metricsData'][0]['count']);
        $this->assertSame('FFMETRICS', $body['metricsData'][0]['metricsType']);
        $this->assertSame('flag1', $body['metricsData'][0]['attributes'][0]['value']);
    }

    public function testPostMetricsWithoutOptionalArguments(): void
    {
        $api = $this->api([new Response(200)]);

        $api->postMetrics('env-uuid');

        $this->assertRequest('POST', '/metrics/env-uuid');
    }

    public function testPostMetricsRequiresEnvironment(): void
    {
        $api = $this->api([]);

        $this->expectException(\InvalidArgumentException::class);
        $api->postMetrics(null);
    }

    public function testPostMetricsErrorThrowsApiException(): void
    {
        $api = $this->api([new Response(401, [], '{"message":"unauthorized"}')]);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);
        $api->postMetrics('env-uuid', null, new Metrics());
    }

    public function testPostMetricsAsync(): void
    {
        $api = $this->api([new Response(200)]);

        $api->postMetricsAsync('env-uuid')->wait();

        $this->assertRequest('POST', '/metrics/env-uuid');
    }
}
