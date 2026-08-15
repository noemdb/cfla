<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;

class BinnacleEntryRequested
{
    public function __construct(
        public string $eventType,
        public array $context = [],
    ) {
        // El listener puede ejecutarse en un worker de cola sin la request
        // original: se captura el contexto HTTP en el momento del dispatch.
        $this->context['request'] ??= [
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'method' => request()?->method(),
            'url' => request()?->fullUrl(),
            'request_id' => request()?->header('X-Request-Id'),
            'session_id' => session()?->getId(),
        ];

        $this->context['created_by'] ??= auth()->id();
    }

    public function requestContext(): array
    {
        return $this->context['request'] ?? [];
    }

    public function subjectType(): ?string
    {
        return $this->resolveType('subject');
    }

    public function subjectId(): ?int
    {
        return $this->resolveId('subject');
    }

    public function subjectIdentifier(): ?string
    {
        return $this->resolveIdentifier('subject');
    }

    public function objectType(): ?string
    {
        return $this->resolveType('object');
    }

    public function objectId(): ?int
    {
        return $this->resolveId('object');
    }

    public function objectIdentifier(): ?string
    {
        return $this->resolveIdentifier('object');
    }

    private function resolveType(string $key): ?string
    {
        $value = $this->context[$key] ?? null;

        if ($value instanceof Model) {
            return $value->getMorphClass();
        }

        return $value['type'] ?? null;
    }

    private function resolveId(string $key): ?int
    {
        $value = $this->context[$key] ?? null;

        if ($value instanceof Model) {
            return $value->getKey();
        }

        return $value['id'] ?? null;
    }

    private function resolveIdentifier(string $key): ?string
    {
        if (isset($this->context[$key.'_identifier'])) {
            return (string) $this->context[$key.'_identifier'];
        }

        $value = $this->context[$key] ?? null;

        if ($value instanceof Model) {
            foreach (['username', 'fullname', 'name', 'email', 'title', 'card_number'] as $field) {
                $attr = $value->getAttribute($field);
                if ($attr !== null) {
                    return (string) $attr;
                }
            }

            return (string) $value->getKey();
        }

        return $value['identifier'] ?? null;
    }
}
