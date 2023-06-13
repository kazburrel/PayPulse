<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'fname' => 'Kaz',
            'lname' => 'Obiora',
            'other_name' => '',
            'email' => 'tomslit001@gmail.com',
            'phone' => '+2343463483463',
            'dob' => '4/01/1995',
            'address' => 'Enugu Independence Layout',
            'state' => 'Enugu',
            'post_code' => '400102',
            'country' => 'Nigeria',
            'account_type' => 'Savings',
            'gender' => 'Male',
            'employment_status' => 'Self Employed',
            't_c' => 'checked',
            'security_question' => 'Bruno',
            'username' => 'Kaz.b',
            'password' => '1234567890',
            'password_confirmation' => '1234567890',
        ]);

        $this->assertAuthenticated();
        $response->assertNoContent();
    }
}
