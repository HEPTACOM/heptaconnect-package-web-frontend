<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

interface SessionInterface
{
    public function getId(): string;

    public function all(): array;

    public function get(string $key): null|bool|int|float|string|array;

    public function set(string $key, null|bool|int|float|string|array $value): void;

    public function delete(string $key): void;

    public function clear(): void;

    public function has(string $key): bool;
}
