<?php

namespace Izzai\One\Services;

use Izzai\One\Types\Agent;
use Izzai\One\Types\WithCount;

class AgentService extends BaseService
{
  /**
   * List all agents
   *
   * @return WithCount
   */
  public function list(
    ?string $search = '',
    ?int $limit = 20,
    ?int $offset = 0
  ): WithCount {
    $response = $this->sendGet('/v1/agent', [
      'search' => $search,
      'limit' => $limit,
      'offset' => $offset,
      'populate' => [['path' => 'steps']],
    ]);

    return new WithCount(
      array_map(
        fn($item) => $this->castToClass($item, Agent::class),
        $response['data'] ?? []
      ),
      $response['count'] ?? 0
    );
  }
}
