<?php

namespace Izzai\One\Types;

/**
 * Represents the object structure of the 'OneAuth' type: { email: string, apiKey: string }.
 *
 * The full TypeScript type 'OneAuth' is equivalent to a variable type-hinted as:
 * **@var OneAuthObject|string**
 */
class OneAuthObjectType
{
  /** @var string */
  public $email;

  /** @var string */
  public $apiKey;

  public function __construct(string $email, string $apiKey)
  {
    $this->email = $email;
    $this->apiKey = $apiKey;
  }
}
