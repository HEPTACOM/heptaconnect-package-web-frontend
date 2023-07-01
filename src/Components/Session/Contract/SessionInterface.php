<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionStorage;

interface SessionInterface
{
    public function getId(): string;

    public function getStorage(): SessionStorage;
}
