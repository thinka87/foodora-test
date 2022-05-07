<?php

namespace App\CustomClass;

use App\Traits\ReadJsonFileTrait;

class RestaurantSearch
{

    use ReadJsonFileTrait;
    public  $search_data;
    private  $file_path =  "";

    public function __construct(string $file_path)
    {

        $this->file_path = $file_path;
    }

    public function readJsonFile()
    {
        $this->search_data = $this->readJsonFileAsArray($this->file_path);
        return $this;
    }

    public function searchByDistance(float $current_longitude, float $current_latitude ,int $distance)
    {
        $distance_search_data = array();
        foreach ($this->search_data as $row) {
            $calculated_distance=$this->calculateDistance($current_longitude,$current_latitude,$row["longitude"],$row["latitude"]);
            if($distance >=$calculated_distance){
                //$row["distance"]=$calculated_distance;
                $distance_search_data[]=$row;
            }

        }
    
        $this->search_data=$distance_search_data;
        return $this;
    }

    private function calculateDistance(float $current_longitude, float $current_latitude, float $res_longitude, float $res_latitude)
    {

        $theta = $current_longitude - $res_longitude;
        $dist = sin(deg2rad($current_latitude)) * sin(deg2rad($res_latitude)) +  cos(deg2rad($current_latitude)) * cos(deg2rad($res_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return round($miles * 1.609344);
    }
}
