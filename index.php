<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './pdos/WoodiePdo.php';
require './pdos/MapPdo.php';
require './pdos/AccomPdo.php';

require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
date_default_timezone_set('Asia/Seoul');
//ini_set('default_charset', 'utf8mb4');
ini_set('default$Longtitude_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);
//tmp
//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    // 유효성 검사는 요청 마다 함으로 굳이 필요없음
    //$r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/test', ['IndexController', 'test']);
    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);

    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);
    $r->addRoute('GET', '/motels', ['WoodieController', 'getMotels']);
    $r->addRoute('GET', '/motel-groups', ['IndexController', 'getMotelGroupList']);
    $r->addRoute('GET', '/areas', ['WoodieController', 'getAreas']);
    $r->addRoute('POST', '/users', ['IndexController', 'createUser']);


//    $r->addRoute('GET', '/motels', ['AccomController', 'searchMotelByArea']);
//    $r->addRoute('GET', '/hotels', ['AccomController', 'searchHotelByArea']);
    $r->addRoute('GET', '/myYanolja', ['IndexController', 'myYanolja']);
    $r->addRoute('GET', '/user-manage', ['IndexController', 'userManage']);
    $r->addRoute('POST', '/isValidPwd', ['IndexController', 'isValidPwd']);
    $r->addRoute('GET', '/users', ['IndexController', 'users']);
    $r->addRoute('PATCH', '/users/change-name', ['IndexController', 'changeName']);
    $r->addRoute('PATCH', '/users/change-pwd', ['IndexController', 'changePwd']);
    $r->addRoute('GET', '/around-motels', ['MapController', 'aroundMotels']);
    $r->addRoute('GET', '/around-hotels', ['MapController', 'aroundHotels']);
    $r->addRoute('GET', '/around/map', ['MapController', 'aroundMap']);
    $r->addRoute('GET', '/motels/{AccomIdx}', ['AccomController', 'getMotelDetail']);
    $r->addRoute('GET', '/hotels/{AccomIdx}', ['AccomController', 'getHotelDetail']);
    $r->addRoute('GET', '/reviews/{AccomIdx}', ['AccomController', 'getReviews']);
    $r->addRoute('GET', '/reviews/{AccomIdx}/photos', ['AccomController', 'getPhotoReviews']);

    $r->addRoute('GET', '/reserve-p', ['ReservationController', 'reserveP']);
    $r->addRoute('GET', '/reserve-a', ['ReservationController', 'reserveA']);
    $r->addRoute('POST', '/reserve-p/order', ['IndexController', 'orderP']);

//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'AccomController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/AccomController.php';
                break;
            case 'ReservationController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ReservationController.php';
                break;
            case 'WoodieController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/WoodieController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'MapController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MapController.php';
                break;
            /*case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
