<?php

namespace Izzai\One\Services;

use Izzai\One\Types\Datasource;
use Izzai\One\Types\WithCount;

class DatasourceService extends BaseService
{
  /**
   * List all datasources
   *
   * @return WithCount
   */
  public function list(
    ?string $search = '',
    ?int $limit = 20,
    ?int $offset = 0
  ): WithCount {
    $response = $this->sendGet('/v1/datasource', [
      'search' => $search,
      'limit' => $limit,
      'offset' => $offset
    ]);

    return new WithCount(
      array_map(
        fn($item) => $this->castToClass($item, Datasource::class),
        $response['data'] ?? []
      ),
      $response['count'] ?? 0
    );
  }
}
