# Build custom page

## Prerequisites

* [Provide custom templates](./provide-custom-templates.md)


## Any package

###### src/Search/SearchPage.php

Provide a structure for the content of the page

```php
<?php

declare(strict_types=1);

namespace Package\Search;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage;

class SearchPage extends AbstractPage
{
    public function __construct(
        public string $term,
        public array $results
    ) {
    }

    public function getTemplate(): string
    {
        return '@Package/page/search.html.twig';
    }
}
```


###### src/Search/SearchHandler.php

Perform the transformation of the request data to an input for an API call, back to a page object to render a response page. 

```php
<?php

declare(strict_types=1);

namespace Package\Search;

use Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandleContextInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SearchHandler extends UiHandlerContract
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    protected function supports(): string
    {
        return 'search';
    }

    protected function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        HttpHandleContextInterface $context
    ): ResponseInterface {
        $term = $request->getQueryParams()['term'] ?? '';

        return $this->render(new SearchPage(
            $term,
            $this->apiClient->queryItems($term),
        ));
    }
}
```


###### src/Resources/views/page/search.html.twig

Template to render the search form and search results

```html
{% extends '@WebFrontendPackage/ui/_base/layout.html.twig' %}
{# @var page \Package\Search\SearchPage #}

{% block head_title %}Search | {{ parent() }}{% endblock %}

{% block content %}
    <form action="{{ path('search') }}">
        <input name="term" value="{{ page.term }}" />
    </form>

    {% for item in results %}
        <div>
            {{ item.name }}
        </div>
    {% endfor %}
{% endblock %}
```


###### src/Resources/views/ui/component/sidebar/scrollable-content.html.twig

Adds a menu item to the sidebar

```html
{% extends '@WebFrontendPackage/ui/component/sidebar/scrollable-content.html.twig' %}

{% block sidebar_content_items %}
    {{ parent() }}

    {% include '@WebFrontendPackage/ui/component/sidebar/item.html.twig' with {
        sidebarItemTargetPath: 'search',
        sidebarItemTitle: 'Search',
        sidebarItemEmoji: '🔍'
    } %}
{% endblock %}
```
