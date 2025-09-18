<?php

namespace App\Traits;

trait WhatsAppApiTrait
{
    protected $senderId = '919727793123';
    // protected $base_url = 'https://api.botmastersender.com/api/v2/';
    // protected $authToken = '5cd77122-acbe-419d-a662-1300d3b20565';
    protected $base_url = 'https://api.botmastersender.com/api/v1/';
    protected $authToken = '53eb1f03-90be-49ce-9dbe-b23fe982b31f';

    // mediaurl
    protected function whatsAppSendMessage($messageText, $receiverId)
    {
        $formattedNumber = $this->validateAndFormatMobileNumber($receiverId);

        if (!$formattedNumber) {
            throw new \Exception("Invalid mobile number format: {$receiverId}");
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url . '?action=send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'senderId' => $this->senderId,
                'authToken' => $this->authToken,
                'messageText' => $messageText,
                'receiverId' => $formattedNumber,
            ],
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            throw new \Exception("WhatsApp API connection failed: {$curlError}");
        }

        if ($httpCode !== 200) {
            throw new \Exception("WhatsApp API returned HTTP {$httpCode}");
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON response from WhatsApp API: {$response}");
        }

        // Check if response indicates failure
        if (is_array($decodedResponse)) {
            foreach ($decodedResponse as $result) {
                if (isset($result['success']) && $result['success'] === false) {
                    $errorMsg = $result['message'] ?? 'Unknown error';

                    // Check for specific error conditions
                    if (isset($result['error']['status']) && $result['error']['status'] === 'session offline') {
                        throw new \Exception("WhatsApp session is offline. Please reconnect your WhatsApp session in BotMasterSender dashboard.");
                    }

                    if (isset($result['error']['error'])) {
                        $specificError = $result['error']['error'];
                        throw new \Exception("WhatsApp sending failed: {$specificError}");
                    }

                    throw new \Exception("WhatsApp sending failed: {$errorMsg}");
                }
            }
        }

        return $response;
    }
    protected function whatsAppSendMessageWithAttachment($messageText, $receiverId, $filePath)
    {
        $formattedNumber = $this->validateAndFormatMobileNumber($receiverId);

        if (!$formattedNumber) {
            throw new \Exception("Invalid mobile number format: {$receiverId}");
        }

        if (!file_exists($filePath)) {
            throw new \Exception("Attachment file not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new \Exception("Attachment file is not readable: {$filePath}");
        }

        try {
            $curl = curl_init();
            $fileHandle = fopen($filePath, 'r');

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->base_url . '?action=send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60, // Longer timeout for file uploads
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'senderId' => $this->senderId,
                    'authToken' => $this->authToken,
                    'messageText' => $messageText,
                    'receiverId' => $formattedNumber,
                    'uploadFile' => curl_file_create($filePath, mime_content_type($filePath), basename($filePath)),
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);
            fclose($fileHandle);

            if ($curlError) {
                throw new \Exception("WhatsApp API connection failed: {$curlError}");
            }

            if ($httpCode !== 200) {
                throw new \Exception("WhatsApp API returned HTTP {$httpCode}");
            }

            $decodedResponse = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from WhatsApp API: {$response}");
            }

            // Check if response indicates failure
            if (is_array($decodedResponse)) {
                foreach ($decodedResponse as $result) {
                    if (isset($result['success']) && $result['success'] === false) {
                        $errorMsg = $result['message'] ?? 'Unknown error';

                        // Check for specific error conditions
                        if (isset($result['error']['status']) && $result['error']['status'] === 'session offline') {
                            throw new \Exception("WhatsApp session is offline. Please reconnect your WhatsApp session in BotMasterSender dashboard.");
                        }

                        if (isset($result['error']['error'])) {
                            $specificError = $result['error']['error'];
                            throw new \Exception("WhatsApp sending failed: {$specificError}");
                        }

                        throw new \Exception("WhatsApp sending failed: {$errorMsg}");
                    }
                }
            }

            return $response;

        } catch (\Throwable $th) {
            // Re-throw as a more descriptive exception
            throw new \Exception("WhatsApp message with attachment failed: " . $th->getMessage());
        }
    }

    public function validateAndFormatMobileNumber($mobileNumber)
    {
        // Remove any non-numeric characters
        $mobileNumber = preg_replace('/\D/', '', $mobileNumber);

        // Check if the number starts with '91'
        if (substr($mobileNumber, 0, 2) !== '91') {
            // If not, prepend '91'
            $mobileNumber = '91' . $mobileNumber;
        }

        // Check if the number is a valid Indian mobile number (10 digits starting with 91)
        if (preg_match('/^91[0-9]{10}$/', $mobileNumber)) {
            return $mobileNumber; // Return the formatted number
        } else {
            return false; // Return false if the number is invalid
        }
    }

    public function newCustomerAdd($customer)
    {
        return "Dear {$customer->name}

Welcome to the world of insurance solutions! I'm Parth Rawal, your dedicated insurance advisor here to guide you through every step of your insurance journey. Whether you're seeking protection for your loved ones, securing your assets, or planning for the future, I'm committed to providing personalized advice and finding the perfect insurance solutions tailored to your needs. Feel free to reach out anytime with questions or concerns. Let's work together to safeguard what matters most to you!

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }
    public function insuranceAdded($customer_insurance)
    {
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        $policy_detail = trim($customer_insurance->premiumType->name . ' ' . $customer_insurance->registration_no);
        return "Dear {$customer_insurance->customer->name}

Thank you for entrusting me with your insurance needs. Attached, you'll find the policy document with *Policy No. {$customer_insurance->policy_no}* of your *{$policy_detail}* which expire on *{$expired_date}*. If you have any questions or need further assistance, please don't hesitate to reach out.

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }

    public function renewalReminder($customer_insurance)
    {
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        return "Dear *{$customer_insurance->customer->name}*

Your *{$customer_insurance->premiumType->name}*  Under Policy No *{$customer_insurance->policy_no}* of *{$customer_insurance->insuranceCompany->name}* is due for renewal on *{$expired_date}*. To ensure continuous coverage, please renew by the due date. For assistance, contact us at +919727793123.

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }

    public function renewalReminderVehicle($customer_insurance)
    {
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        return "Dear *{$customer_insurance->customer->name}*

Your *{$customer_insurance->premiumType->name}* Under Policy No *{$customer_insurance->policy_no}* of *{$customer_insurance->insuranceCompany->name}* for Vehicle Number *{$customer_insurance->registration_no}* is due for renewal on *{$expired_date}*. To ensure continuous coverage, please renew by the due date. For assistance, contact us at +919727793123.

Best regards,
Parth Rawal
https://parthrawal.in
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }
}
