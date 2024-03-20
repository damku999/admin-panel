<?php

namespace App\Traits;

trait WhatsAppApiTrait
{
    protected $base_url = 'https://api.botmastersender.com/api/v2/';
    protected $senderId = '919727793123';
    protected $authToken = '5cd77122-acbe-419d-a662-1300d3b20565';
// mediaurl
    protected function whatsAppSendMessage($messageText, $receiverId)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url . '?action=send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'senderId' => $this->senderId,
                'authToken' => $this->authToken,
                'messageText' => $messageText,
                'receiverId' => $this->validateAndFormatMobileNumber($receiverId),
            ],
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    protected function whatsAppSendMessageWithAttachment($messageText, $receiverId, $filePath)
    {
        try {

            $curl = curl_init();

            $fileHandle = fopen($filePath, 'r');

            $fileSize = filesize($filePath);
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->base_url . '?action=send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => ['senderId' => $this->senderId,
                    'authToken' => $this->authToken,
                    'messageText' => $messageText,
                    'receiverId' => $this->validateAndFormatMobileNumber($receiverId),
                    'uploadFile' => curl_file_create($filePath, mime_content_type($filePath), basename($filePath)),
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);
            fclose($fileHandle);
            return $response;
        } catch (\Throwable $th) {
            return $th->getMessage();
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
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }
    public function insuranceAdded($customer_insurance)
    {
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        return "Dear {$customer_insurance->customer->name}

Thank you for entrusting me with your insurance needs. Attached, you'll find the policy document with Policy No. {$customer_insurance->policy_no} of your {$customer_insurance->premiumType->name} {$customer_insurance->registration_no} which expire on {$expired_date}. If you have any questions or need further assistance, please don't hesitate to reach out.

Best regards,
Parth Rawal
Your Trusted Insurance Advisor
\"Think of Insurance, Think of Us.\"";
    }
}
