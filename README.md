# One PHP SDK

This is the official PHP SDK for [One](https://one.izz.ai).

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/MIT-green.svg)](MIT)

## Installation

Install the package via Composer:

```bash
composer require izzai/one-php
```

## Quick Start

### Authentication

#### Using Email and API Key

```php
<?php

use Izzai\One\One;
use \Izzai\One\Types\OneAuthObjectType;

$one = new One(
  'YOUR_INSTANCE_ID',
  new OneAuthObjectType('YOUR_EMAIL_ID', 'YOUR_API_KEY')
);
```

#### Using JWT Token

```php
<?php

use Izzai\One\One;

$one = new One('YOUR_INSTANCE_ID', 'JWT_TOKEN');
```

> If you are using JWT token, make sure to check for the expiration time and refresh it as needed.

### Basic Usage

#### Chat

```php
use Izzai\One\Types\IChatBody;

// Simple chat [ defaults to AI message only ]
$response = $one->chat->chat('Hi there'); // Returns `Message`

// Get only the User message
$userResponse = $one->chat->chat('Hi there', IChatBody::from([]), 'user-message'); // Returns `Message`

// Get full conversation
$fullResponse = $one->chat->chat('Hi there', IChatBody::from([]), 'all'); // Returns `ChatWithMessages`
```

#### Chat with File

```php
$response = $one->chat->chatWithFile(
  'Analyze this document',
  '/path/to/file.pdf',
  'document.pdf'  // optional filename
);
```

#### Advanced Chat Options

```php
use Izzai\One\Types\IChatBody;

$response = $one->chat->chat(
  'Hello',
  IChatBody::from([
    'datasources' => ['datasource-id-1', 'datasource-id-2'],
    'tags' => ['tag1', 'tag2'],
  ])
);
```

### Datasources

```php
// List all datasources
$datasources = $one->datasource->list();
```

### GPTs

```php
// List all GPTs
$gpts = $one->gpt->list();
```

### Agents

```php
// List all agents
$agents = $one->agent->list();
```

### Error Handling

The SDK throws exceptions for various error conditions:

```php
try {
  $response = $one->chat->chat('Hello');
} catch (\InvalidArgumentException $e) {
  // Configuration or parameter errors
  echo "Configuration error: " . $e->getMessage();
} catch (\Exception $e) {
  // API or network errors
  echo "API error: " . $e->getMessage();
}
```

## Requirements

- PHP 8.1 or higher
- Guzzle HTTP client (^7.0)
- JSON extension

## License

MIT License. See the [LICENSE](LICENSE) file for details.
