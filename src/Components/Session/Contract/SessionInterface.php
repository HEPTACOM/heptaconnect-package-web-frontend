<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract;

interface SessionInterface
{
    public function getId(): string;
}
