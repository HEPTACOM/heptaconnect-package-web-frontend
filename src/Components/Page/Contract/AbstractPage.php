<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract;

abstract class AbstractPage
{
    /**
     * Get the path to the template file.
     * It MUST start with '@Package' to identify the expected origin package of the template.
     */
    abstract public function getTemplate(): string;
}
