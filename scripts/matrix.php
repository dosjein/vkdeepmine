<?php

//create matrix from monitored  list - to get same friends
if (!file_exists ( '../storage/matrix' )){
    mkdir('../storage/matrix');
}

$folders = scandir('../storage/');

$matrix = array();

foreach($folders as $folder) {
  
	if (!in_array($folder , array('.' , '..' , 'matrix')) && is_dir('../storage/'.$folder) ){
//		echo $folder;

		$files = scandir('../storage/'.$folder.'/');
		foreach($files as $file){
			$handle = fopen('../storage/'.$folder.'/'.$file, "r");
			if ($handle) {
    				while (($line = fgets($handle)) !== false) {
        				// process the line read.
					$friendId = explode(',' , $line);
				
					if (!isset($matrix[trim($friendId[0])])){
						$matrix[trim($friendId[0])] = array();
					}
				
					//if (!isset($matrix[trim($fiendId[0])][$folder])){
					$matrix[trim($friendId[0])][$folder] = $friendId[1];
				
    				}
    				fclose($handle);
			} else {
    				// error opening the file.
			} 
		}
	}
}
//die('here');

foreach($matrix as $friendKey => $friendValue){
      if (count($friendValue) > 1){
	echo $friendValue[key($friendValue)].' is friend of '.count($friendValue).' monitored';
	echo "\r\n";
      }else{
	unset($matrix[$friendKey]);
      }
}

file_put_contents('../storage/matrix/'.time() , json_encode($matrix));
