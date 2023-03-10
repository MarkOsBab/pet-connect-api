<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestAuthorization;

class UserTest extends TestCase
{
    use RefreshDatabase, TestAuthorization;

    const USER_URL = 'api/users';
    const AUTH_URL = 'api/auth';

    public function test_store_user()
    {
        $data = [
            'firstname' => 'userTest',
            'lastname' => 'userTest',
            'username' => 'userTest',
            'email' => 'userTest@user.com',
            'password' => 'password',
            'phone' => '096390295',
            'address' => 'userTest'
        ];
        $response = $this->withAuth()
            ->post(self::USER_URL, $data);
        
        $response->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $response['id'],
                'firstname' => $response['firstname'],
                'lastname' => $response['lastname'],
                'username' => $response['username'],
                'email' => $response['email'],
                'phone' => $response['phone'],
                'address' => $response['address'],
            ]);
    }

   public function test_update_user()
   {
        $user = User::factory()->create();

        $data = [
            'username' => $user->username,
            'password' => 'password',
        ];

        $response = $this->post(self::AUTH_URL.'/login', $data)
            ->assertStatus(Response::HTTP_OK);
        
        $updateData = [
            'firstname' => 'updateTest',
            'lastname' => 'updateTest',
            'phone' => '096390295',
            'address' => 'updateTest'
        ];
        
        $this->withHeader('Authorization', 'Bearer '. $response['access_token'])
            ->post(self::USER_URL.'/'.$user->id, $updateData)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $user->id,
                'firstname' => $updateData['firstname'],
                'lastname' => $updateData['lastname'],
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $updateData['phone'],
                'address' => $updateData['address']
            ]);
   }

   public function test_destroy_user()
   {
        $user = User::factory()->create();

        $data = [
            'username' => $user->username,
            'password' => 'password',
        ];

        $response = $this->post(self::AUTH_URL.'/login', $data)
            ->assertStatus(Response::HTTP_OK);

        $this->withHeader('Authorization', 'Bearer '. $response['access_token'])
            ->delete(self::USER_URL.'/'.$user->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ]);

   }
}
