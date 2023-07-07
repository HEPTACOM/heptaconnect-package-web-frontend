<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage;

final class DefaultPage extends AbstractPage
{
    public function getTemplate(): string
    {
        return '@WebFrontendPackage/ui/page/index/index.html.twig';
    }
}
