<?php

declare(strict_types=1);

namespace Test\Api;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Test\Configuration;
use Test\Exception\RequestException;
use Test\ArrayMapperFactory;
use Test\Exception\Request400Exception;
use Test\Exception\Request404Exception;
use Test\Model\ActionParameter\OrderCreateOrder;
use Test\Model\ActionParameter\OrderGetOrderList;
use Test\Model\ActionParameter\OrderUpdateOrder;
use Test\Model\CreateOrderResponse202;
use Test\Model\GeneralResponse;
use Test\Model\OrderCreate200ResponseList;
use Test\Model\OrderCreate201Response;

class OrderApi
{
    private Configuration           $configuration;
    private ClientInterface         $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface  $streamFactory;
    private array                   $defaultHeaders = [];

    public function __construct(
        Configuration $configuration,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        array $defaultHeaders = []
    ) {
        $this->configuration  = $configuration;
        $this->httpClient     = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory  = $streamFactory;
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * @return OrderCreate200ResponseList => 200
     * @return GeneralResponse => default
     */
    public function getOrderList(OrderGetOrderList $request, ?string $overwriteUrl = null): OrderCreate200ResponseList|GeneralResponse
    {
        $path    = '/orders';
        $payload = $request->hasRequestModel() ? json_encode($request->getRequestModel()) : '';
        $headers = array_merge(
            $this->defaultHeaders,
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        );

        if (!empty($this->configuration->getApiKeyHeaderName())) {
            $headers[$this->configuration->getApiKeyHeaderName()] = $this->configuration->getApiKey();
        }

        foreach ($request->getHeaderParameterGetters() as $parameterName => $getterName) {
            $headers[$parameterName] = $request->$getterName();
        }

        foreach ($request->getPathParameterGetters() as $parameterName => $getterName) {
            $path = str_replace('{' . $parameterName . '}', (string)$request->$getterName(), $path);
        }

        $queryParameters = [];
        foreach ($request->getQueryParameterGetters() as $parameterName => $getterName) {
            $queryParameters[$parameterName] = $request->$getterName();
        }

        $path .= strpos($path, '?') === false
            ? '?' . http_build_query($queryParameters)
            : '&' . http_build_query($queryParameters);

        $fullUrl = $overwriteUrl ?? ($this->configuration->getHost() . $path);
        $request = $this->requestFactory->createRequest('GET', $fullUrl);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $request = $request->withBody($this->streamFactory->createStream($payload));

        $response     = $this->httpClient->sendRequest($request);
        $responseCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        if ($responseCode >= 400) {
            $requestExceptionClass = '\Test\Exception\Request' . $responseCode . 'Exception';
            $responseHeaders       = $response->getHeaders();

            if (class_exists($requestExceptionClass)) {
                throw new $requestExceptionClass($responseBody, $responseHeaders, $fullUrl, 'GET', $payload, $headers);
            } else {
                throw new RequestException($responseCode, $responseBody, $responseHeaders, $fullUrl, 'GET', $payload, $headers);
            }
        } else {
            return match ($responseCode) {
                200     => $this->getGetOrderListResponse200($responseCode, $responseBody),
                default => $this->getGetOrderListResponse($responseCode, $responseBody),

            };
        }
    }

    /**
     * @return OrderCreate200ResponseList => 200
     * @return OrderCreate201Response => 201
     * @return CreateOrderResponse202 => 202
     * @return GeneralResponse => default
     * @throws Request400Exception when received 400 (Bad request, the request parameters are invalid)
     * @throws Request404Exception when received 404 (Path not found)
     */
    public function createOrder(OrderCreateOrder $request, ?string $overwriteUrl = null): OrderCreate200ResponseList|OrderCreate201Response|CreateOrderResponse202|GeneralResponse
    {
        $path    = '/order/create';
        $payload = $request->hasRequestModel() ? json_encode($request->getRequestModel()) : '';
        $headers = array_merge(
            $this->defaultHeaders,
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        );

        if (!empty($this->configuration->getApiKeyHeaderName())) {
            $headers[$this->configuration->getApiKeyHeaderName()] = $this->configuration->getApiKey();
        }

        foreach ($request->getHeaderParameterGetters() as $parameterName => $getterName) {
            $headers[$parameterName] = $request->$getterName();
        }

        foreach ($request->getPathParameterGetters() as $parameterName => $getterName) {
            $path = str_replace('{' . $parameterName . '}', (string)$request->$getterName(), $path);
        }

        $queryParameters = [];
        foreach ($request->getQueryParameterGetters() as $parameterName => $getterName) {
            $queryParameters[$parameterName] = $request->$getterName();
        }

        $path .= strpos($path, '?') === false
            ? '?' . http_build_query($queryParameters)
            : '&' . http_build_query($queryParameters);

        $fullUrl = $overwriteUrl ?? ($this->configuration->getHost() . $path);
        $request = $this->requestFactory->createRequest('POST', $fullUrl);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $request = $request->withBody($this->streamFactory->createStream($payload));

        $response     = $this->httpClient->sendRequest($request);
        $responseCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        if ($responseCode >= 400) {
            $requestExceptionClass = '\Test\Exception\Request' . $responseCode . 'Exception';
            $responseHeaders       = $response->getHeaders();

            if (class_exists($requestExceptionClass)) {
                throw new $requestExceptionClass($responseBody, $responseHeaders, $fullUrl, 'POST', $payload, $headers);
            } else {
                throw new RequestException($responseCode, $responseBody, $responseHeaders, $fullUrl, 'POST', $payload, $headers);
            }
        } else {
            return match ($responseCode) {
                200     => $this->getCreateOrderResponse200($responseCode, $responseBody),
                201     => $this->getCreateOrderResponse201($responseCode, $responseBody),
                202     => $this->getCreateOrderResponse202($responseCode, $responseBody),
                default => $this->getCreateOrderResponse($responseCode, $responseBody),

            };
        }
    }

    /**
     * @return GeneralResponse => 204
     * @return GeneralResponse => default
     */
    public function updateOrder(OrderUpdateOrder $request, ?string $overwriteUrl = null): GeneralResponse
    {
        $path    = '/order/update';
        $payload = $request->hasRequestModel() ? json_encode($request->getRequestModel()) : '';
        $headers = array_merge(
            $this->defaultHeaders,
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        );

        if (!empty($this->configuration->getApiKeyHeaderName())) {
            $headers[$this->configuration->getApiKeyHeaderName()] = $this->configuration->getApiKey();
        }

        foreach ($request->getHeaderParameterGetters() as $parameterName => $getterName) {
            $headers[$parameterName] = $request->$getterName();
        }

        foreach ($request->getPathParameterGetters() as $parameterName => $getterName) {
            $path = str_replace('{' . $parameterName . '}', (string)$request->$getterName(), $path);
        }

        $queryParameters = [];
        foreach ($request->getQueryParameterGetters() as $parameterName => $getterName) {
            $queryParameters[$parameterName] = $request->$getterName();
        }

        $path .= strpos($path, '?') === false
            ? '?' . http_build_query($queryParameters)
            : '&' . http_build_query($queryParameters);

        $fullUrl = $overwriteUrl ?? ($this->configuration->getHost() . $path);
        $request = $this->requestFactory->createRequest('POST', $fullUrl);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $request = $request->withBody($this->streamFactory->createStream($payload));

        $response     = $this->httpClient->sendRequest($request);
        $responseCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        if ($responseCode >= 400) {
            $requestExceptionClass = '\Test\Exception\Request' . $responseCode . 'Exception';
            $responseHeaders       = $response->getHeaders();

            if (class_exists($requestExceptionClass)) {
                throw new $requestExceptionClass($responseBody, $responseHeaders, $fullUrl, 'POST', $payload, $headers);
            } else {
                throw new RequestException($responseCode, $responseBody, $responseHeaders, $fullUrl, 'POST', $payload, $headers);
            }
        } else {
            return match ($responseCode) {
                204     => $this->getUpdateOrderResponse204($responseCode, $responseBody),
                default => $this->getUpdateOrderResponse($responseCode, $responseBody),

            };
        }
    }

    private function getGetOrderListResponse200(int $statusCode, string $responseBody): OrderCreate200ResponseList
    {
        $mapper = (new ArrayMapperFactory())->getMapper();
        $list   = (new OrderCreate200ResponseList())
            ->setStatusCode($statusCode)
            ->setBody($responseBody);

        foreach (json_decode($responseBody, true) as $item) {
            $list->add($mapper->map($item, $list->getItemClass()));
        }

        return $list;
    }

    private function getGetOrderListResponse(int $statusCode, string $responseBody): GeneralResponse
    {
        return (new GeneralResponse())
            ->setStatusCode($statusCode)
            ->setBody($responseBody);
    }

    private function getCreateOrderResponse200(int $statusCode, string $responseBody): OrderCreate200ResponseList
    {
        $mapper = (new ArrayMapperFactory())->getMapper();
        $list   = (new OrderCreate200ResponseList())
            ->setStatusCode($statusCode)
            ->setBody($responseBody);

        foreach (json_decode($responseBody, true) as $item) {
            $list->add($mapper->map($item, $list->getItemClass()));
        }

        return $list;
    }

    private function getCreateOrderResponse201(int $statusCode, string $responseBody): OrderCreate201Response
    {
        $response = (new ArrayMapperFactory())
        ->getMapper()
        ->map(
            empty($responseBody) ? [] : json_decode($responseBody, true),
            OrderCreate201Response::class
        );
        $response
            ->setStatusCode($statusCode)
            ->setBody($responseBody);

        return $response;
    }

    private function getCreateOrderResponse202(int $statusCode, string $responseBody): CreateOrderResponse202
    {
        $response = (new ArrayMapperFactory())
        ->getMapper()
        ->map(
            empty($responseBody) ? [] : json_decode($responseBody, true),
            CreateOrderResponse202::class
        );
        $response
            ->setStatusCode($statusCode)
            ->setBody($responseBody);

        return $response;
    }

    private function getCreateOrderResponse(int $statusCode, string $responseBody): GeneralResponse
    {
        return (new GeneralResponse())
            ->setStatusCode($statusCode)
            ->setBody($responseBody);
    }

    private function getUpdateOrderResponse204(int $statusCode, string $responseBody): GeneralResponse
    {
        $response = (new ArrayMapperFactory())
        ->getMapper()
        ->map(
            empty($responseBody) ? [] : json_decode($responseBody, true),
            GeneralResponse::class
        );
        $response
            ->setStatusCode($statusCode)
            ->setBody($responseBody);

        return $response;
    }

    private function getUpdateOrderResponse(int $statusCode, string $responseBody): GeneralResponse
    {
        return (new GeneralResponse())
            ->setStatusCode($statusCode)
            ->setBody($responseBody);
    }
}
