<?php

namespace App\Http\Services;
use  App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsService
{


    public function sendSMS($data)
    {
        $SMS_partner_ID = config('app.SMS_Partner_ID');
        $SMS_API_Key = config('app.SMS_API_Key');
        $SMS_short_Code = config('app.SMS_short_Code');
        $mobile_phone = $data['mobile'];
        $sendData = [
            "partnerID" => $SMS_partner_ID,
            "apikey" => $SMS_API_Key,
            "shortcode" => $SMS_short_Code,
            "mobile" => $mobile_phone,
            "message" => $data['message'],
        ];

        $apiURL = 'https://quicksms.advantasms.com/api/services/sendsms/';

        // Initialize cURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiURL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: FetchWantedList/1.0'
        ]);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($sendData));

        // Execute cURL request
        $curl_response = curl_exec($curl);
        $error_message = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close cURL
        curl_close($curl);

        // Check if cURL request failed
        if ($curl_response === false) {
        
            return null;
        }

        // Log API response
        if ($http_code == 200) {
            Log::info('API Response: ' . $curl_response);
        } else {
            Log::error('API Request failed with status code: ' . $http_code);
        }

        // Decode API response
        $decodedResponse = json_decode($curl_response, true);

        Log::info("Decoded response is: " . json_encode($decodedResponse));

        return $decodedResponse; // Return response instead of redirecting
    }


}