<?php

namespace App\Repositories;

use App\DTO\V1\WeatherDTO;
use App\Repositories\Interfaces\WeatherRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherRepository implements WeatherRepositoryInterface
{
    /**
     * The base URL for the weather API.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * The API key for the weather API.
     *
     * @var string
     */
    protected string $apiKey;

    /**
     * The endpoints for the weather API.
     *
     * @var array
     */
    protected array $endpoints;

    /**
     * The cache configuration.
     *
     * @var array
     */
    protected array $cacheConfig;

    /**
     * The timeout for API requests.
     *
     * @var int
     */
    protected int $timeout;

    /**
     * The retry configuration.
     *
     * @var array
     */
    protected array $retryConfig;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('weather_api.base_url');
        $this->apiKey = config('weather_api.api_key');
        $this->endpoints = config('weather_api.endpoints');
        $this->cacheConfig = config('weather_api.cache');
        $this->timeout = config('weather_api.timeout');
        $this->retryConfig = config('weather_api.retry');
    }

    /**
     * Get current weather for a city.
     *
     * @param string $city
     * @param array $options
     * @return WeatherDTO
     * @throws Exception
     */
    public function getCurrentWeather(string $city, array $options = []): WeatherDTO
    {
        $cacheKey = "weather_current_{$city}_" . md5(json_encode($options));

        if ($this->cacheConfig['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $endpoint = $this->baseUrl . $this->endpoints['current'];
            
            $response = Http::timeout($this->timeout)
                ->retry($this->retryConfig['times'], $this->retryConfig['sleep'])
                ->get($endpoint, array_merge([
                    'key' => $this->apiKey,
                    'q' => $city,
                ], $options));

            if ($response->failed()) {
                throw new Exception("Failed to fetch weather data: " . $response->body());
            }

            $data = $response->json();
            
            $weatherDTO = new WeatherDTO(
                $data['location']['name'],
                $data['location']['country'],
                $data['current']['temp_c'],
                $data['current']['condition']['text'],
                $data['current']['humidity'],
                $data['current']['wind_kph'],
                array_merge(['city' => $city], $options),
                $data
            );

            if ($this->cacheConfig['enabled']) {
                Cache::put($cacheKey, $weatherDTO, $this->cacheConfig['ttl'] * 60);
            }

            return $weatherDTO;
        } catch (Exception $e) {
            Log::error("Weather API error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get weather forecast for a city.
     *
     * @param string $city
     * @param int $days
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function getForecast(string $city, int $days = 3, array $options = []): array
    {
        $cacheKey = "weather_forecast_{$city}_{$days}_" . md5(json_encode($options));

        if ($this->cacheConfig['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $endpoint = $this->baseUrl . $this->endpoints['forecast'];
            
            $response = Http::timeout($this->timeout)
                ->retry($this->retryConfig['times'], $this->retryConfig['sleep'])
                ->get($endpoint, array_merge([
                    'key' => $this->apiKey,
                    'q' => $city,
                    'days' => $days,
                ], $options));

            if ($response->failed()) {
                throw new Exception("Failed to fetch forecast data: " . $response->body());
            }

            $data = $response->json();
            
            $forecast = [];
            foreach ($data['forecast']['forecastday'] as $day) {
                $forecast[] = [
                    'date' => $day['date'],
                    'max_temp' => $day['day']['maxtemp_c'],
                    'min_temp' => $day['day']['mintemp_c'],
                    'condition' => $day['day']['condition']['text'],
                    'icon' => $day['day']['condition']['icon'],
                    'humidity' => $day['day']['avghumidity'],
                    'wind_speed' => $day['day']['maxwind_kph'],
                    'chance_of_rain' => $day['day']['daily_chance_of_rain'],
                ];
            }

            if ($this->cacheConfig['enabled']) {
                Cache::put($cacheKey, $forecast, $this->cacheConfig['ttl'] * 60);
            }

            return $forecast;
        } catch (Exception $e) {
            Log::error("Weather API forecast error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search for cities by name.
     *
     * @param string $query
     * @return array
     * @throws Exception
     */
    public function searchCity(string $query): array
    {
        $cacheKey = "weather_search_" . md5($query);

        if ($this->cacheConfig['enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $endpoint = $this->baseUrl . $this->endpoints['search'];
            
            $response = Http::timeout($this->timeout)
                ->retry($this->retryConfig['times'], $this->retryConfig['sleep'])
                ->get($endpoint, [
                    'key' => $this->apiKey,
                    'q' => $query,
                ]);

            if ($response->failed()) {
                throw new Exception("Failed to search cities: " . $response->body());
            }

            $data = $response->json();
            
            $cities = [];
            foreach ($data as $city) {
                $cities[] = [
                    'id' => $city['id'],
                    'name' => $city['name'],
                    'region' => $city['region'],
                    'country' => $city['country'],
                    'lat' => $city['lat'],
                    'lon' => $city['lon'],
                ];
            }

            if ($this->cacheConfig['enabled']) {
                Cache::put($cacheKey, $cities, $this->cacheConfig['ttl'] * 60);
            }

            return $cities;
        } catch (Exception $e) {
            Log::error("Weather API search error: " . $e->getMessage());
            throw $e;
        }
    }
}
