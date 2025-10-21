<?php

namespace Izzai\One\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Exception;
use Izzai\One\Cast\Caster;
use Izzai\One\Types\OneAuthObjectType;

abstract class BaseService
{
  protected string $instanceId;
  protected OneAuthObjectType|string $auth;
  protected string $baseUrl;
  private Client $httpClient;

  /**
   * @param string $instanceId
   * @param string|OneAuthObjectType $auth JWT token string or OneAuthObjectType
   * @param string|null $baseUrl
   */
  public function __construct(
    string $instanceId,
    OneAuthObjectType|string $auth,
    ?string $baseUrl = null
  ) {
    $this->instanceId = $instanceId;
    $this->auth = $auth;
    $this->baseUrl = $baseUrl ?? 'https://api.one.izz.ai/';

    $this->validateConfiguration();
    $this->httpClient = new Client(['base_uri' => $this->baseUrl]);
  }

  private function validateConfiguration(): void
  {
    if (empty($this->instanceId)) {
      throw new InvalidArgumentException('Instance ID is required');
    }

    if (is_string($this->auth)) {
      if (empty($this->auth)) {
        throw new InvalidArgumentException('JWT token cannot be empty');
      }
    } elseif (!is_null($this->auth)) {
      if (empty($this->auth->email) || empty($this->auth->apiKey)) {
        throw new InvalidArgumentException('Email and API key are required');
      }
    } else {
      throw new InvalidArgumentException(
        'Auth must be a JWT token string or array with email and apiKey'
      );
    }
  }

  protected function getHeaders(array $extra = []): array
  {
    $headers = array_merge($extra, [
      'x-environment-id' => $this->instanceId,
      'Accept' => 'application/json',
      'User-Agent' => 'izzai-one-php/1.0.0'
    ]);

    if (is_string($this->auth)) {
      $headers['izz-access-token'] = $this->auth;
    } else {
      $credentials = base64_encode($this->auth->email . ':' . $this->auth->apiKey);
      $headers['Authorization'] = 'Basic ' . $credentials;
    }

    return $headers;
  }

  protected function sendPost(string $path, $body = null): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];

    if ($body !== null) {
      if (is_array($body)) {
        $options[RequestOptions::JSON] = $body;
      } else {
        $options[RequestOptions::BODY] = $body;
      }
    }

    try {
      $response = $this->httpClient->post($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function sendGet(string $path, array $query = []): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];

    if (!empty($query)) {
      $options[RequestOptions::QUERY] = $query;
    }

    try {
      $response = $this->httpClient->get($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function sendPut(string $path, $body = null): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];

    if ($body !== null) {
      if (is_array($body)) {
        $options[RequestOptions::JSON] = $body;
      } else {
        $options[RequestOptions::BODY] = $body;
      }
    }

    try {
      $response = $this->httpClient->put($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function sendPatch(string $path, $body = null): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];

    if ($body !== null) {
      if (is_array($body)) {
        $options[RequestOptions::JSON] = $body;
      } else {
        $options[RequestOptions::BODY] = $body;
      }
    }

    try {
      $response = $this->httpClient->patch($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function sendDelete(string $path): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];

    try {
      $response = $this->httpClient->delete($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function sendMultipart(string $path, array $multipart): array
  {
    $options = [
      RequestOptions::HEADERS => $this->getHeaders(),
      RequestOptions::MULTIPART => $multipart
    ];

    try {
      $response = $this->httpClient->post($path, $options);
      return json_decode($response->getBody()->getContents(), true);
    } catch (Exception $e) {
      throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
    }
  }

  protected function castToClass(object|array $data, string $className)
  {
    return (new Caster())->castToClass($data, $className);
  }
}
