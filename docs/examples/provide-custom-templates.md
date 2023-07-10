# Provide custom templates

Provide Twig templates for own pages or to override existing templates.
Every class can be a theme.
Making any HEPTAconnect package a theme is the low-code approach shown here. 
It is recommended to choose speaking short class names, as these form the theme name by default.


## Prerequisites

* [Use the package](./use-package.md)


## Any package

###### src/Resources/views/ui/_base/layout.html.twig

Example to overwrite a block to add an HTML title to the page.
To overwrite a file your file has to be placed at the same relative path to the theme file root.
Here: `ui/_base/layout.html.twig`

```twig
{% extends '@WebFrontendPackage/ui/_base/layout.html.twig' %}

{% block head_title %}FooBar | {{ parent() }}{% endblock %}
```


###### src/Resources/views/page/foobar.html.twig

```twig
{% extends '@WebFrontendPackage/ui/_base/layout.html.twig' %}
{# @var page \Package\Page\FooBarPage #}

{% block content %}
    <div class="container-fluid">
        {{ page.message }}
    </div>
{% endblock %}
```


## Package

###### src/FooBarPackage.php

```php
<?php

namespace Package;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract;

class FooBarPackage extends PackageContract implements ThemeInterface
{
    use ThemePackageTrait;
}
```


###### ide-twig.json (optional)

```json
{
    "namespaces": [
        {
            "path": "src/Resources/views",
            "namespace": "FooBarPackage"
        }
    ]
}
```


## Portal

###### src/FooBarPortal.php

```php
<?php

namespace Portal;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;

class FooBarPortal extends PortalContract implements ThemeInterface
{
    use ThemePackageTrait;
}
```


###### ide-twig.json (optional)

```json
{
    "namespaces": [
        {
            "path": "src/Resources/views",
            "namespace": "FooBarPortal"
        }
    ]
}
```


## PortalExtension

###### src/FooBarPortalExtension.php

```php
<?php

namespace PortalExtension;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface;
use Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;

class FooBarPortalExtension extends PortalExtensionContract implements ThemeInterface
{
    use ThemePackageTrait;
}
```


###### ide-twig.json (optional)

```json
{
    "namespaces": [
        {
            "path": "src/Resources/views",
            "namespace": "FooBarPortalExtension"
        }
    ]
}
```
