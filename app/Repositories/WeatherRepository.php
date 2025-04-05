<?php

namespace App\Repositories;

use App\DTO\V1\WeatherDTO;
use App\Exceptions\WeatherApiException;
use App\Repositories\Interfaces\WeatherRepositoryInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Throwable;

class WeatherRepository implements WeatherRepositoryInterface
{
    private PendingRequest $http;
    private string $baseUrl;
    private string $apiKey;
    private bool $useCache;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = config('weather_api.url');
        $this->apiKey = config('weather_api.key');
        $this->useCache = config('weather_api.cache.enabled', true);
        $this->cacheTtl = config('weather_api.cache.ttl', 3600);

        $this->http = Http::baseUrl($this->baseUrl)
            ->withQueryParameters(['key' => $this->apiKey])
            ->timeout(config('weather_api.timeout', 30))
            ->retry(
                config('weather_api.max_retries', 3),
                config('weather_api.retry_delay', 100)
            );
    }

    public function getCurrentWeather(string $city, array $options = []): WeatherDTO
    {
        $cacheKey = "weather_current_{$city}_" . md5(json_encode($options));

        if ($this->useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->http->get('/current.json', [
                'q' => $city,
                'lang' => $options['language'] ?? 'en',
                'units' => $options['units'] ?? 'metric'
            ]);

            if ($response->failed()) {
                $response->throw();
            }

            $data = $response->json();
            
            $weatherDTO = new WeatherDTO(
                $data['location']['name'],
                $data['location']['country'],
                $data['current']['temp_c'],
                $data['current']['condition']['text'],
                $data['current']['humidity'],
                $data['current']['wind_kph'],
                $options,
                $data
            );

            if ($this->useCache) {
                Cache::put($cacheKey, $weatherDTO, $this->cacheTtl);
            }

            return $weatherDTO;

        } catch (RequestException $e) {
            throw new WeatherApiException(
                $e->response?->json()['error']['message'] ?? 'Weather API error',
                $e->response?->status() ?? 500
            );
        } catch (Throwable $e) {
            throw new WeatherApiException($e->getMessage());
        }
    }

    public function getForecast(string $city, int $days = 3, array $options = []): array
    {
        $cacheKey = "weather_forecast_{$city}_{$days}_" . md5(json_encode($options));

        if ($this->useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->http->get('/forecast.json', [
                'q' => $city,
                'days' => $days,
                'lang' => $options['language'] ?? 'en',
                'units' => $options['units'] ?? 'metric'
            ]);

            if ($response->failed()) {
                $response->throw();
            }

            $data = $response->json();
            $forecast = [];

            foreach ($data['forecast']['forecastday'] as $day) {
                $forecast[] = [
                    'date' => $day['date'],
                    'max_temp' => $day['day']['maxtemp_c'],
                    'min_temp' => $day['day']['mintemp_c'],
                    'condition' => $day['day']['condition']['text'],
                    'humidity' => $day['day']['avghumidity'],
                    'wind_speed' => $day['day']['maxwind_kph'],
                    'chance_of_rain' => $day['day']['daily_chance_of_rain'],
                ];
            }

            if ($this->useCache) {
                Cache::put($cacheKey, $forecast, $this->cacheTtl);
            }

            return $forecast;

        } catch (RequestException $e) {
            throw new WeatherApiException(
                $e->response?->json()['error']['message'] ?? 'Weather API error',
                $e->response?->status() ?? 500
            );
        } catch (Throwable $e) {
            throw new WeatherApiException($e->getMessage());
        }
    }

    public function searchCity(string $query): array
    {
        $cacheKey = "weather_search_" . md5($query);

        if ($this->useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->http->get('/search.json', [
                'q' => $query
            ]);

            if ($response->failed()) {
                $response->throw();
            }

            $cities = $response->json();

            $results = array_map(function ($city) {
                return [
                    'name' => $city['name'],
                    'country' => $city['country'],
                    'region' => $city['region'],
                    'latitude' => $city['lat'],
                    'longitude' => $city['lon'],
                ];
            }, $cities);

            if ($this->useCache) {
                Cache::put($cacheKey, $results, $this->cacheTtl);
            }

            return $results;

        } catch (RequestException $e) {
            throw new WeatherApiException(
                $e->response?->json()['error']['message'] ?? 'Weather API error',
                $e->response?->status() ?? 500
            );
        } catch (Throwable $e) {
            throw new WeatherApiException($e->getMessage());
        }
    }
}
