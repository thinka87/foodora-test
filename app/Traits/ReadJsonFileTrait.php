<?php
  
namespace App\Traits;
  

trait ReadJsonFileTrait {
  

    public function readJsonFileAsArray($file_path ) {
 
        return json_decode(file_get_contents($file_path), true);
    }
}