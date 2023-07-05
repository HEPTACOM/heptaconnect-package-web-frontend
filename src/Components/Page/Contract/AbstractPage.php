<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

abstract class AbstractPage
{
    abstract public function getTemplate(): string;
}
