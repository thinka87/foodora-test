<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RestaurantSearchRequest;
use App\CustomClass\RestaurantSearch;

class RestaurantController extends Controller
{
    public function search(RestaurantSearchRequest $request){
        $restaurant_search_data = (new RestaurantSearch(storage_path()."/reastaurent_data.json"))->readJsonFile();

        $inputs = $request->only(['restaurant_name', 'cuisine','city','distance','longitude','latitude','search_text']);

        if($inputs["distance"]> 0 && is_numeric(number_format($inputs["longitude"], 6, '.','')) && is_numeric(number_format($inputs["latitude"], 6, '.','')) ){
            $restaurant_search_data = $restaurant_search_data->searchByDistance($inputs["longitude"],$inputs["latitude"],$inputs["distance"]);
        }
        print_r($restaurant_search_data->search_data);
    }
}
