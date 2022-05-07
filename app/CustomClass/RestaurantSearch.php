<?php

namespace App\CustomClass;

use App\Traits\ReadJsonFileTrait;

class RestaurantSearch
{

    use ReadJsonFileTrait;
    public  $search_data;
    public  $search_fields;
    public  $search_distance;
    public  $response;
    private $file_path =  "";

    private $search_field_list = array("distance" => true, "search_text" => true, "restaurant_name" => true, "cuisine" => true, "city" => true);


    public function __construct(string $file_path, array $search_fields, bool $search_distance)
    {

        $this->file_path = $file_path;
        $this->search_fields = $search_fields;
        $this->search_distance = $search_distance;
    }

    public function readJsonFile()
    {
        $this->search_data = $this->readJsonFileAsArray($this->file_path);
        return $this;
    }

    public function searchRestaurants()
    {
        $search_data = array();

        foreach ($this->search_data as $row) {

            $search_field_list = $this->search_field_list;
            
            if ($this->search_distance == true) {

                $calculated_distance = $this->calculateDistance($this->search_fields["longitude"], $this->search_fields["latitude"], $row["longitude"], $row["latitude"]);
                $row["distance"] = $calculated_distance;
                if ($calculated_distance > $this->search_fields["distance"]) {
                    $search_field_list["distance"] = false;
                }
            }

            if ($this->search_fields["search_text"] != "") {

                if ($this->searchByText($this->search_fields["search_text"], $row) === false)
                        $search_field_list["search_text"] = false;
            } else {

                if ($this->search_fields["restaurant_name"] != "") {
                    if ($this->searchByRestaurantName($this->search_fields["restaurant_name"], $row) === false)
                        $search_field_list["restaurant_name"] = false;
                }

                if ($this->search_fields["cuisine"] != "") {
                    if ($this->searchByCuisine($this->search_fields["cuisine"], $row) === false)
                        $search_field_list["cuisine"] = false;
                }

                if ($this->search_fields["city"] != "") {
                    if ($this->searchByCity($this->search_fields["city"], $row) === false)
                        $search_field_list["city"] = false;
                }
            }

            if ($this->checkArrayHasFalseValue($search_field_list)===false)
                $search_data[] = $row;
        }

        $this->search_data = $search_data;
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

    private function searchByText($search_text, $data)
    {

        if (stristr($data["restaurantName"], $search_text) !== FALSE || stristr($data["city"], $search_text) !== FALSE || stristr($data["cuisine"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    private function searchByRestaurantName($search_text, $data)
    {

        if (stristr($data["restaurantName"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    private function searchByCuisine($search_text, $data)
    {

        if (stristr($data["cuisine"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    private function searchByCity($search_text, $data)
    {

        if (stristr($data["city"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    private function checkArrayHasFalseValue($array){
        foreach ($array as $key=>$val){
            if($val===false){
                return true;
            }
        }

        return false;
    }

    public function getSearchResult(){

        $this->response=response()->json([
            'success'   => true,
            'data'      => $this->search_data
        ],200);

        return $this;
    }
}
