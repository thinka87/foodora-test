<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantSearchRequest;
use App\CustomClass\RestaurantSearch;

class RestaurantController extends Controller
{
    public function search(RestaurantSearchRequest $request)
    {
        try {
            //Get search filed values from json body
            $search_fields = $request->only(['restaurant_name', 'cuisine', 'city', 'distance', 'longitude', 'latitude', 'search_text']);
            $search_distance = false;

            //Make sure, want to calculate distance
            if ($search_fields["distance"] > 0 && is_numeric($search_fields["longitude"]) && is_numeric($search_fields["latitude"])) {
                $inputs["latitude"] = number_format($search_fields["latitude"], 6, '.', '');
                $inputs["longitude"] = number_format($search_fields["longitude"], 6, '.', '');
                $search_distance = true;
            }
            //Call to RestaurantSearch class creating an object
            $restaurant_search_data = (new RestaurantSearch(storage_path() . "/reastaurent_data.json", $search_fields, $search_distance))
                ->readJsonFile()
                ->searchRestaurants()
                ->getSearchResult();

            return $restaurant_search_data->response;
        } catch (\Exception $e) {

            return $this->response = response()->json([
                'success'   => false,
                'message'   => "internal_server_error"
            ], 500);
        }
    }
}
