<?php
namespace EasySwoole\EasySwoole;




use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

use EasySwoole\Session\Session;
use EasySwoole\Session\SessionFileHandler;


class EasySwooleEvent implements Event
{

    /**
     * @throws \EasySwoole\Pool\Exception\Exception
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        \EasySwoole\Component\Di::getInstance()->set(SysConst::HTTP_CONTROLLER_MAX_DEPTH,3);  //路由层数
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $register->add($register::onWorkerStart,function (){
            //链接预热
            DbManager::getInstance()->getConnection()->getClientPool()->keepMin();
        });
        //可以自己实现一个标准的session handler
        $handler = new SessionFileHandler(EASYSWOOLE_TEMP_DIR);
        //表示cookie name   还有save path
        Session::getInstance($handler,'easy_session','session_dir');
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        $cookie = $request->getCookieParams('easy_session');
        if(empty($cookie)){
            $sid = Session::getInstance()->sessionId();
            $response->setCookie('easy_session',$sid);
        }else{
            Session::getInstance()->sessionId($cookie);
        }
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }



}