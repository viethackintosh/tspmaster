<?php
// outputs e.g.  somefile.txt was last modified: December 29 2002 22:16:23.
/**
 * $dir: đường dẫn thư mục gốc cần tìm file vừa được save
 * $fileIn: đường dẫn đến file hiện là file vừa được sửa (có tên file.ext)
 */
$mirror = ['source'=> 'dev'];
$api =['js'=>'https://www.toptal.com/developers/javascript-minifier/api/raw',
      'css'=>'https://www.toptal.com/developers/cssminifier/api/raw',
      'html'=>'https://www.toptal.com/developers/html-minifier/api/raw'
      ];
define('COMPLIER', 'scss|html|js');

function lastUpdateFile($dir, $checkedFile) {    
      $fileIn = $checkedFile;
      if (is_dir($dir)) {            
            $listIn = array_filter(scandir($dir) ,'removeOrginSub');
            if (empty($listIn)) return $fileIn;
            while (!empty($listIn)) {
                  $checkFile = $dir . '/'. array_shift($listIn);
                  if (is_file($checkFile)) {
                        if ($fileIn != '') $fileIn = filemtime($fileIn) > filemtime($checkFile) ? $fileIn : $checkFile;   
                        else $fileIn = $checkFile; 
                  } else $fileIn = lastUpdateFile( $checkFile, $fileIn);
            }
      }
      return $fileIn;
} 

function removeOrginSub($fileName) {
      $removeFile = '.|..|.sass-cache|app.css.map|.DS_Store';
      return strpos($removeFile, $fileName) === false;
}

function getCompressFromAPI($api, $content) {
      $ch = curl_init();

      curl_setopt_array($ch, [
          CURLOPT_URL => $api,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POST => true,
          CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
          CURLOPT_POSTFIELDS => http_build_query([ 'input' => $content ])
      ]);
  
      $minified = curl_exec($ch);
  
      // finally, close the request
      curl_close($ch);
  
      // output the $minified JavaScript
      return $minified;
}

$lastUpdateFile = lastUpdateFile(__DIR__ ,''); //đường dẫn đến file được lưu lại sau cùng

$fileInfo = pathinfo($lastUpdateFile); // lấy thông tin của file

$targetDir = str_replace($mirror['source'].'/', '', $fileInfo['dirname']);

$ext = $fileInfo['extension'];
if (! file_exists($targetDir)) mkdir($targetDir,0755, true);

if (strpos(COMPLIER, $ext) !== false) {     
      if ($ext == 'scss') {
            $lastUpdateFile = str_replace($fileInfo['filename'],'app', $lastUpdateFile);
            $link = $targetDir.'/app.min.css';           
            $sassRs = shell_exec('sass '.$lastUpdateFile .' ' . $link .' --style compressed'); 
      } else {
            $link = $targetDir.'/'.$fileInfo['filename'].'.min.'.$ext;
            $kindApi = $api[$ext];
            $content = file_get_contents($lastUpdateFile);
            $minified = getCompressFromAPI($kindApi, $content);
            
            $fp = fopen($link,'w');
            fwrite($fp, $minified);
            fclose($fp);
            echo 'đã biên dịch';
      }
}

$output = shell_exec('rsync -avzhe ssh --progress --delete --exclude-from=exclude.txt  --chown=www-data:www-data --perms --chmod=Du=rwx,Dgo=rx,Fu=rw,Fog=r ' .__DIR__. '/  root@45.32.123.235:/home/nginx/sites/indepgiasi/blog/wp-content/plugins/tspmaster/');
echo $output;
?> 