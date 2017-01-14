<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../vendor/autoload.php';

$dotenv = Dotenv::load(__DIR__.'/../');

$vk = getjump\Vk\Core::getInstance()->apiVersion('5.5')->setToken(getenv('VKTOKEN'));

$friends = new getjump\Vk\Wrapper\Friends($vk);

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

if (!isset($_REQUEST['friend'])){
    die('ask for friend here'."\r\n");
}else{
    if (!file_exists ( '../storage/'.$_REQUEST['friend'] )){
        mkdir('../storage/'.$_REQUEST['friend']);
    }
}

foreach($friends->get($_REQUEST['friend'], array('first_name','last_name'))->batch(300) as $f) //BATCH MEAN $f WILL CONTAIN JUST 100 ELEMENTS, AND REQUEST WILL MADE FOR 100 ELEMENTS
{
    /**
     * @var $f \getjump\Vk\ApiResponse;
     */

   $myfile = fopen('../storage/'.$_REQUEST['friend'].'/'.time() , "w") or die("Unable to open file!");

    foreach($f->response->data->items as $object){
        //var_dump($object->online);
        //var_dump($object->first_name.' '.$object->last_name);
    if (isset($_REQUEST['deep'])){
        if (!file_exists ( '../storage/'.$object->id )){
            mkdir('../storage/'.$object->id);
        }
    }
    fwrite($myfile, $object->id.','.$object->first_name.' '.$object->last_name.','.intval($object->online)."\n");
    }
    //var_dump(count($f->response->data->items));
    fclose($myfile);
    //var_dump($f->response->data);

    //$f->response->each(function($i, $j) {
    //    var_dump($j);
    //if(!$j->online) return;
    //    var_dump($j->getName());
    //});
}
