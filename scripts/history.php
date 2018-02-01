<?php

// get token from :  http://oauth.vk.com/authorize?client_id=3470411&scope=messages,photos,groups,status,wall,offline&redirect_uri=blank.html&display=page&v=5.5&response_type=token

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../vendor/autoload.php';

$dotenv = Dotenv::load(__DIR__.'/../');

function demand($newDemand)
{

    $vk = getjump\Vk\Core::getInstance()->apiVersion('5.5')->setToken(getenv('VKTOKEN'));

    $msgObject = (object) array('msg_in' => array() , 'msg_out' => array());

    //MESSAGES OUT
    foreach($vk->request('messages.getHistory', $newDemand)->batch(200) as $data){

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

            var_dump($value);
            file_put_contents(md5(getenv('VKTOKEN')).'_'.$value->user_id.'_ids.txt', '::'.json_encode($value)."\n", FILE_APPEND);
            $user = $fetchData($value->user_id);
            //file_put_contents('../storage/msg_'.md5(getenv('VKTOKEN')) , json_encode($msgObject));
            $msgObject->out[] = array($user , $value);
            sleep(1);
            return;
        });
    }
}


$handle = fopen("ids.txt", "r");
if ($handle) {
    if (($line = fgets($handle)) !== false) 
    {
        var_dump($line);
        $lineParts = explode('::', $line);

        $newDemand = ['count' => 200 , 'user_id' => intval($lineParts[0]) ];

        demand($newDemand);

    }
    fclose($handle);


    $contents = file("ids.txt", FILE_IGNORE_NEW_LINES);
    $first_line = array_shift($contents);

    var_dump($first_line);

    file_put_contents("ids.txt", implode("\r\n", $contents));

    // $newDoc = file_get_contents("ids.txt", true);
    // $newFileContents = substr( $line, strpos($newDoc, "\n")+1 );

    // var_dump($newFileContents);
    // //then you want to save it 
    // file_put_contents($newFileContents, "ids.txt");
} else {
    // error opening the file.
} 


