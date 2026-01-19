<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $superAdmin;
    protected User $governmentalOfficial;
    protected User $policyMaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders to set up roles and permissions
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'MainRoles']);
        
        // Create test users
        $this->createTestUsers();
    }

    private function createTestUsers(): void
    {
        $this->superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567890',
            'type' => UserTypeEnum::ADMIN,
            'active' => true,
        ]);

        $this->governmentalOfficial = User::create([
            'first_name' => 'Government',
            'last_name' => 'Official',
            'email' => 'official@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567891',
            'type' => UserTypeEnum::USER,
            'active' => true,
        ]);

        $this->policyMaker = User::create([
            'first_name' => 'Policy',
            'last_name' => 'Maker',
            'email' => 'policy@test.com',
            'password' => Hash::make('password'),
            'phone_number' => '1234567892',
            'type' => UserTypeEnum::POLICY_MAKER,
            'active' => true,
        ]);
    }

    /** @test */
    public function super_admin_can_access_profile_page()
    {
        $this->actingAs($this->superAdmin);
        
        $response = $this->get('/profile');
        
        $response->assertStatus(200);
        $response->assertSee('Profile');
        $response->assertSee('Basic Information');
        $response->assertSee('Change Password');
    }

    /** @test */
    public function governmental_official_can_access_profile_page()
    {
        $this->actingAs($this->governmentalOfficial);
        
        $response = $this->get('/profile');
        
        $response->assertStatus(200);
        $response->assertSee('Profile');
        $response->assertSee('Basic Information');
        $response->assertSee('Change Password');
    }

    /** @test */
    public function policy_maker_cannot_access_profile_page()
    {
        $this->actingAs($this->policyMaker);
        
        $response = $this->get('/profile');
        
        // Should be forbidden since Policy Makers don't have 'show user' permission
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_basic_information()
    {
        $this->actingAs($this->governmentalOfficial);
        
        $response = $this->post('/profile', [
            'data' => [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'email' => 'updated@test.com',
                'phone_number' => '9876543210',
            ]
        ]);
        
        $this->governmentalOfficial->refresh();
        
        $this->assertEquals('Updated', $this->governmentalOfficial->first_name);
        $this->assertEquals('Name', $this->governmentalOfficial->last_name);
        $this->assertEquals('updated@test.com', $this->governmentalOfficial->email);
        $this->assertEquals('9876543210', $this->governmentalOfficial->phone_number);
    }

    /** @test */
    public function user_can_change_password_with_correct_current_password()
    {
        $this->actingAs($this->governmentalOfficial);
        
        $response = $this->post('/profile', [
            'data' => [
                'first_name' => $this->governmentalOfficial->first_name,
                'last_name' => $this->governmentalOfficial->last_name,
                'email' => $this->governmentalOfficial->email,
                'phone_number' => $this->governmentalOfficial->phone_number,
                'current_password' => 'password',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        ]);
        
        $this->governmentalOfficial->refresh();
        
        $this->assertTrue(Hash::check('newpassword123', $this->governmentalOfficial->password));
    }

    /** @test */
    public function user_cannot_change_password_with_incorrect_current_password()
    {
        $this->actingAs($this->governmentalOfficial);
        
        $response = $this->post('/profile', [
            'data' => [
                'first_name' => $this->governmentalOfficial->first_name,
                'last_name' => $this->governmentalOfficial->last_name,
                'email' => $this->governmentalOfficial->email,
                'phone_number' => $this->governmentalOfficial->phone_number,
                'current_password' => 'wrongpassword',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]
        ]);
        
        $response->assertSessionHasErrors(['data.current_password']);
        
        $this->governmentalOfficial->refresh();
        
        // Password should remain unchanged
        $this->assertTrue(Hash::check('password', $this->governmentalOfficial->password));
    }

    // /** @test */
    // public function email_must_be_unique()
    // {
    //     $this->actingAs($this->governmentalOfficial);
        
    //     $response = $this->post('/profile', [
    //         'data' => [
    //             'first_name' => $this->governmentalOfficial->first_name,
    //             'last_name' => $this->governmentalOfficial->last_name,
    //             'email' => $this->superAdmin->email, // Try to use existing email
    //             'phone_number' => $this->governmentalOfficial->phone_number,
    //         ]
    //     ]);
        
    //     $response->assertSessionHasErrors(['data.email']);
        
    //     $this->governmentalOfficial->refresh();
        
    //     // Email should remain unchanged
    //     $this->assertEquals('official@test.com', $this->governmentalOfficial->email);
    // }

    // /** @test */
    // public function user_can_keep_same_email()
    // {
    //     $this->actingAs($this->governmentalOfficial);
        
    //     $response = $this->post('/profile', [
    //         'data' => [
    //             'first_name' => 'Updated',
    //             'last_name' => 'Name',
    //             'email' => $this->governmentalOfficial->email, // Keep same email
    //             'phone_number' => '9876543210',
    //         ]
    //     ]);
        
    //     $this->governmentalOfficial->refresh();
        
    //     $this->assertEquals('Updated', $this->governmentalOfficial->first_name);
    //     $this->assertEquals('official@test.com', $this->governmentalOfficial->email);
    // }
}