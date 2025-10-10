<?php

namespace Izzai\One\Types;

use Izzai\One\Cast\Caster;

/**
 * Extends the base Chat structure and adds a collection of Message documents.
 * * TypeScript equivalent: Chat & { messages: Message[] }
 */
class ChatWithMessages extends Chat
{
  /** @var Message[] */
  public $messages;
}

enum MessageMode: string
{
  case USER_MESSAGE = 'user-message';
  case AI_MESSAGE = 'ai-message';
  case ALL = 'all';
}

/**
 * Defines the allowed behaviors (from Chat behavior property).
 * * TypeScript equivalent: LlmBehaviorEnum
 * @var ('conversational'|'explaining'|'creative'|'formal'|'informal'|'technical'|'friendly'|'professional'|'humorous')
 */
// This type is used within the IChatBody class below.

/**
 * TypeScript equivalent: IChatBody
 *
 * The structure of a request body for creating or updating a chat session.
 */
class IChatBody
{
  static function from(array|object $data): IChatBody
  {
    return (new Caster())->castToClass($data, IChatBody::class);
  }

  /** @var string|null */
  public $id;

  /** @var string|null */
  public $messageId;

  /** @var string|null */
  public $messageRequest;

  /** @var string */
  public $msg;

  /** * @var (Message[])|null 
   * TypeScript equivalent: Pick<Message, 'content' | 'role'>[]
   */
  public $messageHistory;

  /** @var (Datasource|string)[]|null */
  public $datasources;

  /** @var string|Gpt|null */
  public $gpt;

  /** @var string|Agent|null */
  public $agent;

  /** @var ChatLlm|null */
  public $llm;

  /** @var float|null */
  public $temperature;

  /** @var int|null */
  public $topN;

  /** @var bool|null */
  public $useInternet;

  /** * @var ('conversational'|'explaining'|'creative'|'formal'|'informal'|'technical'|'friendly'|'professional'|'humorous')[]|null
   * TypeScript equivalent: LlmBehaviorEnum[]
   */
  public $behavior;

  /** @var string|null */
  public $systemMessage;

  /** @var bool|null */
  public $saveSystemMessage;

  /** @var bool|null */
  public $debug;

  /** @var int|null */
  public $tokenLimit;

  /** * @var ('JSON'|'MARKDOWN'|'TEXT'|'SPEECH')|null 
   * TypeScript equivalent: Chat['outputFormat']
   */
  public $outputFormat;

  /** @var mixed|null */
  public $llmOptions;

  /** @var string|null */
  public $label;

  /** @var string|null */
  public $outputParser;

  /** @var bool|null */
  public $excludeReferences;

  /** @var bool|null */
  public $resetSystemMessage;

  /** @var string|null */
  public $additionalAssistantMessage;

  /** @var string|null */
  public $additionalSystemMessage;

  /** * @var (string|Tag)[]|null 
   * TypeScript equivalent: Chat['tags']
   */
  public $tags;

  /** @var bool|null */
  public $regenerate;
}

/**
 * Represents a paginated list of data items.
 * * TypeScript equivalent: { data: T[]; count: number; }
 */
class WithCount
{
  public array $data;
  public int $count;

  public function __construct(array $data, int $count = 0)
  {
    $this->data = $data;
    $this->count = $count;
  }
}
