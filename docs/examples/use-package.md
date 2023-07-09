# Use package

Use the package within a package, portal and portal extension declare it as additional package:

## Prerequisites

* Installed via composer `composer require heptacom/heptaconnect-package-web-frontend`


## Package

###### src/Package.php

```php
<?php

namespace Package;

use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract;

class Package extends PackageContract
{
    public function getAdditionalPackages() : iterable
    {
        yield new WebFrontendPackage();
    }
}
```


## Portal

###### src/Portal.php

```php
<?php

namespace Portal;

use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;

class Portal extends PortalContract
{
    public function getAdditionalPackages() : iterable
    {
        yield new WebFrontendPackage();
    }
}
```


## PortalExtension

###### src/PortalExtension.php

```php
<?php

namespace PortalExtension;

use Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;

class PortalExtension extends PortalExtensionContract
{
    public function getAdditionalPackages() : iterable
    {
        yield new WebFrontendPackage();
    }
}
```
