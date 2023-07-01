<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface;

final class Session implements SessionInterface
{
    public function __construct(
        private string $sessionId,
        private array $values,
    ) {
    }

    public function getId(): string
    {
        return $this->sessionId;
    }

    public function all(): array
    {
        return $this->values;
    }

    public function get(string $key): null|bool|int|float|string|array
    {
        return $this->values[$key] ?? null;
    }

    public function set(string $key, null|bool|int|float|string|array $value): void
    {
        $this->values[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->values[$key]);
    }

    public function clear(): void
    {
        $this->values = [];
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }
}
