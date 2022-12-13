<?php

declare(strict_types=1);

namespace Test\Api;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Test\Configuration;
use Test\Exception\RequestException;
use Test\ArrayMapperFactory;
use Test\Model\ActionParameter\OrderCreateOrder;
use Test\Model\OrderCreateResponseList;

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

    public function createOrder(OrderCreateOrder $request): OrderCreateResponseList
    {
        $path    = '/order/create';
        $payload = $request->hasRequestModel() ? json_encode($request->getRequestModel()) : '';
        $headers = array_merge(
            $this->defaultHeaders,
            [
                'Content-Type'                              => 'application/json',
                'Accept'                                    => 'application/json',
                $this->configuration->getApiKeyHeaderName() => $this->configuration->getApiKey(),
            ],
        );

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

        $request = $this->requestFactory->createRequest(
            'POST',
            $this->configuration->getHost() . $path,
        );

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $request = $request->withBody($this->streamFactory->createStream($payload));

        $response     = $this->httpClient->sendRequest($request);
        $responseCode = $response->getStatusCode();

        if ($responseCode >= 400) {
            $requestExceptionClass = '\Test\Exception\Request' . $responseCode . 'Exception';
            $responseBody          = $response->getBody()->getContents();
            $responseHeaders       = $response->getHeaders();

            if (class_exists($requestExceptionClass)) {
                throw new $requestExceptionClass($responseBody, $responseHeaders);
            } else {
                throw new RequestException($responseCode, $responseBody, $responseHeaders);
            }
        } else {
            $mapper = (new ArrayMapperFactory())->getMapper();
            $list   = new OrderCreateResponseList();

            foreach (json_decode($response->getBody()->getContents(), true) as $item) {
                $list->add($mapper->map($item, $list->getItemClass()));
            }

            return $list;
        }
    }
}
