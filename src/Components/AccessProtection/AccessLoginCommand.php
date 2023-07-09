<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AccessProtectionServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReportingContextInterface;

final class AccessLoginCommand extends StatusReporterContract
{
    public function __construct(
        private AccessProtectionServiceInterface $accessProtectionMiddleware,
    ) {
    }

    public function supportsTopic(): string
    {
        return 'web-frontend:access-protection:login';
    }

    protected function run(StatusReportingContextInterface $context): array
    {
        return [
            $this->supportsTopic() => true,
            'login_url' => $this->accessProtectionMiddleware->generateLoginUrl(),
        ];
    }
}
