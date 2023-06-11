<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session;

final class Session implements SessionInterface
{
    public function __construct(
        private string $sessionId,
        private SessionStorage $sessionStorage,
    ) {
    }

    public function getId(): string
    {
        return $this->sessionId;
    }

    public function getStorage(): SessionStorage
    {
        return $this->sessionStorage;
    }
}
