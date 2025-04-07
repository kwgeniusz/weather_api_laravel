<?php

namespace Tests\Feature\Api;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_get_favorites()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create some favorites for the user
        Favorite::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'city',
                        'country',
                        'latitude',
                        'longitude',
                        'is_default'
                    ]
                ]
            ]);
            
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_can_get_default_favorite()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create a default favorite for the user
        Favorite::factory()->create([
            'user_id' => $user->id,
            'is_default' => true
        ]);
        
        // Create some non-default favorites
        Favorite::factory()->count(2)->create([
            'user_id' => $user->id,
            'is_default' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/favorites/default');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'city',
                    'country',
                    'latitude',
                    'longitude',
                    'is_default'
                ]
            ]);
            
        $this->assertTrue($response->json('data.is_default'));
    }

    public function test_user_can_add_favorite()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $favoriteData = [
            'city' => 'Berlin',
            'country' => 'Germany',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
            'is_default' => false
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/favorites', $favoriteData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'city',
                    'country',
                    'latitude',
                    'longitude',
                    'is_default'
                ],
                'message'
            ]);
            
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'city' => $favoriteData['city'],
            'country' => $favoriteData['country']
        ]);
    }

    public function test_user_cannot_add_duplicate_favorite()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $city = 'Berlin';
        
        // Create an existing favorite
        Favorite::factory()->create([
            'user_id' => $user->id,
            'city' => $city
        ]);
        
        $favoriteData = [
            'city' => $city,
            'country' => 'Germany',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
            'is_default' => false
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/favorites', $favoriteData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['city']);
    }

    public function test_user_can_remove_favorite()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create a favorite for the user
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/favorites/{$favorite->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Favorite removed successfully'
            ]);
            
        $this->assertDatabaseMissing('favorites', [
            'id' => $favorite->id
        ]);
    }

    public function test_user_cannot_remove_another_users_favorite()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user1->createToken('test-token')->plainTextToken;
        
        // Create a favorite for user2
        $favorite = Favorite::factory()->create([
            'user_id' => $user2->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/v1/favorites/{$favorite->id}");

        $response->assertStatus(404);
            
        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id
        ]);
    }

    public function test_user_can_set_default_favorite()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Create a default favorite
        $defaultFavorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'is_default' => true
        ]);
        
        // Create a non-default favorite
        $newFavorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'is_default' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/favorites/{$newFavorite->id}/default");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Default favorite set successfully'
            ]);
            
        // The old default should no longer be default
        $this->assertDatabaseHas('favorites', [
            'id' => $defaultFavorite->id,
            'is_default' => false
        ]);
        
        // The new favorite should now be default
        $this->assertDatabaseHas('favorites', [
            'id' => $newFavorite->id,
            'is_default' => true
        ]);
    }

    public function test_user_cannot_set_another_users_favorite_as_default()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user1->createToken('test-token')->plainTextToken;
        
        // Create a favorite for user2
        $favorite = Favorite::factory()->create([
            'user_id' => $user2->id,
            'is_default' => false
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/v1/favorites/{$favorite->id}/default");

        $response->assertStatus(404);
            
        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id,
            'is_default' => false
        ]);
    }
}
