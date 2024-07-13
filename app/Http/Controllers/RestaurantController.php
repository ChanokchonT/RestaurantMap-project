<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', 'Bang Sue');
        $restaurants = $this->getRestaurants($search);

        if ($request->ajax()) {
            return response()->json(['restaurants' => $restaurants]);
        }

        return view('restaurant.index', compact('restaurants', 'search'));
    }

    private function getRestaurants($location)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => [
                'query' => 'restaurant in ' . $location,
                'key' => $apiKey
            ],
            'verify' => false  
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['results'];
    }

    // private function getRestaurants($location)
    // {
    //     $apiKey = env('GOOGLE_MAPS_API_KEY');
    //     $client = new Client();
    //     $restaurants = [];
    //     $nextPageToken = null;

    //     do {
    //         // Build the query parameters
    //         $queryParams = [
    //             'query' => 'restaurant in ' . $location,
    //             'key' => $apiKey
    //         ];

    //         if ($nextPageToken) {
    //             $queryParams['pagetoken'] = $nextPageToken;
    //         }

    //         $response = $client->get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
    //             'query' => $queryParams,
    //             'verify' => false  
    //         ]);

    //         $data = json_decode($response->getBody(), true);

    //         // Merge the results
    //         $restaurants = array_merge($restaurants, $data['results']);

            
    //         $nextPageToken = $data['next_page_token'] ?? null;

    //         // Wait for a short time to allow the next page to be ready
    //         if ($nextPageToken) {
    //             sleep(2); 
    //         }
    //     } while ($nextPageToken);

    //     return $restaurants;
    // }
}
