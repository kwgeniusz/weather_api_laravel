<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\WeatherHistory;
use App\Services\Interfaces\WeatherServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the WeatherService to avoid actual API calls
        $this->mockWeatherService = Mockery::mock(WeatherServiceInterface::class);
        $this->app->instance(WeatherServiceInterface::class, $this->mockWeatherService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_get_current_weather()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $city = 'London';
        
        $weatherData = [
            'city' => $city,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'temperature' => 15.5,
            'feels_like' => 14.2,
            'humidity' => 76,
            'pressure' => 1011,
            'wind_speed' => 4.1,
            'wind_direction' => 'NE',
            'description' => 'Cloudy',
            'icon' => 'cloudy.png',
            'updated_at' => now()->format('Y-m-d H:i:s')
        ];
        
        $this->mockWeatherService
            ->shouldReceive('getCurrentWeather')
            ->once()
            ->with($city, 'en')
            ->andReturn((object) $weatherData);
            
        $this->mockWeatherService
            ->shouldReceive('addToHistory')
            ->once()
            ->andReturn(new WeatherHistory());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/weather/current?city={$city}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'city',
                    'country',
                    'temperature',
                    'description'
                ]
            ]);
    }

    public function test_user_can_get_weather_forecast()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $city = 'London';
        $days = 3;
        
        $forecastData = [
            [
                'city' => $city,
                'country' => 'UK',
                'date' => now()->format('Y-m-d'),
                'temperature' => 15.5,
                'description' => 'Cloudy',
                'icon' => 'cloudy.png'
            ],
            [
                'city' => $city,
                'country' => 'UK',
                'date' => now()->addDay()->format('Y-m-d'),
                'temperature' => 16.2,
                'description' => 'Partly cloudy',
                'icon' => 'partly_cloudy.png'
            ],
            [
                'city' => $city,
                'country' => 'UK',
                'date' => now()->addDays(2)->format('Y-m-d'),
                'temperature' => 17.8,
                'description' => 'Sunny',
                'icon' => 'sunny.png'
            ]
        ];
        
        $this->mockWeatherService
            ->shouldReceive('getForecast')
            ->once()
            ->with($city, $days, 'en')
            ->andReturn($forecastData);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/weather/forecast?city={$city}&days={$days}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'city',
                        'country',
                        'date',
                        'temperature',
                        'description'
                    ]
                ]
            ]);
    }

    public function test_user_can_search_cities()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $query = 'Lon';
        
        $citiesData = [
            [
                'id' => 1,
                'name' => 'London',
                'country' => 'UK',
                'latitude' => 51.5074,
                'longitude' => -0.1278
            ],
            [
                'id' => 2,
                'name' => 'Londonderry',
                'country' => 'UK',
                'latitude' => 54.9966,
                'longitude' => -7.3086
            ]
        ];
        
        $this->mockWeatherService
            ->shouldReceive('searchCities')
            ->once()
            ->with($query, 'en')
            ->andReturn($citiesData);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/weather/search?query={$query}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'country',
                        'latitude',
                        'longitude'
                    ]
                ]
            ]);
    }

    public function test_user_can_get_weather_history()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create some history records for the user
        WeatherHistory::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/weather/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'city',
                        'country',
                        'temperature',
                        'description',
                        'created_at'
                    ]
                ]
            ]);
            
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_can_clear_weather_history()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create some history records for the user
        WeatherHistory::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/weather/history');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'History cleared successfully'
            ]);
            
        $this->assertDatabaseCount('weather_histories', 0);
    }

    public function test_user_can_delete_specific_history_item()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create a history record for the user
        $historyItem = WeatherHistory::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/weather/history/{$historyItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'History item deleted successfully'
            ]);
            
        $this->assertDatabaseMissing('weather_histories', [
            'id' => $historyItem->id
        ]);
    }

    public function test_user_cannot_delete_another_users_history_item()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user1->createToken('test-token')->plainTextToken;
        
        // Create a history record for user2
        $historyItem = WeatherHistory::factory()->create([
            'user_id' => $user2->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/weather/history/{$historyItem->id}");

        $response->assertStatus(404);
            
        $this->assertDatabaseHas('weather_histories', [
            'id' => $historyItem->id
        ]);
    }
}
