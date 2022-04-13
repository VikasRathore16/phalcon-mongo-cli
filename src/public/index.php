<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require(APP_PATH . '/library/vendor/autoload.php');


// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/components',
        'App\Listeners' => APP_PATH . '/listeners',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'config',
    function () {
        $config = new Config([]);
        $fileName = APP_PATH . '/config/config.php';
        $factory  = new ConfigFactory();
        $config = $factory->newInstance('php', $fileName);
        return $config;
    }
);


$container->set(
    'db',
    function () {
        $config = $this->get('config');
        return new Mysql(
            [
                'host'     => $config->db->host,
                'username' => $config->db->username,
                'password' => $config->db->password,
                'dbname'   => $config->db->dbname,
            ]
        );
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => 'root', "password" => "password123"));

        return $mongo;
    },
    true
);


$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->setName('user')
            ->start();

        return $session;
    }
);

$container->set(
    'Mycurl',
    (new \App\Components\Mycurl())
);


$eventsManager = new EventsManager();


$eventsManager->attach(
    'token',
    new \App\Listeners\TokenListeners()
);

$container->set(
    'EventsManager',
    $eventsManager,
);

$application = new Application($container);

// $application->setEventsManager($eventsManager);
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
