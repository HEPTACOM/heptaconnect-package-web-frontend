# Configuration

There are multiple configuration possible, that allows for your adjustment and reconfiguration.
When you want to deviate from the default values, you have to call `loadFromExtension` as seen below.
You apply it using extension configuration:

```php
<?php

declare(strict_types=1);

namespace Package;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtectionFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\BootstrapThemeFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\PageFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\SessionFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\CacheFeature;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\DebugFeature;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Package extends PackageContract
{
    public function buildContainer(ContainerBuilder $containerBuilder): void
    {
        parent::buildContainer($containerBuilder);

        $containerBuilder->loadFromExtension(AccessProtectionFeature::getName(), [
            // … your configuration  
        ]);
        $containerBuilder->loadFromExtension(BootstrapThemeFeature::getName(), [
            // … your configuration  
        ]);
        $containerBuilder->loadFromExtension(PageFeature::getName(), [
            // … your configuration  
        ]);
        $containerBuilder->loadFromExtension(SessionFeature::getName(), [
            // … your configuration  
        ]);
        $containerBuilder->loadFromExtension(CacheFeature::getName(), [
            // … your configuration  
        ]);
        $containerBuilder->loadFromExtension(DebugFeature::getName(), [
            // … your configuration  
        ]);
    }
}
```


## WebFrontendAccessProtection

### after_login_page_path

**Default:** `ui`\
**Allowed types:** string\
Configures the path to the page, that will be redirected to after a login.


### login_page_path

**Default:** `_access/lockscreen`\
**Allowed types:** string\
Configures the path to the login form page.


### login_path

**Default:** `_access/login`\
**Allowed types:** string\
Configures the path to the login form action.


### logout_path

**Default:** `_access/logout`\
**Allowed types:** string\
Configures the path to the logout action.


## WebFrontendBootstrapTheme

### enabled

**Default:** `true`\
**Allowed types:** boolean\
Enables the bootstrap theme.
Disable, when you want to ship a complete own theme.


## WebFrontendPage

### enabled

**Default:** `true`\
**Allowed types:** boolean\
Enables the pages rendering.


### default_page_enabled

**Default:** `null`\
**Allowed types:** boolean | null\
Enables the default page.
When null is given, the value of `enabled` configuration is used.


### default_page_path

**Default:** `ui`\
**Allowed types:** string\
Configures the path to the default page.


## WebFrontendSession

### enabled

**Default:** `true`\
**Allowed types:** boolean\
Enables cookie-driven session management.


### session_lifetime

**Default:** `P30D`\
**Allowed types:** string\
Defines the [time formatted as interval](https://en.wikipedia.org/wiki/ISO_8601#Durations) for how long a session should be stored.


### cookie_name

**Default:** `HC_SESSION_ID`\
**Allowed types:** string\
Defines the name of the cookie used for storing the session.


### cache_key_prefix

**Default:** `session.storage.`\
**Allowed types:** string\
Defines the prefix of the cache storage used for the sessions.


## WebFrontendTemplateCache

### enabled

**Default:** `true`\
**Allowed types:** boolean\
Enables the twig template cache.


## WebFrontendTemplateDebug

### enabled

**Default:** `false`\
**Allowed types:** boolean\
Enables the twig debug functionalities.


### html_error_renderer

**Default:** `null`\
**Allowed types:** boolean | null\
When null is given, the value of `enabled` configuration is used.
Enables a contextful error page useful for debugging, when an uncaught exception occurs.
Should be disabled on production environments.
