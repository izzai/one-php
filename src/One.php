<?php

namespace Izzai\One;

use Izzai\One\Services\AgentService;
use Izzai\One\Services\ChatService;
use Izzai\One\Services\DatasourceService;
use Izzai\One\Services\GptService;
use Izzai\One\Services\BaseService;
use Izzai\One\Types\OneAuthObjectType;

class One extends BaseService
{
  public ?AgentService $agent = null;
  public ?ChatService $chat = null;
  public ?DatasourceService $datasource = null;
  public ?GptService $gpt = null;

  public function __construct(string $instanceId, OneAuthObjectType|string $auth, ?string $baseUrl = null)
  {
    parent::__construct($instanceId, $auth, $baseUrl);
    $this->_init();
  }

  private function _init(): One
  {
    if ($this->chat === null) {
      $this->chat = new ChatService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if ($this->datasource === null) {
      $this->datasource = new DatasourceService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if ($this->gpt === null) {
      $this->gpt = new GptService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if ($this->agent === null) {
      $this->agent = new AgentService($this->instanceId, $this->auth, $this->baseUrl);
    }

    return $this;
  }
}
