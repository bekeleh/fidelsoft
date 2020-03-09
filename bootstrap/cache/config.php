<?php return array(
    'app' =>
        array(
            'name' => 'CARE ERP',
            'debug' => false,
            'env' => 'production',
            'url' => 'http://127.0.0.1:8000',
            'timezone' => 'UTC',
            'locale' => 'en',
            'fallback_locale' => 'en',
            'key' => 'base64:4i5yJu6j0RZGJwNlJuhJsEwynpuBcuBr+80qFpdwCRg=',
            'cipher' => 'AES-256-CBC',
            'log' => 'single',
            'providers' =>
                array(
                    0 => 'Illuminate\\Auth\\AuthServiceProvider',
                    1 => 'Collective\\Html\\HtmlServiceProvider',
                    2 => 'Illuminate\\Bus\\BusServiceProvider',
                    3 => 'Illuminate\\Cache\\CacheServiceProvider',
                    4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
                    5 => 'Illuminate\\Cookie\\CookieServiceProvider',
                    6 => 'Illuminate\\Database\\DatabaseServiceProvider',
                    7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
                    8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
                    9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
                    10 => 'Illuminate\\Hashing\\HashServiceProvider',
                    11 => 'Illuminate\\Mail\\MailServiceProvider',
                    12 => 'Illuminate\\Pagination\\PaginationServiceProvider',
                    13 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
                    14 => 'Illuminate\\Queue\\QueueServiceProvider',
                    15 => 'Illuminate\\Redis\\RedisServiceProvider',
                    16 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
                    17 => 'Illuminate\\Session\\SessionServiceProvider',
                    18 => 'Illuminate\\Translation\\TranslationServiceProvider',
                    19 => 'Illuminate\\Validation\\ValidationServiceProvider',
                    20 => 'Illuminate\\View\\ViewServiceProvider',
                    21 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
                    22 => 'Illuminate\\Notifications\\NotificationServiceProvider',
                    23 => 'Bootstrapper\\BootstrapperL5ServiceProvider',
                    24 => 'Former\\FormerServiceProvider',
                    25 => 'Barryvdh\\Debugbar\\ServiceProvider',
                    26 => 'Intervention\\Image\\ImageServiceProvider',
                    27 => 'Webpatser\\Countries\\CountriesServiceProvider',
                    28 => 'Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider',
                    29 => 'Laravel\\Socialite\\SocialiteServiceProvider',
                    30 => 'Jlapp\\Swaggervel\\SwaggervelServiceProvider',
                    31 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
                    32 => 'Websight\\GcsProvider\\CloudStorageServiceProvider',
                    33 => 'Jaybizzle\\LaravelCrawlerDetect\\LaravelCrawlerDetectServiceProvider',
                    34 => 'Codedge\\Updater\\UpdaterServiceProvider',
                    35 => 'Nwidart\\Modules\\LaravelModulesServiceProvider',
                    36 => 'Barryvdh\\Cors\\ServiceProvider',
                    37 => 'PragmaRX\\Google2FALaravel\\ServiceProvider',
                    38 => 'Chumper\\Datatable\\DatatableServiceProvider',
                    39 => 'Laravel\\Tinker\\TinkerServiceProvider',
                    40 => 'App\\Providers\\AuthServiceProvider',
                    41 => 'App\\Providers\\AppServiceProvider',
                    42 => 'App\\Providers\\ComposerServiceProvider',
                    43 => 'App\\Providers\\ConfigServiceProvider',
                    44 => 'App\\Providers\\EventServiceProvider',
                    45 => 'App\\Providers\\RouteServiceProvider',
                    46 => 'Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider',
                    47 => 'Davibennun\\LaravelPushNotification\\LaravelPushNotificationServiceProvider',
                ),
            'aliases' =>
                array(
                    'App' => 'Illuminate\\Support\\Facades\\App',
                    'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
                    'Auth' => 'Illuminate\\Support\\Facades\\Auth',
                    'Blade' => 'Illuminate\\Support\\Facades\\Blade',
                    'Cache' => 'Illuminate\\Support\\Facades\\Cache',
                    'ClassLoader' => 'Illuminate\\Support\\ClassLoader',
                    'Config' => 'Illuminate\\Support\\Facades\\Config',
                    'Controller' => 'Illuminate\\Routing\\Controller',
                    'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
                    'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
                    'DB' => 'Illuminate\\Support\\Facades\\DB',
                    'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
                    'Event' => 'Illuminate\\Support\\Facades\\Event',
                    'File' => 'Illuminate\\Support\\Facades\\File',
                    'Gate' => 'Illuminate\\Support\\Facades\\Gate',
                    'Hash' => 'Illuminate\\Support\\Facades\\Hash',
                    'Input' => 'Illuminate\\Support\\Facades\\Input',
                    'Lang' => 'Illuminate\\Support\\Facades\\Lang',
                    'Log' => 'Illuminate\\Support\\Facades\\Log',
                    'Mail' => 'Illuminate\\Support\\Facades\\Mail',
                    'Password' => 'Illuminate\\Support\\Facades\\Password',
                    'Queue' => 'Illuminate\\Support\\Facades\\Queue',
                    'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
                    'Redis' => 'Illuminate\\Support\\Facades\\Redis',
                    'Request' => 'Illuminate\\Support\\Facades\\Request',
                    'Response' => 'Illuminate\\Support\\Facades\\Response',
                    'Route' => 'Illuminate\\Support\\Facades\\Route',
                    'Schema' => 'Illuminate\\Support\\Facades\\Schema',
                    'Seeder' => 'Illuminate\\Database\\Seeder',
                    'Session' => 'Illuminate\\Support\\Facades\\Session',
                    'Storage' => 'Illuminate\\Support\\Facades\\Storage',
                    'Str' => 'Illuminate\\Support\\Str',
                    'URL' => 'Illuminate\\Support\\Facades\\URL',
                    'Validator' => 'Illuminate\\Support\\Facades\\Validator',
                    'View' => 'Illuminate\\Support\\Facades\\View',
                    'Form' => 'Collective\\Html\\FormFacade',
                    'HTML' => 'Collective\\Html\\HtmlFacade',
                    'SSH' => 'Illuminate\\Support\\Facades\\SSH',
                    'Alert' => 'Bootstrapper\\Facades\\Alert',
                    'Badge' => 'Bootstrapper\\Facades\\Badge',
                    'Breadcrumb' => 'Bootstrapper\\Facades\\Breadcrumb',
                    'Button' => 'Bootstrapper\\Facades\\Button',
                    'ButtonGroup' => 'Bootstrapper\\Facades\\ButtonGroup',
                    'ButtonToolbar' => 'Bootstrapper\\Facades\\ButtonToolbar',
                    'Carousel' => 'Bootstrapper\\Facades\\Carousel',
                    'DropdownButton' => 'Bootstrapper\\Facades\\DropdownButton',
                    'Helpers' => 'Bootstrapper\\Facades\\Helpers',
                    'Icon' => 'Bootstrapper\\Facades\\Icon',
                    'Label' => 'Bootstrapper\\Facades\\Label',
                    'MediaObject' => 'Bootstrapper\\Facades\\MediaObject',
                    'Navbar' => 'Bootstrapper\\Facades\\Navbar',
                    'Navigation' => 'Bootstrapper\\Facades\\Navigation',
                    'Paginator' => 'Bootstrapper\\Facades\\Paginator',
                    'Progress' => 'Bootstrapper\\Facades\\Progress',
                    'Tabbable' => 'Bootstrapper\\Facades\\Tabbable',
                    'Table' => 'Bootstrapper\\Facades\\Table',
                    'Thumbnail' => 'Bootstrapper\\Facades\\Thumbnail',
                    'Typeahead' => 'Bootstrapper\\Facades\\Typeahead',
                    'Typography' => 'Bootstrapper\\Facades\\Typography',
                    'Former' => 'Former\\Facades\\Former',
                    'Omnipay' => 'Omnipay\\Omnipay',
                    'CreditCard' => 'Omnipay\\Common\\CreditCard',
                    'Image' => 'Intervention\\Image\\Facades\\Image',
                    'Countries' => 'Webpatser\\Countries\\CountriesFacade',
                    'Carbon' => 'Carbon\\Carbon',
                    'Rocketeer' => 'Rocketeer\\Facades\\Rocketeer',
                    'Socialite' => 'Laravel\\Socialite\\Facades\\Socialite',
                    'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
                    'PushNotification' => 'Davibennun\\LaravelPushNotification\\Facades\\PushNotification',
                    'Crawler' => 'Jaybizzle\\LaravelCrawlerDetect\\Facades\\LaravelCrawlerDetect',
                    'Datatable' => 'Chumper\\Datatable\\Facades\\DatatableFacade',
                    'Updater' => 'Codedge\\Updater\\UpdaterFacade',
                    'Module' => 'Nwidart\\Modules\\Facades\\Module',
                    'Utils' => 'App\\Libraries\\Utils',
                    'DateUtils' => 'App\\Libraries\\DateUtils',
                    'HTMLUtils' => 'App\\Libraries\\HTMLUtils',
                    'CurlUtils' => 'App\\Libraries\\CurlUtils',
                    'Domain' => 'App\\Constants\\Domain',
                    'Google2FA' => 'PragmaRX\\Google2FALaravel\\Facade',
                ),
        ),
    'auth' =>
        array(
            'defaults' =>
                array(
                    'guard' => 'user',
                    'passwords' => 'users',
                ),
            'guards' =>
                array(
                    'user' =>
                        array(
                            'driver' => 'session',
                            'provider' => 'users',
                        ),
                    'client' =>
                        array(
                            'driver' => 'session',
                            'provider' => 'clients',
                        ),
                    'api' =>
                        array(
                            'driver' => 'token',
                            'provider' => 'users',
                        ),
                ),
            'providers' =>
                array(
                    'users' =>
                        array(
                            'driver' => 'eloquent',
                            'model' => 'App\\Models\\User',
                        ),
                    'clients' =>
                        array(
                            'driver' => 'eloquent',
                            'model' => 'App\\Models\\Contact',
                        ),
                ),
            'passwords' =>
                array(
                    'users' =>
                        array(
                            'provider' => 'users',
                            'table' => 'password_resets',
                            'expire' => 60,
                        ),
                    'clients' =>
                        array(
                            'provider' => 'clients',
                            'table' => 'password_resets',
                            'expire' => 60,
                        ),
                ),
        ),
    'bootstrapper' =>
        array(
            'bootstrapVersion' => '3.3.0',
            'jqueryVersion' => '2.1.0',
            'icon_prefix' => 'glyphicon',
        ),
    'cache' =>
        array(
            'default' => 'file',
            'stores' =>
                array(
                    'apc' =>
                        array(
                            'driver' => 'apc',
                        ),
                    'array' =>
                        array(
                            'driver' => 'array',
                        ),
                    'database' =>
                        array(
                            'driver' => 'database',
                            'table' => 'cache',
                            'connection' => NULL,
                        ),
                    'file' =>
                        array(
                            'driver' => 'file',
                            'path' => 'C:\\wamp\\www\\invoiceninja\\storage/framework/cache',
                        ),
                    'memcached' =>
                        array(
                            'driver' => 'memcached',
                            'servers' =>
                                array(
                                    0 =>
                                        array(
                                            'host' => '127.0.0.1',
                                            'port' => 11211,
                                            'weight' => 100,
                                        ),
                                ),
                        ),
                    'redis' =>
                        array(
                            'driver' => 'redis',
                            'connection' => 'default',
                        ),
                ),
            'prefix' => 'laravel',
        ),
    'chumper' =>
        array(
            'datatable' =>
                array(
                    'table' =>
                        array(
                            'class' => 'table table-bordered',
                            'id' => '',
                            'options' =>
                                array(
                                    'sPaginationType' => 'full_numbers',
                                    'bProcessing' => false,
                                ),
                            'callbacks' =>
                                array(),
                            'noScript' => false,
                            'table_view' => 'chumper.datatable::template',
                            'script_view' => 'chumper.datatable::javascript',
                            'options_view' => NULL,
                        ),
                    'engine' =>
                        array(
                            'exactWordSearch' => false,
                        ),
                    'classmap' =>
                        array(
                            'CollectionEngine' => 'Chumper\\Datatable\\Engines\\CollectionEngine',
                            'QueryEngine' => 'Chumper\\Datatable\\Engines\\QueryEngine',
                            'Table' => 'Chumper\\Datatable\\Table',
                        ),
                ),
        ),
    'compile' =>
        array(
            'files' =>
                array(
                    0 => 'C:\\wamp\\www\\invoiceninja\\app\\Providers\\AppServiceProvider.php',
                    1 => 'C:\\wamp\\www\\invoiceninja\\app\\Providers\\BusServiceProvider.php',
                    2 => 'C:\\wamp\\www\\invoiceninja\\app\\Providers\\ConfigServiceProvider.php',
                    3 => 'C:\\wamp\\www\\invoiceninja\\app\\Providers\\EventServiceProvider.php',
                    4 => 'C:\\wamp\\www\\invoiceninja\\app\\Providers\\RouteServiceProvider.php',
                ),
            'providers' =>
                array(),
        ),
    'cors' =>
        array(
            'supportsCredentials' => false,
            'allowedOrigins' =>
                array(
                    0 => '*',
                ),
            'allowedHeaders' =>
                array(
                    0 => '*',
                ),
            'allowedMethods' =>
                array(
                    0 => '*',
                ),
            'exposedHeaders' =>
                array(),
            'maxAge' => 0,
        ),
    'countries' =>
        array(
            'table_name' => 'countries',
        ),
    'database' =>
        array(
            'fetch' => 8,
            'default' => 'mysql',
            'connections' =>
                array(
                    'mysql' =>
                        array(
                            'driver' => 'mysql',
                            'host' => 'localhost',
                            'database' => 'ninja',
                            'username' => 'root',
                            'password' => 'secret',
                            'port' => '3306',
                            'charset' => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix' => '',
                            'strict' => false,
                            'engine' => 'InnoDB',
                        ),
                    'db-ninja-0' =>
                        array(
                            'driver' => 'mysql',
                            'host' => 'localhost',
                            'database' => 'ninja',
                            'username' => 'root',
                            'password' => 'secret',
                            'port' => '3306',
                            'charset' => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix' => '',
                            'strict' => false,
                            'engine' => 'InnoDB',
                        ),
                    'db-ninja-1' =>
                        array(
                            'driver' => 'mysql',
                            'host' => 'localhost',
                            'database' => 'ninja',
                            'username' => 'root',
                            'password' => 'secret',
                            'port' => '3306',
                            'charset' => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix' => '',
                            'strict' => false,
                            'engine' => 'InnoDB',
                        ),
                    'db-ninja-2' =>
                        array(
                            'driver' => 'mysql',
                            'host' => 'localhost',
                            'database' => 'ninja',
                            'username' => 'root',
                            'password' => 'secret',
                            'port' => '3306',
                            'charset' => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix' => '',
                            'strict' => false,
                            'engine' => 'InnoDB',
                        ),
                ),
            'migrations' => 'migrations',
            'redis' =>
                array(
                    'cluster' => false,
                    'default' =>
                        array(
                            'host' => '127.0.0.1',
                            'port' => 6379,
                            'database' => 0,
                        ),
                ),
        ),
    'debugbar' =>
        array(
            'enabled' => NULL,
            'storage' =>
                array(
                    'enabled' => true,
                    'driver' => 'file',
                    'path' => 'C:\\wamp\\www\\invoiceninja\\storage/debugbar',
                    'connection' => NULL,
                ),
            'include_vendors' => true,
            'capture_ajax' => true,
            'add_ajax_timing' => false,
            'error_handler' => false,
            'clockwork' => false,
            'collectors' =>
                array(
                    'phpinfo' => true,
                    'messages' => true,
                    'time' => true,
                    'memory' => true,
                    'exceptions' => true,
                    'log' => true,
                    'db' => true,
                    'views' => true,
                    'route' => true,
                    'laravel' => false,
                    'events' => false,
                    'default_request' => false,
                    'symfony_request' => true,
                    'mail' => true,
                    'logs' => false,
                    'files' => false,
                    'config' => false,
                    'auth' => false,
                    'session' => true,
                ),
            'options' =>
                array(
                    'auth' =>
                        array(
                            'show_name' => false,
                        ),
                    'db' =>
                        array(
                            'with_params' => true,
                            'timeline' => false,
                            'backtrace' => false,
                            'explain' =>
                                array(
                                    'enabled' => false,
                                    'types' =>
                                        array(
                                            0 => 'SELECT',
                                        ),
                                ),
                            'hints' => true,
                        ),
                    'mail' =>
                        array(
                            'full_log' => false,
                        ),
                    'views' =>
                        array(
                            'data' => false,
                        ),
                    'route' =>
                        array(
                            'label' => true,
                        ),
                    'logs' =>
                        array(
                            'file' => NULL,
                        ),
                ),
            'inject' => true,
            'route_prefix' => '_debugbar',
            'route_domain' => NULL,
        ),
    'excel' =>
        array(
            'cache' =>
                array(
                    'enable' => true,
                    'driver' => 'memory',
                    'settings' =>
                        array(
                            'memoryCacheSize' => '32MB',
                            'cacheTime' => 600,
                        ),
                    'memcache' =>
                        array(
                            'host' => 'localhost',
                            'port' => 11211,
                        ),
                    'dir' => 'C:\\wamp\\www\\invoiceninja\\storage\\cache',
                ),
            'properties' =>
                array(
                    'creator' => 'Maatwebsite',
                    'lastModifiedBy' => 'Maatwebsite',
                    'title' => 'Spreadsheet',
                    'description' => 'Default spreadsheet export',
                    'subject' => 'Spreadsheet export',
                    'keywords' => 'maatwebsite, excel, export',
                    'category' => 'Excel',
                    'manager' => 'Maatwebsite',
                    'company' => 'Maatwebsite',
                ),
            'sheets' =>
                array(
                    'pageSetup' =>
                        array(
                            'orientation' => 'portrait',
                            'paperSize' => '9',
                            'scale' => '100',
                            'fitToPage' => false,
                            'fitToHeight' => true,
                            'fitToWidth' => true,
                            'columnsToRepeatAtLeft' =>
                                array(
                                    0 => '',
                                    1 => '',
                                ),
                            'rowsToRepeatAtTop' =>
                                array(
                                    0 => 0,
                                    1 => 0,
                                ),
                            'horizontalCentered' => false,
                            'verticalCentered' => false,
                            'printArea' => NULL,
                            'firstPageNumber' => NULL,
                        ),
                ),
            'creator' => 'Maatwebsite',
            'csv' =>
                array(
                    'delimiter' => ',',
                    'enclosure' => '"',
                    'line_ending' => '
',
                ),
            'export' =>
                array(
                    'autosize' => false,
                    'autosize-method' => 'approx',
                    'generate_heading_by_indices' => true,
                    'merged_cell_alignment' => 'left',
                    'calculate' => false,
                    'includeCharts' => false,
                    'sheets' =>
                        array(
                            'page_margin' => false,
                            'nullValue' => NULL,
                            'startCell' => 'A1',
                            'strictNullComparison' => false,
                        ),
                    'store' =>
                        array(
                            'path' => 'C:\\wamp\\www\\invoiceninja\\storage\\exports',
                            'returnInfo' => false,
                        ),
                    'pdf' =>
                        array(
                            'driver' => 'mPDF',
                            'drivers' =>
                                array(
                                    'DomPDF' =>
                                        array(
                                            'path' => 'C:\\wamp\\www\\invoiceninja\\vendor/dompdf/dompdf/',
                                        ),
                                    'tcPDF' =>
                                        array(
                                            'path' => 'C:\\wamp\\www\\invoiceninja\\vendor/tecnick.com/tcpdf/',
                                        ),
                                    'mPDF' =>
                                        array(
                                            'path' => 'C:\\wamp\\www\\invoiceninja\\vendor/mpdf/mpdf/',
                                        ),
                                ),
                        ),
                ),
            'filters' =>
                array(
                    'registered' =>
                        array(
                            'chunk' => 'Maatwebsite\\Excel\\Filters\\ChunkReadFilter',
                        ),
                    'enabled' =>
                        array(),
                ),
            'import' =>
                array(
                    'heading' => 'slugged',
                    'startRow' => 1,
                    'separator' => '_',
                    'includeCharts' => false,
                    'to_ascii' => true,
                    'encoding' =>
                        array(
                            'input' => 'UTF-8',
                            'output' => 'UTF-8',
                        ),
                    'calculate' => true,
                    'ignoreEmpty' => false,
                    'force_sheets_collection' => false,
                    'dates' =>
                        array(
                            'enabled' => true,
                            'format' => false,
                            'columns' =>
                                array(),
                        ),
                    'sheets' =>
                        array(
                            'test' =>
                                array(
                                    'firstname' => 'A2',
                                ),
                        ),
                ),
            'views' =>
                array(
                    'styles' =>
                        array(
                            'th' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 12,
                                        ),
                                ),
                            'strong' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 12,
                                        ),
                                ),
                            'b' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 12,
                                        ),
                                ),
                            'i' =>
                                array(
                                    'font' =>
                                        array(
                                            'italic' => true,
                                            'size' => 12,
                                        ),
                                ),
                            'h1' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 24,
                                        ),
                                ),
                            'h2' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 18,
                                        ),
                                ),
                            'h3' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 13.5,
                                        ),
                                ),
                            'h4' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 12,
                                        ),
                                ),
                            'h5' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 10,
                                        ),
                                ),
                            'h6' =>
                                array(
                                    'font' =>
                                        array(
                                            'bold' => true,
                                            'size' => 7.5,
                                        ),
                                ),
                            'a' =>
                                array(
                                    'font' =>
                                        array(
                                            'underline' => true,
                                            'color' =>
                                                array(
                                                    'argb' => 'FF0000FF',
                                                ),
                                        ),
                                ),
                            'hr' =>
                                array(
                                    'borders' =>
                                        array(
                                            'bottom' =>
                                                array(
                                                    'style' => 'thin',
                                                    'color' =>
                                                        array(
                                                            0 => 'FF000000',
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
        ),
    'filesystems' =>
        array(
            'default' => 'local',
            'cloud' => 's3',
            'disks' =>
                array(
                    'local' =>
                        array(
                            'driver' => 'local',
                            'root' => 'C:\\wamp\\www\\invoiceninja\\storage/app',
                        ),
                    'logos' =>
                        array(
                            'driver' => 'local',
                            'root' => 'C:\\wamp\\www\\invoiceninja\\public/logo',
                        ),
                    'documents' =>
                        array(
                            'driver' => 'local',
                            'root' => 'C:\\wamp\\www\\invoiceninja\\storage/documents',
                        ),
                    's3' =>
                        array(
                            'driver' => 's3',
                            'key' => '',
                            'secret' => '',
                            'region' => 'us-east-1',
                            'bucket' => '',
                        ),
                    'rackspace' =>
                        array(
                            'driver' => 'rackspace',
                            'username' => '',
                            'key' => '',
                            'container' => '',
                            'endpoint' => 'https://identity.api.rackspacecloud.com/v2.0/',
                            'region' => 'IAD',
                            'url_type' => 'publicURL',
                        ),
                    'gcs' =>
                        array(
                            'driver' => 'gcs',
                            'bucket' => 'cloud-storage-bucket',
                            'project_id' => NULL,
                            'credentials' => 'C:\\wamp\\www\\invoiceninja\\storage/gcs-credentials.json',
                        ),
                ),
        ),
    'former' =>
        array(
            'automatic_label' => true,
            'default_form_type' => 'horizontal',
            'fetch_errors' => true,
            'live_validation' => true,
            'error_messages' => true,
            'push_checkboxes' => true,
            'unchecked_value' => '0',
            'required_class' => 'required',
            'required_text' => '',
            'translate_from' => 'texts',
            'capitalize_translations' => false,
            'translatable' =>
                array(
                    0 => 'help',
                    1 => 'inlineHelp',
                    2 => 'blockHelp',
                    3 => 'placeholder',
                    4 => 'data_placeholder',
                    5 => 'label',
                ),
            'framework' => 'TwitterBootstrap3',
            'TwitterBootstrap4' =>
                array(
                    'viewports' =>
                        array(
                            'large' => 'lg',
                            'medium' => 'md',
                            'small' => 'sm',
                            'mini' => 'xs',
                        ),
                    'labelWidths' =>
                        array(
                            'large' => 2,
                            'small' => 4,
                        ),
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => 'fa',
                            'prefix' => 'fa',
                        ),
                ),
            'TwitterBootstrap3' =>
                array(
                    'viewports' =>
                        array(
                            'large' => 'lg',
                            'medium' => 'md',
                            'small' => 'sm',
                            'mini' => 'xs',
                        ),
                    'labelWidths' =>
                        array(
                            'large' => 4,
                            'small' => 4,
                        ),
                    'icon' =>
                        array(
                            'tag' => 'span',
                            'set' => 'glyphicon',
                            'prefix' => 'glyphicon',
                        ),
                ),
            'Nude' =>
                array(
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => NULL,
                            'prefix' => 'icon',
                        ),
                ),
            'TwitterBootstrap' =>
                array(
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => NULL,
                            'prefix' => 'icon',
                        ),
                ),
            'ZurbFoundation5' =>
                array(
                    'viewports' =>
                        array(
                            'large' => 'large',
                            'medium' => NULL,
                            'small' => 'small',
                            'mini' => NULL,
                        ),
                    'labelWidths' =>
                        array(
                            'small' => 3,
                        ),
                    'wrappedLabelClasses' =>
                        array(
                            0 => 'right',
                            1 => 'inline',
                        ),
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => NULL,
                            'prefix' => 'fi',
                        ),
                    'error_classes' =>
                        array(
                            'class' => 'error',
                        ),
                ),
            'ZurbFoundation4' =>
                array(
                    'viewports' =>
                        array(
                            'large' => 'large',
                            'medium' => NULL,
                            'small' => 'small',
                            'mini' => NULL,
                        ),
                    'labelWidths' =>
                        array(
                            'small' => 3,
                        ),
                    'wrappedLabelClasses' =>
                        array(
                            0 => 'right',
                            1 => 'inline',
                        ),
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => 'general',
                            'prefix' => 'foundicon',
                        ),
                    'error_classes' =>
                        array(
                            'class' => 'alert-box radius warning',
                        ),
                ),
            'ZurbFoundation' =>
                array(
                    'viewports' =>
                        array(
                            'large' => '',
                            'medium' => NULL,
                            'small' => 'mobile-',
                            'mini' => NULL,
                        ),
                    'labelWidths' =>
                        array(
                            'large' => 2,
                            'small' => 4,
                        ),
                    'wrappedLabelClasses' =>
                        array(
                            0 => 'right',
                            1 => 'inline',
                        ),
                    'icon' =>
                        array(
                            'tag' => 'i',
                            'set' => NULL,
                            'prefix' => 'fi',
                        ),
                    'error_classes' =>
                        array(
                            'class' => 'alert-box alert error',
                        ),
                ),
        ),
    'google2fa' =>
        array(
            'enabled' => true,
            'lifetime' => 0,
            'keep_alive' => true,
            'auth' => 'auth',
            'session_var' => 'google2fa',
            'otp_input' => 'one_time_password',
            'window' => 1,
            'forbid_old_passwords' => false,
            'otp_secret_column' => 'google2fa_secret',
            'view' => 'google2fa.index',
            'error_messages' =>
                array(
                    'wrong_otp' => 'The \'One Time Password\' typed was wrong.',
                ),
        ),
    'ide-helper' =>
        array(
            'filename' => '_ide_helper',
            'format' => 'php',
            'meta_filename' => '.phpstorm.meta.php',
            'include_fluent' => false,
            'include_factory_builders' => false,
            'write_model_magic_where' => true,
            'write_eloquent_model_mixins' => false,
            'include_helpers' => false,
            'helper_files' =>
                array(
                    0 => 'C:\\wamp\\www\\invoiceninja/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
                ),
            'model_locations' =>
                array(
                    0 => 'app',
                ),
            'extra' =>
                array(
                    'Eloquent' =>
                        array(
                            0 => 'Illuminate\\Database\\Eloquent\\Builder',
                            1 => 'Illuminate\\Database\\Query\\Builder',
                        ),
                    'Session' =>
                        array(
                            0 => 'Illuminate\\Session\\Store',
                        ),
                ),
            'magic' =>
                array(
                    'Log' =>
                        array(
                            'debug' => 'Monolog\\Logger::addDebug',
                            'info' => 'Monolog\\Logger::addInfo',
                            'notice' => 'Monolog\\Logger::addNotice',
                            'warning' => 'Monolog\\Logger::addWarning',
                            'error' => 'Monolog\\Logger::addError',
                            'critical' => 'Monolog\\Logger::addCritical',
                            'alert' => 'Monolog\\Logger::addAlert',
                            'emergency' => 'Monolog\\Logger::addEmergency',
                        ),
                ),
            'interfaces' =>
                array(
                    '\\Illuminate\\Contracts\\Auth\\Authenticatable' => 'App\\User',
                ),
            'custom_db_types' =>
                array(),
            'model_camel_case_properties' => false,
            'type_overrides' =>
                array(
                    'integer' => 'int',
                    'boolean' => 'bool',
                ),
            'include_class_docblocks' => false,
        ),
    'image' =>
        array(
            'driver' => 'gd',
        ),
    'mail' =>
        array(
            'driver' => 'smtp',
            'host' => '',
            'port' => '587',
            'from' =>
                array(
                    'address' => '',
                    'name' => '',
                ),
            'encryption' => 'tls',
            'username' => '',
            'password' => '',
            'sendmail' => '/usr/sbin/sendmail -bs',
        ),
    'modules' =>
        array(
            'namespace' => 'Modules',
            'stubs' =>
                array(
                    'enabled' => true,
                    'path' => 'C:\\wamp\\www\\invoiceninja/app/Console/Commands/stubs',
                    'files' =>
                        array(
                            'start' => 'start.php',
                            'routes' => 'Http/routes.php',
                            'json' => 'module.json',
                            'views/master' => 'Resources/views/layouts/master.blade.php',
                            'scaffold/config' => 'Config/config.php',
                            'composer' => 'composer.json',
                        ),
                    'replacements' =>
                        array(
                            'start' =>
                                array(
                                    0 => 'LOWER_NAME',
                                ),
                            'routes' =>
                                array(
                                    0 => 'LOWER_NAME',
                                    1 => 'STUDLY_NAME',
                                    2 => 'MODULE_NAMESPACE',
                                ),
                            'json' =>
                                array(
                                    0 => 'LOWER_NAME',
                                    1 => 'STUDLY_NAME',
                                    2 => 'MODULE_NAMESPACE',
                                ),
                            'views/master' =>
                                array(
                                    0 => 'STUDLY_NAME',
                                ),
                            'scaffold/config' =>
                                array(
                                    0 => 'STUDLY_NAME',
                                ),
                            'composer' =>
                                array(
                                    0 => 'LOWER_NAME',
                                    1 => 'STUDLY_NAME',
                                    2 => 'VENDOR',
                                    3 => 'AUTHOR_NAME',
                                    4 => 'AUTHOR_EMAIL',
                                    5 => 'MODULE_NAMESPACE',
                                ),
                        ),
                ),
            'paths' =>
                array(
                    'modules' => 'C:\\wamp\\www\\invoiceninja\\Modules',
                    'assets' => 'C:\\wamp\\www\\invoiceninja\\public\\modules',
                    'migration' => 'C:\\wamp\\www\\invoiceninja\\database/migrations',
                    'generator' =>
                        array(
                            'assets' => 'Assets',
                            'config' => 'Config',
                            'command' => 'Console',
                            'event' => 'Events',
                            'listener' => 'Events/Handlers',
                            'migration' => 'Database/Migrations',
                            'model' => 'Models',
                            'repository' => 'Repositories',
                            'seeder' => 'Database/Seeders',
                            'controller' => 'Http/Controllers',
                            'filter' => 'Http/Middleware',
                            'request' => 'Http/Requests',
                            'provider' => 'Providers',
                            'lang' => 'Resources/lang/en',
                            'views' => 'Resources/views',
                            'test' => 'Tests',
                            'jobs' => 'Jobs',
                            'emails' => 'Emails',
                            'notifications' => 'Notifications',
                            'datatable' => 'Datatables',
                            'policy' => 'Policies',
                            'presenter' => 'Presenters',
                            'api-controller' => 'Http/ApiControllers',
                            'transformer' => 'Transformers',
                        ),
                ),
            'scan' =>
                array(
                    'enabled' => false,
                    'paths' =>
                        array(
                            0 => 'C:\\wamp\\www\\invoiceninja\\vendor/*/*',
                        ),
                ),
            'composer' =>
                array(
                    'vendor' => 'invoiceninja',
                    'author' =>
                        array(
                            'name' => 'Hillel Coren',
                            'email' => 'contact@invoiceninja.com',
                        ),
                ),
            'cache' =>
                array(
                    'enabled' => false,
                    'key' => 'laravel-modules',
                    'lifetime' => 60,
                ),
            'register' =>
                array(
                    'translations' => true,
                ),
            'relations' =>
                array(),
        ),
    'ninja' =>
        array(
            'video_urls' =>
                array(
                    'all' => 'https://www.youtube.com/channel/UCXAHcBvhW05PDtWYIq7WDFA/videos',
                    'custom_design' => 'https://www.youtube.com/watch?v=pXQ6jgiHodc',
                    'getting_started' => 'https://www.youtube.com/watch?v=i7fqfi5HWeo',
                ),
            'lock_sent_invoices' => NULL,
            'time_tracker_web_url' => 'https://www.invoiceninja.com/time-tracker',
            'knowledge_base_url' => 'https://www.invoiceninja.com/knowledge-base/',
            'coupon_50_off' => false,
            'coupon_75_off' => false,
            'coupon_free_year' => false,
            'exchange_rates_enabled' => false,
            'exchange_rates_url' => 'https://api.fixer.io/latest',
            'exchange_rates_base' => 'EUR',
            'terms_of_service_url' =>
                array(
                    'hosted' => 'https://www.invoiceninja.com/terms/',
                    'selfhost' => 'https://www.invoiceninja.com/self-hosting-terms-service/',
                ),
            'privacy_policy_url' =>
                array(
                    'hosted' => 'https://www.invoiceninja.com/privacy-policy/',
                    'selfhost' => 'https://www.invoiceninja.com/self-hosting-privacy-data-control/',
                ),
            'google_maps_enabled' => true,
            'google_maps_api_key' => '',
            'voice_commands' =>
                array(
                    'app_id' => 'ea1cda29-5994-47c4-8c25-2b58ae7ae7a8',
                    'subscription_key' => NULL,
                ),
        ),
    'packages' =>
        array(
            'ignited' =>
                array(
                    'laravel-omnipay' =>
                        array(
                            'config' =>
                                array(
                                    'default' => 'paypal',
                                    'gateways' =>
                                        array(
                                            'paypal' =>
                                                array(
                                                    'driver' => 'Paypal_Express',
                                                    'options' =>
                                                        array(
                                                            'solutionType' => '',
                                                            'landingPage' => '',
                                                            'headerImageUrl' => '',
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
            'zizaco' =>
                array(
                    'confide' =>
                        array(
                            'config' =>
                                array(
                                    'throttle_limit' => 9,
                                    'throttle_time_period' => 2,
                                    'login_cache_field' => 'email',
                                    'login_form' => 'users.login',
                                    'signup_form' => 'confide::signup',
                                    'forgot_password_form' => 'users.forgot_password',
                                    'reset_password_form' => 'users.reset_password',
                                    'email_reset_password' => 'emails.passwordreset_html',
                                    'email_account_confirmation' => 'emails.confirm_html',
                                    'signup_cache' => 0,
                                    'signup_email' => false,
                                    'signup_confirm' => false,
                                ),
                        ),
                ),
        ),
    'pdf' =>
        array(
            'phantomjs' =>
                array(
                    'secret' => 'n3jg0mm31heio0gfaiw6wyy6ejujvb0w',
                    'bin_path' => NULL,
                    'cloud_key' => 'a-demo-key-with-low-quota-per-ip-address',
                ),
        ),
    'push-notification' =>
        array(
            'devNinjaIOS' =>
                array(
                    'environment' => 'development',
                    'certificate' => 'C:\\wamp\\www\\invoiceninja\\storage/ninjaIOS.pem',
                    'passPhrase' => '',
                    'service' => 'apns',
                ),
            'ninjaIOS' =>
                array(
                    'environment' => 'production',
                    'certificate' => 'C:\\wamp\\www\\invoiceninja\\storage/productionNinjaIOS.pem',
                    'passPhrase' => '',
                    'service' => 'apns',
                ),
            'ninjaAndroid' =>
                array(
                    'environment' => 'production',
                    'apiKey' => NULL,
                    'service' => 'gcm',
                ),
        ),
    'queue' =>
        array(
            'default' => 'sync',
            'connections' =>
                array(
                    'sync' =>
                        array(
                            'driver' => 'sync',
                        ),
                    'database' =>
                        array(
                            'connection' => 'mysql',
                            'driver' => 'database',
                            'table' => 'jobs',
                            'queue' => 'default',
                            'expire' => 60,
                        ),
                    'beanstalkd' =>
                        array(
                            'driver' => 'beanstalkd',
                            'host' => 'localhost',
                            'queue' => 'default',
                            'ttr' => 60,
                        ),
                    'sqs' =>
                        array(
                            'driver' => 'sqs',
                            'key' => 'your-public-key',
                            'secret' => 'your-secret-key',
                            'queue' => 'your-queue-url',
                            'region' => 'us-east-1',
                        ),
                    'iron' =>
                        array(
                            'driver' => 'iron',
                            'host' => 'mq-aws-us-east-1.iron.io',
                            'token' => NULL,
                            'project' => NULL,
                            'queue' => NULL,
                            'encrypt' => true,
                        ),
                    'redis' =>
                        array(
                            'driver' => 'redis',
                            'queue' => 'default',
                            'expire' => 60,
                        ),
                ),
            'failed' =>
                array(
                    'database' => 'mysql',
                    'table' => 'failed_jobs',
                ),
        ),
    'self-update' =>
        array(
            'default' => 'github',
            'version_installed' => '2.6.9',
            'repository_types' =>
                array(
                    'github' =>
                        array(
                            'type' => 'github',
                            'repository_vendor' => 'invoiceninja',
                            'repository_name' => 'invoiceninja',
                            'repository_url' => '',
                            'download_path' => '/tmp',
                        ),
                ),
            'exclude_folders' =>
                array(
                    0 => 'node_modules',
                    1 => 'bootstrap/cache',
                    2 => 'bower',
                    3 => 'storage/app',
                    4 => 'storage/framework',
                    5 => 'storage/logs',
                    6 => 'storage/self-update',
                    7 => 'vendor',
                ),
            'log_events' => false,
            'mail_to' =>
                array(
                    'address' => '',
                    'name' => '',
                ),
            'artisan_commands' =>
                array(
                    'pre_update' =>
                        array(),
                    'post_update' =>
                        array(),
                ),
        ),
    'services' =>
        array(
            'postmark' => '',
            'mailgun' =>
                array(
                    'domain' => '',
                    'secret' => '',
                ),
            'mandrill' =>
                array(
                    'secret' => '',
                ),
            'ses' =>
                array(
                    'key' => '',
                    'secret' => '',
                    'region' => 'us-east-1',
                ),
            'stripe' =>
                array(
                    'model' => 'User',
                    'secret' => '',
                ),
            'github' =>
                array(
                    'client_id' => NULL,
                    'client_secret' => NULL,
                    'redirect' => NULL,
                ),
            'google' =>
                array(
                    'client_id' => NULL,
                    'client_secret' => NULL,
                    'redirect' => NULL,
                ),
            'facebook' =>
                array(
                    'client_id' => NULL,
                    'client_secret' => NULL,
                    'redirect' => NULL,
                ),
            'linkedin' =>
                array(
                    'client_id' => NULL,
                    'client_secret' => NULL,
                    'redirect' => NULL,
                ),
        ),
    'session' =>
        array(
            'driver' => 'file',
            'lifetime' => 480,
            'expire_on_close' => true,
            'encrypt' => false,
            'files' => 'C:\\wamp\\www\\invoiceninja\\storage/framework/sessions',
            'connection' => NULL,
            'table' => 'sessions',
            'lottery' =>
                array(
                    0 => 2,
                    1 => 100,
                ),
            'cookie' => 'ninja_session',
            'path' => '/',
            'domain' => NULL,
            'secure' => false,
        ),
    'swaggervel' =>
        array(
            'doc-dir' => 'C:\\wamp\\www\\invoiceninja\\storage/docs',
            'doc-route' => 'docs',
            'api-docs-route' => 'api-docs',
            'app-dir' => 'app',
            'excludes' =>
                array(
                    0 => 'C:\\wamp\\www\\invoiceninja\\storage',
                    1 => 'C:\\wamp\\www\\invoiceninja/tests',
                    2 => 'C:\\wamp\\www\\invoiceninja/resources/views',
                    3 => 'C:\\wamp\\www\\invoiceninja/config',
                    4 => 'C:\\wamp\\www\\invoiceninja/vendor',
                    5 => 'C:\\wamp\\www\\invoiceninja/app/Console/Commands/stubs',
                ),
            'generateAlways' => false,
            'api-key' => 'auth_token',
            'default-api-version' => 'v1',
            'default-swagger-version' => '2.0',
            'default-base-path' => '',
            'behind-reverse-proxy' => false,
        ),
    'view' =>
        array(
            'paths' =>
                array(
                    0 => 'C:\\wamp\\www\\invoiceninja\\resources\\views',
                ),
            'compiled' => 'C:\\wamp\\www\\invoiceninja\\storage\\framework\\views',
        ),
    'tinker' =>
        array(
            'commands' =>
                array(),
            'dont_alias' =>
                array(
                    0 => 'App\\Nova',
                ),
        ),
);
