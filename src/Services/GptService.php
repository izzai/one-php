<?php

namespace Izzai\One\Services;

use Izzai\One\Types\Gpt;
use Izzai\One\Types\WithCount;

class GptService extends BaseService
{
  /**
   * List all GPTs
   *
   * @return WithCount
   */
  public function list(
    ?string $search = '',
    ?int $limit = 20,
    ?int $offset = 0
  ): WithCount {
    $response = $this->sendGet('/v1/gpt', [
      'search' => $search,
      'limit' => $limit,
      'offset' => $offset
    ]);

    return new WithCount(
      array_map(
        fn($item) => $this->castToClass($item, Gpt::class),
        $response['data'] ?? []
      ),
      $response['count'] ?? 0
    );
  }
}
