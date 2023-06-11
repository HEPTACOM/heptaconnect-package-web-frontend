<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification;

use Heptacom\HeptaConnect\Dataset\Base\Support\AbstractObjectCollection;

/**
 * @extends AbstractObjectCollection<Notification>
 */
final class NotificationBag extends AbstractObjectCollection
{
    protected function getT(): string
    {
        return Notification::class;
    }
}
