<?php

namespace App\CustomClass;

use App\Traits\ReadJsonFileTrait;

/**
 * Restaurant Search class
 *
 * Search restaurent using json file
 *
 */ 

class RestaurantSearch
{

    use ReadJsonFileTrait;

    private  $search_data;
    private  $search_fields;
    private  $search_distance;
    private  $file_path =  "";
    private $search_field_list = array("distance" => true, "search_text" => true, "restaurant_name" => true, "cuisine" => true, "city" => true);

    public  $response;

    public function __construct(string $file_path, array $search_fields, bool $search_distance)
    {

        $this->file_path = $file_path;
        $this->search_fields = $search_fields;
        $this->search_distance = $search_distance;
    }

    /**
     * Read json file as array
     *
     * @return \App\CustomClass\RestaurantSearch
     */
    public function readJsonFile(): RestaurantSearch
    {
        $this->search_data = $this->readJsonFileAsArray($this->file_path);
        return $this;
    }

     /**
     * Serach all values in array
     *
     * @return \App\CustomClass\RestaurantSearch
     */
    public function searchRestaurants(): RestaurantSearch
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

            if ($this->checkArrayHasFalseValue($search_field_list) === false)
                $search_data[] = $row;
        }

        $this->search_data = $search_data;
        return $this;
    }

    /**
     * Calculate distance beetween two corrdinates (in KM)
     * 
     * @param float $current_longitude
     * @param float $current_latitude
     * @param float $res_longitude
     * @param float $res_latitude
     * @return float
     */
    private function calculateDistance(float $current_longitude, float $current_latitude, float $res_longitude, float $res_latitude): float
    {

        $theta = $current_longitude - $res_longitude;
        $dist = sin(deg2rad($current_latitude)) * sin(deg2rad($res_latitude)) +  cos(deg2rad($current_latitude)) * cos(deg2rad($res_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return round($miles * 1.609344);
    }
    /**
     * Serach restaurant name ,city, cuisine match with search text
     * @param string $search_text
     * @param array $data
     * @return bool
     */
    private function searchByText(string $search_text, array $data): bool
    {

        if (stristr($data["restaurantName"], $search_text) !== FALSE || stristr($data["city"], $search_text) !== FALSE || stristr($data["cuisine"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    /**
     * Serach restaurant name contains in restaurantName key
     * @param string $search_text
     * @param array $data
     * @return bool
     */
    private function searchByRestaurantName(string $search_text, array $data): bool
    {

        if (stristr($data["restaurantName"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }
    /**
     * Serach cuisine name contains in cuisine key
     * @param string $search_text
     * @param array $data
     * @return bool
     */
    private function searchByCuisine(string $search_text, array $data): bool
    {

        if (stristr($data["cuisine"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    /**
     * Serach city name contains in city key
     * @param string $search_text
     * @param array $data
     * @return bool
     */
    private function searchByCity(string $search_text, array $data): bool
    {

        if (stristr($data["city"], $search_text) !== FALSE) {
            return true;
        }

        return false;
    }

    /**
     * Check array contains a bool false value
     * @param array $array
     * @return bool
     */
    private function checkArrayHasFalseValue(array $array): bool
    {
        foreach ($array as $key => $val) {
            if ($val === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set final search result json response
     *
     * @return \App\CustomClass\RestaurantSearch
     */
    public function getSearchResult(): RestaurantSearch
    {

        $this->response = response()->json([
            'success'   => true,
            'data'      => $this->search_data
        ], 200);

        return $this;
    }
}
