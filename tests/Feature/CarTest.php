<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    private function createDriver(): User
    {
        return User::factory()->create([
            'phone' => '+998901112233',
            'role' => UserRole::DRIVER,
        ]);
    }

    private function createPassenger(): User
    {
        return User::factory()->create([
            'phone' => '+998904445566',
            'role' => UserRole::PASSENGER,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_cars_endpoints(): void
    {
        $response = $this->getJson('/api/cars');
        $response->assertStatus(401);

        $response = $this->postJson('/api/cars', [
            'brand' => 'Chevrolet',
            'model' => 'Cobalt',
            'license_plate' => '01A123AA',
        ]);
        $response->assertStatus(401);
    }

    public function test_passenger_cannot_access_cars_endpoints(): void
    {
        $passenger = $this->createPassenger();
        $token = $passenger->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cars');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Действие доступно только водителям.',
            ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cars', [
                'brand' => 'Chevrolet',
                'model' => 'Cobalt',
                'license_plate' => '01A123AA',
            ]);

        $response->assertStatus(403);
    }

    public function test_driver_can_add_car_successfully(): void
    {
        $driver = $this->createDriver();
        $token = $driver->createToken('auth_token')->plainTextToken;

        $payload = [
            'brand' => 'Chevrolet',
            'model' => 'Lacetti',
            'license_plate' => '01B777BB',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cars', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно добавлен.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'brand',
                    'model',
                    'license_plate',
                ],
            ]);

        $this->assertDatabaseHas('cars', [
            'user_id' => $driver->id,
            'brand' => 'Chevrolet',
            'model' => 'Lacetti',
            'license_plate' => '01B777BB',
        ]);
    }

    public function test_add_car_validation_fails_for_missing_fields(): void
    {
        $driver = $this->createDriver();
        $token = $driver->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cars', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand', 'model', 'license_plate']);
    }

    public function test_add_car_validation_fails_for_duplicate_license_plate(): void
    {
        $driver = $this->createDriver();
        $token = $driver->createToken('auth_token')->plainTextToken;

        Car::create([
            'user_id' => $driver->id,
            'brand' => 'Toyota',
            'model' => 'Camry',
            'license_plate' => '01A999AA',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cars', [
                'brand' => 'Hyundai',
                'model' => 'Elantra',
                'license_plate' => '01A999AA',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['license_plate']);
    }

    public function test_driver_can_list_only_their_cars(): void
    {
        $driverA = $this->createDriver();
        $driverB = User::factory()->create([
            'phone' => '+998909990011',
            'role' => UserRole::DRIVER,
        ]);

        Car::create([
            'user_id' => $driverA->id,
            'brand' => 'Chevrolet',
            'model' => 'Tracker',
            'license_plate' => '01A111AA',
        ]);

        Car::create([
            'user_id' => $driverB->id,
            'brand' => 'BYD',
            'model' => 'Song Plus',
            'license_plate' => '01B222BB',
        ]);

        $tokenA = $driverA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/cars');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['license_plate' => '01A111AA'])
            ->assertJsonMissing(['license_plate' => '01B222BB']);
    }

    public function test_driver_can_update_their_car(): void
    {
        $driver = $this->createDriver();
        $token = $driver->createToken('auth_token')->plainTextToken;

        $car = Car::create([
            'user_id' => $driver->id,
            'brand' => 'Chevrolet',
            'model' => 'Cobalt',
            'license_plate' => '01C333CC',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/cars/' . $car->id, [
                'brand' => 'Chevrolet',
                'model' => 'Cobalt Midnight',
                'license_plate' => '01C333CC',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Информация об автомобиле успешно обновлена.',
            ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'model' => 'Cobalt Midnight',
            'license_plate' => '01C333CC',
        ]);
    }

    public function test_driver_cannot_update_another_drivers_car(): void
    {
        $driverA = $this->createDriver();
        $driverB = User::factory()->create([
            'phone' => '+998907778899',
            'role' => UserRole::DRIVER,
        ]);

        $carB = Car::create([
            'user_id' => $driverB->id,
            'brand' => 'Kia',
            'model' => 'K5',
            'license_plate' => '01K555KK',
        ]);

        $tokenA = $driverA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->putJson('/api/cars/' . $carB->id, [
                'model' => 'K5 GT',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Вы не являетесь владельцем этого автомобиля.',
            ]);
    }

    public function test_driver_can_delete_their_car(): void
    {
        $driver = $this->createDriver();
        $token = $driver->createToken('auth_token')->plainTextToken;

        $car = Car::create([
            'user_id' => $driver->id,
            'brand' => 'Daewoo',
            'model' => 'Nexia',
            'license_plate' => '01N111NN',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/cars/' . $car->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автомобиль успешно удален.',
            ]);

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id,
        ]);
    }

    public function test_driver_cannot_delete_another_drivers_car(): void
    {
        $driverA = $this->createDriver();
        $driverB = User::factory()->create([
            'phone' => '+998901239876',
            'role' => UserRole::DRIVER,
        ]);

        $carB = Car::create([
            'user_id' => $driverB->id,
            'brand' => 'BMW',
            'model' => 'X5',
            'license_plate' => '01B005XX',
        ]);

        $tokenA = $driverA->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->deleteJson('/api/cars/' . $carB->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('cars', [
            'id' => $carB->id,
        ]);
    }
}
