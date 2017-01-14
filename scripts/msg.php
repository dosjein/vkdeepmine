<?php

// get token from :  http://oauth.vk.com/authorize?client_id=3470411&scope=messages,photos,groups,status,wall,offline&redirect_uri=blank.html&display=page&v=5.5&response_type=token

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../vendor/autoload.php';

$dotenv = Dotenv::load(__DIR__.'/../');

$vk = getjump\Vk\Core::getInstance()->apiVersion('5.5')->setToken(getenv('VKTOKEN'));

$msgObject = (object) array('msg_in' => array() , 'msg_out' => array());


//$user=new getjump\Vk\Wrapper\User(getjump\Vk\Core::getInstance()->apiVersion('5.5'));
//$user->get(1, 'photo_max_orig, sex'); 

//MESSAGES OUT
foreach($vk->request('messages.get', ['count' => 200 , 'out' => 1])->batch(200) as $data){


    $userMap = [];
    $userCache = [];

    $user = new \getjump\Vk\Wrapper\User($vk);

    $fetchData = function($id) use($user, &$userMap, &$userCache , $msgObject)
    {
        if(!isset($userMap[$id]))
        {
            $userMap[$id] = sizeof($userCache);
            $userCache[] = $user->get($id)->response->get();
        }
        return $userCache[$userMap[$id]];
    };

    //REQUEST WILL ISSUE JUST HERE! SINCE __get overrided
    $data->each(function($key, $value) use($fetchData , $msgObject) {
        $user = $fetchData($value->user_id);
        $msgObject->out[] = array($user , $value);
        sleep(1);
        return;
    });
}

//MESSAGES IN
foreach($vk->request('messages.get', ['count' => 200 , 'out' => 0])->batch(200) as $data){


    $userMap = [];
    $userCache = [];

    $user = new \getjump\Vk\Wrapper\User($vk);

    $fetchData = function($id) use($user, &$userMap, &$userCache , $msgObject)
    {
        if(!isset($userMap[$id]))
        {
            $userMap[$id] = sizeof($userCache);
            $userCache[] = $user->get($id)->response->get();
        }
        return $userCache[$userMap[$id]];
    };

    //REQUEST WILL ISSUE JUST HERE! SINCE __get overrided
    $data->each(function($key, $value) use($fetchData , $msgObject) {
        $user = $fetchData($value->user_id);
        $msgObject->in[] = array($user , $value);
        sleep(1);
        return;
    });
}

$content = json_encode($msgObject);

file_put_contents('../storage/msg_'.md5(getenv('VKTOKEN')).'_'.time() , $content);