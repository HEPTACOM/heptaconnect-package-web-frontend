<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page;

abstract class AbstractPage
{
    abstract public function getTemplate(): string;
}
