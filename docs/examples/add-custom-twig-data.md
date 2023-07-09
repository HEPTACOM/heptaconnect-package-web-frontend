# Add twig data

## Prerequisites

* [Use package](./use-package.md)


## Any package

### Using an HTTP middleware

###### src/Middleware/AddCurrentTimeMiddleware.php

`twig.`-prefix request attributes are forwarded as twig variables

```php
<?php

declare(strict_types=1);

namespace Package\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddCurrentTimeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle(
            $request->withAttribute('twig.time', time())
        );
    }
}
```


### Using a factory decorator

###### src/WebFrontend/WebPageTwigEnvFactory.php

```php
<?php

declare(strict_types=1);

namespace Package\WebFrontend;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class WebPageTwigEnvFactory implements WebPageTwigEnvironmentFactoryInterface
{
    public function __construct(
        private WebPageTwigEnvironmentFactoryInterface $decorated
    ) {}
    
    public function createTwigEnvironment(ServerRequestInterface $request,HttpHandleContextInterface $context) : Environment
    {
        $result = $this->decorated->createTwigEnvironment($request, $context);
        $result->addGlobal('time', time());
        
        return $result;
    }
}
```


###### src/Resources/config/services.xml

```xml
<?xml version="1.0" ?>
<container
    xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd"
>
    <services>
        <service
            decorates="Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface"
            id="Package\WebFrontend\WebPageTwigEnvFactory"
        >
            <argument type="service" id=".inner"/>
        </service>
    </services>
</container>
```
