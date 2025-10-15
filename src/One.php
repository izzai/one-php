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
  public AgentService $agent;
  public ChatService $chat;
  public DatasourceService $datasource;
  public GptService $gpt;

  public function __construct(string $instanceId, OneAuthObjectType|string $auth, ?string $baseUrl = null)
  {
    parent::__construct($instanceId, $auth, $baseUrl);
    $this->_init();
  }

  private function _init(): One
  {
    if (empty($this->chat)) {
      $this->chat = new ChatService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if (empty($this->datasource)) {
      $this->datasource = new DatasourceService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if (empty($this->gpt)) {
      $this->gpt = new GptService($this->instanceId, $this->auth, $this->baseUrl);
    }
    if (empty($this->agent)) {
      $this->agent = new AgentService($this->instanceId, $this->auth, $this->baseUrl);
    }

    return $this;
  }
}
