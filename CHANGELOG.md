# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [1.0.1] - 2023-11-22

### Fixed

- Fix login when no session was started yet

## [1.0.0] - 2023-07-10

### Added

- Require `php: >=8.0`
- Add composer dependency `ext-filter: *` to validate user input in PHP ini settings
- Add composer dependency `symfony/dependency-injection: ^5.0 || ^6.0` and `symfony/config: ^5.0 || ^6.0` as compiler passes, services.xml files and extensions are used
- Add composer dependency `heptacom/heptaconnect-portal-base: ^0.9.6` as `\Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage` is a package and different flow components are provided
- Add HEPTAconnect package class `\Heptacom\HeptaConnect\Package\WebFrontend\WebFrontendPackage`
- Add composer dependency `symfony/error-handler: ^5.0 || ^6.0` to provide human readable error pages
- Add composer dependencies `psr/http-factory: ^1.0`, `psr/http-message: ^1.0 || ^2.0`, `psr/http-server-handler: ^1.0` and `psr/http-server-middleware: ^1.0` as PSR-7 server requests are processed and responded
- Add HTTP middleware service `Heptacom\HeptaConnect\Package\WebFrontend\Components\ErrorHandler\HttpErrorHandlerMiddleware` with positive priority to catch exception as early as possible and render them as HTML
- Add composer dependency `heptacom/heptaconnect-dataset-base: ^0.9` to use collections and attachable structures
- Add collection service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\NotificationBag` holding `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Notification\Notification` for rendering use
- Add composer dependency `ext-mbstring: *` to work with multibyte strings
- Add composer dependencies `twig/twig: ^3.0` and `twig/string-extra: ^3.0` to make use of the Twig templating engine
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\Contract\TemplateFinderInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\TemplateFinder` to find the next matching template to render in the next step
- Add class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\TokenParserDecorator` to reuse existing token parser under a different name
- Add class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\ExtendsTokenParser` as theme-aware implementation for Twig tag `extends`
- Add class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\IncludeTokenParser` and `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\InheritedInclude` as theme-aware implementation for Twig tag `include`
- Add class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Hierarchy\NodeExtension` as Twig extension to provide theme-awareness to Twig
- Add interface `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface` to identify themes and collect them in collection service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeCollection`
- Add trait `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Utility\ThemePackageTrait` to implement `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\ThemeInterface` for any `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PackageContract` without any further code
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TemplateTagCompilerPass` to collect themes and bring them in order
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\TwigExtensionTagCompilerPass` to collect all Twig extensions
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RegisterSuggestedTwigExtensionsCompilerPass` to use the Twig Intl extension, when installed
- Add HTTP middleware service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\AssetMiddleware` to serve any given path to an asset optimized for web browser caching
- Add Twig test `instanceof` with `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension\InstanceOfExtension` to allow for variable checks to be a certain type
- Add Twig test `numeric` with `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension\IsNumericExtension`
- Add Twig filter `urldecode` with `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Extension\UrlDecodeExtension` as counterpart to `urlencode`
- Add composer dependency `bentools/iterable-functions: >=1.4 <2` to simplify working with iterables
- Add factory service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\TwigEnvironmentFactory` to build common Twig environment instances
- Add base class `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\AbstractFeature` for Symfony extensions, that are used to group code into features
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\ProvideContainerParameterForTwigEnvironmentCompilerPass` to pass feature configurations into the Twig template
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Debug\DebugTwigEnvironmentFactory` decorating `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface` to enable debugging features
- Add flow component `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Debug\DebugThemeStatusReporter` to debug theme functionalities
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\DebugFeature` to control template debugging
- Add Symfony extension `web_frontend_template_debug` configuration `enabled` to enable template debugging
- Add Symfony extension `web_frontend_template_debug` configuration `html_error_renderer` to fully render exceptions
- Add Twig cache implementation `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache\TwigCache`, that works different with temporary files
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache\CachePath` to handle Twig cache access
- Add flow component `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache\CacheClearCommand` to clear Twig cache
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\Cache\CachedTwigEnvironmentFactory` decorating `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Contract\TwigEnvironmentFactoryInterface` to enable caching features
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\Feature\CacheFeature` to control template caching
- Add Symfony extension `web_frontend_template_cache` configuration `enabled` to enable template caching
- Add theme `WebFrontendPackage` by class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\BootstrapTheme\BootstrapTheme`
- Add editor theme component in `@WebFrontendPackage/ui/_base/component/editor.html.twig`, `@WebFrontendPackage/ui/_base/js/editor.js` and `@WebFrontendPackage/ui/_base/css/editor.css` to have simplified code editor
- Add notification theme component in `@WebFrontendPackage/ui/_base/component/notifications.html.twig` and `@WebFrontendPackage/ui/_base/js/notifications.js` to display notifications with Bootstrap toasts
- Add sidebar theme component in `@WebFrontendPackage/ui/_base/component/sidebar.html.twig`, `@WebFrontendPackage/ui/_base/js/sidebar.js` and `@WebFrontendPackage/ui/_base/css/sidebar.css` divided into `@WebFrontendPackage/ui/component/sidebar/header.html.twig` and `@WebFrontendPackage/ui/component/sidebar/scrollable-content.html.twig` of `@WebFrontendPackage/ui/component/sidebar/item.html.twig` for sidebar menu items
- Add dark mode appearance in `@WebFrontendPackage/ui/_base/js/appearance.js`
- Add left-sidebar page layout in `@WebFrontendPackage/ui/_base/layout.html.twig`
- Add HEPTAconnect icon asset in `src/Components/BootstrapTheme/Resources/public/icon/heptaconnect-logo.png`
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\BootstrapThemeFeature` to control the Bootstrap 5 theme
- Add Symfony extension `web_frontend_bootstrap_theme` configuration `enabled` to enable the Bootstrap 5 theme
- Add composer dependency `psr/simple-cache": "^1.0` to use cache storages for sessions
- Add session storage class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Session` described by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionInterface`
- Add class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionManager` described by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\Contract\SessionManagerInterface` to store sessions and access them from requests
- Add HTTP middleware service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionMiddleware` with a lower priority than `Heptacom\HeptaConnect\Package\WebFrontend\Components\Template\AssetMiddleware` to ensure assets are not slowed by attaching and storing sessions for every request
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\SessionFeature` to control the session handling
- Add Symfony extension `web_frontend_session` configuration `enabled` to enable cookie-driven session management
- Add Symfony extension `web_frontend_session` configuration `session_lifetime` to defines for how long a session should be stored
- Add Symfony extension `web_frontend_session` configuration `cookie_name` to set the name of the cookie used for storing the session in a request and response
- Add Symfony extension `web_frontend_session` configuration `cache_key_prefix` to set the prefix of the cache storage used for the sessions
- Add base class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage` to identify page structure classes
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\RemovePagesCompilerPass` to remove any services, that might accidentally be picked up as service, but are a page structure object
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageTwigEnvironmentFactoryInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\WebPageTwigEnvironmentFactory` to generate Twig environments to render HTML pages
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\WebPageRendererInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\WebPageRenderer` to render any `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage` in a request
- Add base class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract` for HTTP handlers, that work with `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\AbstractPage`
- Add compiler pass `\Heptacom\HeptaConnect\Package\WebFrontend\DependencyInjection\ControllerPreparationCompilerPass` to automatically tag services of `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\Contract\UiHandlerContract`
- Add fallback page `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage\DefaultPage` with template `@WebFrontendPackage/ui/page/index/index.html.twig` handled by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\Page\DefaultPage\DefaultUiHandler` to always have page to show
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\PageFeature` to control page handling
- Add Symfony extension `web_frontend_page` configuration `enabled` to enable page rendering service
- Add Symfony extension `web_frontend_page` configuration `default_page_enabled` to enables the fallback page
- Add Symfony extension `web_frontend_page` configuration `default_page_path` to set the fallback page path
- Add HTTP handler `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\LoginHandler` to render and and perform a login
- Add HTTP handler `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\LogoutHandler` to perform a logout
- Add status reporter `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\AccessLoginCommand` to create root access login links
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AccessProtectionServiceInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\AccessProtectionService` to generate root login links
- Add service `Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\Contract\AuthorizationBackendInterface` implemented by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\AuthorizationBackend` to manage `htpasswd`-alike file as user directory
- Add HTTP middleware service `Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\AccessProtectionMiddleware` with a lower priority than `Heptacom\HeptaConnect\Package\WebFrontend\Components\Session\SessionMiddleware` to ensure sessions to access data are available to verify and assign login data
- Add lockscreen page `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\LockscreenPage` with template `@WebFrontendPackage/ui/page/lockscreen/index.html.twig` with custom style in `@WebFrontendPackage/ui/page/lockscreen/css/lockscreen.css` handled by `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtection\LockscreenUiHandler`
- Add feature class `\Heptacom\HeptaConnect\Package\WebFrontend\Components\AccessProtectionFeature` to control page access protection
- Add Symfony extension `web_frontend_access_protection` configuration `after_login_page_path` to set the path to the page, that will be redirected to after a login
- Add Symfony extension `web_frontend_access_protection` configuration `login_page_path` to set the path to the login form page
- Add Symfony extension `web_frontend_access_protection` configuration `login_path` to set the path to the login action
- Add Symfony extension `web_frontend_access_protection` configuration `logout_path` to set the path to the logout action
