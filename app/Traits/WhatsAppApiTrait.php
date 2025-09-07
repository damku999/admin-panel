<?php

namespace App\Traits;

use App\Services\AppSettingService;

trait WhatsAppApiTrait
{
    protected function getSenderId()
    {
        return AppSettingService::get('whatsapp_sender_id', '919727793123');
    }

    protected function getBaseUrl()
    {
        return AppSettingService::get('whatsapp_base_url', 'https://api.botmastersender.com/api/v1/');
    }

    protected function getAuthToken()
    {
        return AppSettingService::get('whatsapp_auth_token', '53eb1f03-90be-49ce-9dbe-b23fe982b31f');
    }

    // mediaurl
    protected function whatsAppSendMessage($messageText, $receiverId)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getBaseUrl() . '?action=send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'senderId' => $this->getSenderId(),
                'authToken' => $this->getAuthToken(),
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
                CURLOPT_URL => $this->getBaseUrl() . '?action=send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'senderId' => $this->getSenderId(),
                    'authToken' => $this->getAuthToken(),
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
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $website = AppSettingService::get('business_website', 'https://parthrawal.in');
        $tagline = AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.');
        $contactPhone = AppSettingService::get('contact_phone', '+919727793123');

        return "Dear {$customer->name}

Welcome to the world of insurance solutions! I'm {$advisorName}, your dedicated insurance advisor here to guide you through every step of your insurance journey. Whether you're seeking protection for your loved ones, securing your assets, or planning for the future, I'm committed to providing personalized advice and finding the perfect insurance solutions tailored to your needs. Feel free to reach out anytime with questions or concerns. Let's work together to safeguard what matters most to you!

Best regards,
{$advisorName}
{$website}
Your Trusted Insurance Advisor
\"{$tagline}\"";
    }
    public function insuranceAdded($customer_insurance)
    {
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $website = AppSettingService::get('business_website', 'https://parthrawal.in');
        $tagline = AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.');
        
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        $policy_detail = trim($customer_insurance->premiumType->name . ' ' . $customer_insurance->registration_no);
        return "Dear {$customer_insurance->customer->name}

Thank you for entrusting me with your insurance needs. Attached, you'll find the policy document with *Policy No. {$customer_insurance->policy_no}* of your *{$policy_detail}* which expire on *{$expired_date}*. If you have any questions or need further assistance, please don't hesitate to reach out.

Best regards,
{$advisorName}
{$website}
Your Trusted Insurance Advisor
\"{$tagline}\"";
    }

    public function renewalReminder($customer_insurance)
    {
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $website = AppSettingService::get('business_website', 'https://parthrawal.in');
        $tagline = AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.');
        $contactPhone = AppSettingService::get('contact_phone', '+919727793123');
        
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        return "Dear *{$customer_insurance->customer->name}*

Your *{$customer_insurance->premiumType->name}*  Under Policy No *{$customer_insurance->policy_no}* of *{$customer_insurance->insuranceCompany->name}* is due for renewal on *{$expired_date}*. To ensure continuous coverage, please renew by the due date. For assistance, contact us at {$contactPhone}.

Best regards,
{$advisorName}
{$website}
Your Trusted Insurance Advisor
\"{$tagline}\"";
    }

    public function renewalReminderVehicle($customer_insurance)
    {
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $website = AppSettingService::get('business_website', 'https://parthrawal.in');
        $tagline = AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.');
        $contactPhone = AppSettingService::get('contact_phone', '+919727793123');
        
        $expired_date = date('d-m-Y', strtotime($customer_insurance->expired_date));
        return "Dear *{$customer_insurance->customer->name}*

Your *{$customer_insurance->premiumType->name}* Under Policy No *{$customer_insurance->policy_no}* of *{$customer_insurance->insuranceCompany->name}* for Vehicle Number *{$customer_insurance->registration_no}* is due for renewal on *{$expired_date}*. To ensure continuous coverage, please renew by the due date. For assistance, contact us at {$contactPhone}.

Best regards,
{$advisorName}
{$website}
Your Trusted Insurance Advisor
\"{$tagline}\"";
    }

    public function claimNumberAssigned($claim)
    {
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $contactPhone = AppSettingService::get('contact_phone', '+919727793123');
        
        $vehicleText = '';
        if ($claim->vehicle_number) {
            $vehicleText = " against your vehicle number *{$claim->vehicle_number}*";
        }
        
        return "Dear *{$claim->customer->name}*,

Your Claim Number *{$claim->insurance_claim_number}* is generated{$vehicleText}. For further assistance kindly contact me.

Best regards,
{$advisorName}
{$contactPhone}";
    }

    public function claimClosed($claim, $closureReason)
    {
        $advisorName = AppSettingService::get('insurance_advisor_name', 'Parth Rawal');
        $website = AppSettingService::get('business_website', 'https://parthrawal.in');
        $tagline = AppSettingService::get('business_tagline', 'Think of Insurance, Think of Us.');
        $contactPhone = AppSettingService::get('contact_phone', '+919727793123');
        
        $claimReference = $claim->insurance_claim_number ?: "ID: {$claim->id}";
        $vehicleText = $claim->vehicle_number ? " for vehicle number *{$claim->vehicle_number}*" : '';
        
        return "Dear *{$claim->customer->name}*,

Your Claim *{$claimReference}*{$vehicleText} has been closed.

*Closure Reason:* {$closureReason}

If you have any questions regarding this claim closure, please feel free to contact us.

Best regards,
{$advisorName}
{$website}
Your Trusted Insurance Advisor
\"{$tagline}\"
{$contactPhone}";
    }
}
