<?php

namespace Izzai\One\Services;

use Exception;
use Izzai\One\Cast\CastChatWithMessage;
use Izzai\One\Types\ChatWithMessages;
use Izzai\One\Types\IChatBody;
use Izzai\One\Types\MessageLlm;
use Izzai\One\Types\MessageScratchPad;
use Izzai\One\Types\MessageFileContent;
use Izzai\One\Types\Message;
use Izzai\One\Types\MessageMode;

class ChatService extends BaseService
{
  /**
   * Send a chat message
   *
   * @param string $message
   * @param IChatBody $options
   * @param MessageMode $messageMode
   * @return (MessageMode is MessageMode::ALL ? ChatWithMessages : Message)
   */
  public function chat(
    string $message,
    IChatBody $options = new IChatBody(),
    MessageMode $messageMode = MessageMode::AI_MESSAGE
  ) {
    $body = array_merge([
      'msg' => $message,
      'datasources' => []
    ], json_decode(json_encode($this->defaultOptions($options)), true));

    $response = $this->sendPost('/v1/chat', $body);

    if (empty($response['messages'])) {
      throw new Exception('No messages found in response');
    }

    return $this->filterMessagesByMode(
      $this->castToClass($response, ChatWithMessages::class),
      $messageMode
    );
  }

  /**
   * Send a chat message with a file
   *
   * @param string $message
   * @param string $filePath
   * @param string|null $fileName
   * @param IChatBody $options
   * @param MessageMode $messageMode
   * @return ($messageMode is MessageMode::ALL ? ChatWithMessages : Message)
   */
  public function chatWithFile(
    string $message,
    string $filePath,
    ?string $fileName = null,
    IChatBody $options = new IChatBody(),
    MessageMode $messageMode = MessageMode::AI_MESSAGE
  ) {
    if (!file_exists($filePath)) {
      throw new Exception("File not found: {$filePath}");
    }

    $multipart = [
      [
        'name' => 'msg',
        'contents' => $message
      ],
      [
        'name' => 'file',
        'contents' => fopen($filePath, 'r'),
        'filename' => $fileName ?? basename($filePath)
      ]
    ];

    // Add options as form fields
    $optionsWithDefaults = array_merge(
      ['datasources' => []],
      json_decode(json_encode($this->defaultOptions($options)), true)
    );
    foreach ($optionsWithDefaults as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $index => $item) {
          $multipart[] = [
            'name' => "{$key}[{$index}]",
            'contents' => is_string($item) ? $item : json_encode($item)
          ];
        }
      } else {
        $multipart[] = [
          'name' => $key,
          'contents' => is_string($value) ? $value : json_encode($value)
        ];
      }
    }

    $response = $this->sendMultipart('/v1/chat', $multipart);

    if (empty($response['messages'])) {
      throw new Exception('No messages found in response');
    }

    return $this->filterMessagesByMode(
      $this->castToClass($response, ChatWithMessages::class),
      $messageMode
    );
  }

  private function defaultOptions(IChatBody $options): array
  {
    $options->messageHistory ??= [];
    $options->datasources ??= [];
    $options->behavior ??= [];
    $options->tags ??= [];

    $options->tags[] = 'one-sdk-php';


    return array_filter(json_decode(json_encode($options), true), fn($v) => $v !== null);
  }

  private function filterMessagesByMode(
    ChatWithMessages $response,
    MessageMode $messageMode
  ) {
    switch ($messageMode) {

      case MessageMode::AI_MESSAGE:
        foreach ($response->messages as $message) {
          if ($message->role === 'assistant') {
            return $message;
          }
        }
        throw new Exception('No assistant message found in response');

      case MessageMode::ALL:
        return $response;

      case MessageMode::USER_MESSAGE:
      default:
        foreach ($response->messages as $message) {
          if ($message->role === 'user') {
            return $message;
          }
        }
        throw new Exception('No user message found in response');
    }
  }
}
