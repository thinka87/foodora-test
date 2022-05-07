<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RestaurantSearchRequest;
use App\CustomClass\RestaurantSearch;

class RestaurantController extends Controller
{
    public function search(RestaurantSearchRequest $request){
        
        $search_fields = $request->only(['restaurant_name', 'cuisine','city','distance','longitude','latitude','search_text']);
        $search_distance=false;

        if($search_fields["distance"]> 0 && is_numeric($search_fields["longitude"]) && is_numeric($search_fields["latitude"]) ){
            $inputs["latitude"]=number_format($search_fields["latitude"], 6, '.','');
            $inputs["longitude"]=number_format($search_fields["longitude"], 6, '.','');
            $search_distance=true;
        }
         
        $restaurant_search_data = (new RestaurantSearch(storage_path()."/reastaurent_data.json",$search_fields,$search_distance))->readJsonFile();
        $restaurant_search_data = $restaurant_search_data->searchRestaurants();
        print_r($restaurant_search_data->search_data);
    }
}
