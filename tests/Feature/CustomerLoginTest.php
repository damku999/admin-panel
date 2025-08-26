<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerLoginTest extends TestCase
{
    /**
     * Test customer login with specific credentials.
     */
    public function test_customer_login_with_provided_credentials()
    {
        // Test credentials
        $email = 'damku999@gmail.com';
        $password = 'Devyaan@1967';

        // Find the customer
        $customer = Customer::where('email', $email)->first();
        
        $this->assertNotNull($customer, 'Customer should exist in database');
        $this->assertTrue($customer->status, 'Customer should be active');

        // Test password verification
        $passwordCorrect = Hash::check($password, $customer->password);
        $this->assertTrue($passwordCorrect, 'Password should be correct');

        // First test if the login route exists
        $loginPageResponse = $this->get('/customer/login');
        echo "\n=== Login Page Test ===\n";
        echo "Login page status: " . $loginPageResponse->getStatusCode() . "\n";
        
        // Test actual login via POST request with CSRF protection disabled for testing
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                         ->post('/customer/login', [
                             'email' => $email,
                             'password' => $password,
                         ]);

        echo "\n=== Login Response Debug ===\n";
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Is Redirect: " . ($response->isRedirect() ? 'YES' : 'NO') . "\n";
        
        if ($response->isRedirect()) {
            echo "Redirect Location: " . $response->headers->get('Location') . "\n";
        } else {
            echo "Response Content: " . substr($response->getContent(), 0, 200) . "...\n";
        }

        // Should redirect after successful login (either to dashboard or change password)
        if (!$response->isRedirect()) {
            // Try to see what errors occurred
            $errors = session('errors');
            if ($errors) {
                echo "Validation Errors: " . print_r($errors->all(), true) . "\n";
            }
        }

        // Check if customer is authenticated
        $this->assertAuthenticated('customer');

        // Get the authenticated customer
        $authenticatedCustomer = auth('customer')->user();
        $this->assertEquals($customer->id, $authenticatedCustomer->id);

        echo "\n=== Login Test Results ===\n";
        echo "✅ Customer found: {$customer->name}\n";
        echo "✅ Customer is active\n"; 
        echo "✅ Password is correct\n";
        echo "✅ Login successful\n";
        echo "✅ Customer authenticated as: {$authenticatedCustomer->name}\n";
        
        // Test accessing change password page
        $changePasswordResponse = $this->get('/customer/change-password');
        
        if ($changePasswordResponse->isSuccessful()) {
            echo "✅ Change password page accessible\n";
        } else {
            echo "❌ Change password page redirect status: " . $changePasswordResponse->getStatusCode() . "\n";
            if ($changePasswordResponse->isRedirect()) {
                echo "   Redirected to: " . $changePasswordResponse->headers->get('Location') . "\n";
            }
        }
    }

    /**
     * Test accessing change password page when not authenticated.
     */
    public function test_change_password_redirects_when_not_authenticated()
    {
        $response = $this->get('/customer/change-password');
        
        // Should redirect to login when not authenticated
        $response->assertRedirect('/customer/login');
        
        echo "\n=== Unauthenticated Access Test ===\n";
        echo "✅ Correctly redirects to login when not authenticated\n";
    }
}