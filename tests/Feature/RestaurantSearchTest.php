<?php

namespace Tests\Feature;

use Tests\TestCase;

class RestaurentSearchTest extends TestCase
{
    /**
     * A basic test for search request.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->post('/api/restaurants/search');
        $response->assertStatus(400);
    }
    public function testRequiredKeysTest()
    {
        $response = $this->post('/api/restaurants/search');
        $response->assertStatus(400);
        $response->assertSee("The restaurant name field must be present.");
        $response->assertSee("The cuisine field must be present.");
        $response->assertSee("The city field must be present.");
        $response->assertSee("The distance field must be present.");
        $response->assertSee("The longitude field must be present.");
        $response->assertSee("The latitude field must be present.");
        $response->assertSee("The search text field must be present.");
        
    }

    public function testLongLatValidationTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "180.006559",
            "latitude"=>"90.332401",
            "search_text"=> ""
        ]);
        $response->assertStatus(400);     
        $response->assertSee("The longitude must be between -180 and 180.");
        $response->assertSee("The latitude must be between -90 and 90.");
        
    }
    public function testLongLatValidationWithNegativeValuesTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "-180.006559",
            "latitude"=>"-90.332401",
            "search_text"=> ""
        ]);
        $response->assertStatus(400);     
        $response->assertSee("The longitude must be between -180 and 180.");
        $response->assertSee("The latitude must be between -90 and 90.");
        
    }

    public function testLongLatUpperBoundryValidationTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "180.000000",
            "latitude"=>"90.000000",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);     
        
    }

    public function testLongLatLowerBoundryValidationTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "-180.000000",
            "latitude"=>"-90.000000",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);     
        
    }

    public function testDataTypeOfKeysTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => 111,
            "cuisine"=>111,
            "city" =>111,
            "distance"=> "kkkk" ,
            "longitude"=> "kkk",
            "latitude"=>"kkk",
            "search_text"=> 111
        ]);
        $response->assertStatus(400);
        $response->assertSee("The restaurant name must be a string.");
        $response->assertSee("The cuisine must be a string.");
        $response->assertSee("The city must be a string.");
        $response->assertSee("The distance must be a number.");
        $response->assertSee("The longitude must be a number.");
        $response->assertSee("The latitude must be a number.");
        $response->assertSee("The search text must be a string");
        
    }

    public function testSearchResultWithDefaultValuesTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);       
    }

    public function testSearchRestaurantByNameTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "Thaimiddag",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);
        $response->assertDontSee("Hai");
        $response->assertDontSee("Zen Sushi");
        $response->assertDontSee("Delikatessen");
        $response->assertDontSee("Grönt o' Gott LTH");
        $response->assertDontSee("Salads and Smoothies City");
        $response->assertDontSee("Sushido Rörsjöstaden");
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
              0 => 
              array (
                'clientKey' => 'e5CDWLrkOYxeissNSJ',
                'restaurantName' => 'Thaimiddag',
                'cuisine' => 'Thai',
                'city' => 'Stockholm',
                'latitude' => '59.332401',
                'longitude' => '18.006559',
              ),
            ),
          ));      
    }

    public function testSearchRestaurantByCuisineTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"Thai",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);
        $response->assertDontSee("Sushi");
        $response->assertDontSee("Gourmet");
        $response->assertDontSee("Sallad");
        $response->assertDontSee("Italienskt");
        $response->assertDontSee("Asiatiskt");
        $response->assertDontSee("Nudlar");
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
              0 => 
              array (
                'clientKey' => 'e5CDWLrkOYxeissNSJ',
                'restaurantName' => 'Thaimiddag',
                'cuisine' => 'Thai',
                'city' => 'Stockholm',
                'latitude' => '59.332401',
                'longitude' => '18.006559',
              ),
            ),
          ));      
    }

    public function testSearchRestaurantByCityTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"Stockholm",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);
        $response->assertDontSee("Lund");
        $response->assertDontSee("Malmö");
        $response->assertDontSee("Göteborg");
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
              0 => 
              array (
                'clientKey' => 'e5CDWLrkOYxeissNSJ',
                'restaurantName' => 'Thaimiddag',
                'cuisine' => 'Thai',
                'city' => 'Stockholm',
                'latitude' => '59.332401',
                'longitude' => '18.006559',
              ),
            ),
          ));      
    }

    public function testSearchRestaurantByWithIn1KMAndNameTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "Thaimiddag",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "1" ,
            "longitude"=> "18.006559",
            "latitude"=>"59.332401",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
              0 => 
              array (
                'clientKey' => 'e5CDWLrkOYxeissNSJ',
                'restaurantName' => 'Thaimiddag',
                'cuisine' => 'Thai',
                'city' => 'Stockholm',
                'latitude' => '59.332401',
                'longitude' => '18.006559',
              ),
            ),
          ));      
    }

    public function testSearchNonExistRestaurantByNameTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "kkkkkkkkkkkkkkkkkkkkkk",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> ""
        ]);
        $response->assertStatus(200);
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
            ),
          ));      
    }

    public function testSearchRestaurantByTextTest()
    {
        $response = $this->post('/api/restaurants/search',[
            "restaurant_name" => "",
            "cuisine"=>"",
            "city" =>"",
            "distance"=> "" ,
            "longitude"=> "",
            "latitude"=>"",
            "search_text"=> "Stockholm"
        ]);
        $response->assertStatus(200);
        $response->assertDontSee("Lund");
        $response->assertDontSee("Malmö");
        $response->assertDontSee("Göteborg");
        $response->assertJson(array (
            'success' => true,
            'data' => 
            array (
              0 => 
              array (
                'clientKey' => 'e5CDWLrkOYxeissNSJ',
                'restaurantName' => 'Thaimiddag',
                'cuisine' => 'Thai',
                'city' => 'Stockholm',
                'latitude' => '59.332401',
                'longitude' => '18.006559',
              ),
            ),
          ));      
    }




}
