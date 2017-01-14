<?php

#https://vk.com/dev/photos.getUserPhotos
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../vendor/autoload.php';

$dotenv = Dotenv::load(__DIR__.'/../');

if (isset($argv) && count($argv) > 1){
    if (!isset($_REQUEST)){
        $_REQUEST = array();
    }

    $skip = false;

    foreach($argv as $argKey => $argVal){
        if ($skip){
            $skip = false;
            continue;
        }
        $_REQUEST[ $argv[$argKey - 1]] = $argv[$argKey];
        echo $argv[$argKey - 1].' is '.$argv[$argKey]."\r\n";
        $skip = true;
    }
}


$friend = isset($_REQUEST['friend']) ? $_REQUEST['friend'] : 16550882;

$vk = getjump\Vk\Core::getInstance()->apiVersion('5.5')->setToken(getenv('VKTOKEN'));

$object = (object) array('photos' => array());

/**Permission to perform this action is denied **/
$vk->request('photos.getUserPhotos', ['user_id' => $friend , 'extended' => 1 , 'sort' => 0])->each(function($i, $v) use ($vk , $friend, $object){

    $photo_id = $v->id;
    $object->photos[$photo_id] = (object) ['photo' => $v , 'tags' => array() , 'likes' => array()];

    if ($v->tags->count > 0 ){
        $vk->request('photos.getTags', ['photo_id' => $v->id , 'owner_id' => $v->owner_id])->each(function($itag, $vtag) use ($vk , $friend , $photo_id , $object){
            $object->photos[$photo_id]->tags[] = $vtag;
        });
    }

    sleep(1);

    if ($v->likes->count > 0 ){

        $vk->request('likes.getList', ['type' => 'photo' , 'item_id' => $v->id , 'owner_id' => $v->owner_id])->each(function($itag, $vtag) use ($vk , $friend , $photo_id , $object){
            $object->photos[$photo_id]->likes[] = $vtag;
        });
    }

    sleep(1);

    

});

if (!file_exists('../storage/img_'.$friend )){
    mkdir('../storage/img_'.$friend);
}

$content = json_encode($object);


file_put_contents('../storage/img_'.$friend.'/'.time() , $content);
