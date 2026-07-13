<?php

namespace App\Core\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class MaskSensitiveDataProcessor implements ProcessorInterface
{
    private const MASK = '[FILTERED]';

    /** @var list<string> */
    private array $fields;

    public function __construct()
    {
        $this->fields = config('security.masked_fields', [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'secret',
            'api_key',
            'card_number',
        ]);
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            context: $this->maskArray($record->context),
            extra: $this->maskArray($record->extra),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function maskArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $this->fields, true)) {
                $data[$key] = self::MASK;
            } elseif (is_array($value)) {
                $data[$key] = $this->maskArray($value);
            }
        }

        return $data;
    }
}
