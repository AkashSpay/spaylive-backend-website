<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NTTPayinController extends Controller
{
    private $authUrl = 'https://paynetzuat.atomtech.in/ots/aipay/auth';

    private $merchantId = '445842';

    private $password = 'Test@123';

    private $reqKey = 'A4476C2062FFA58980DC8F79EB6A799E';

    private $reqSalt = 'A4476C2062FFA58980DC8F79EB6A799E';

    private $resKey = '75AEF0FA1B94B3C10D4F5B268F757F11';

    private $resSalt = '75AEF0FA1B94B3C10D4F5B268F757F11';

    private $hashRequestKey = 'KEY123657234';

    public function initiatePayment(Request $request)
    {
        $txnId = 'TXN'.time();

        $payload = [
            'payInstrument' => [
                'headDetails' => [
                    'version' => 'OTSv1.1',
                    'api' => 'AUTH',
                    'platform' => 'FLASH',
                ],

                'merchDetails' => [
                    'merchId' => $this->merchantId,
                    'userId' => '',
                    'password' => $this->password,
                    'merchTxnId' => $txnId,
                    'merchTxnDate' => now()->format('Y-m-d H:i:s'),
                ],

                'payDetails' => [
                    // 'amount' => '1.00',
                    'amount' => $request->amount,
                    'product' => 'AIPAY',
                    'custAccNo' => '213232323',
                    'txnCurrency' => 'INR',
                ],

                'custDetails' => [
                    // 'custEmail' => 'test@test.com',
                    // 'custMobile' => '9999999999',
                    'custEmail' => $request->email,
                    'custMobile' => $request->mobile,
                ],

                'extras' => [
                    'udf1' => '',
                    'udf2' => '',
                    'udf3' => '',
                    'udf4' => '',
                    'udf5' => '',
                ],
            ],
        ];

        $jsonPayload = json_encode($payload);

        $encryptedData = $this->encryptAES($jsonPayload);

        $postData = [
            'merchId' => $this->merchantId,
            'encData' => $encryptedData,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->authUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),

            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,

            // CURLOPT_CAINFO => base_path('cacert.pem'),

            CURLOPT_VERBOSE => true,

            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_USERAGENT => 'Mozilla/5.0',

            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);

        $error = curl_error($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            return response()->json([
                'status' => false,
                'error' => $error,
            ]);
        }

        parse_str($response, $responseArray);

        if (! isset($responseArray['encData'])) {
            return response()->json([
                'status' => false,
                'raw_response' => $response,
            ]);
        }

        $decrypted = $this->decryptAES($responseArray['encData']);

        // return response()->json([
        //     'status' => true,
        //     'txn_id' => $txnId,
        //     'encrypted_response' => $responseArray['encData'],
        //     'decrypted_response' => json_decode($decrypted, true),
        // ]);
        $decoded = json_decode($decrypted, true);

        return response()->json([
            'status' => true,
            'txn_id' => $txnId,
            'atomTokenId' => $decoded['atomTokenId'] ?? null,
            'merchId' => $this->merchantId,
            'full_response' => $decoded,
        ]);
    }

    public function callback(Request $request)
    {
        $encData = $request->encData;

        $decrypted = $this->decryptAES($encData);

        return response()->json([
            'status' => true,
            'data' => json_decode($decrypted, true),
        ]);
    }

    private function encryptAES($data)
    {
        $method = 'AES-256-CBC';

        $iv = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

        $chars = array_map('chr', $iv);

        $IVbytes = implode($chars);

        $salt1 = mb_convert_encoding($this->reqKey, 'UTF-8');

        $key1 = mb_convert_encoding($this->reqKey, 'UTF-8');

        $hash = openssl_pbkdf2(
            $key1,
            $salt1,
            '256',
            '65536',
            'sha512'
        );

        $encrypted = openssl_encrypt(
            $data,
            $method,
            $hash,
            OPENSSL_RAW_DATA,
            $IVbytes
        );

        return strtoupper(bin2hex($encrypted));
    }

    private function decryptAES($data)
    {
        $dataEncypted = hex2bin($data);

        $method = 'AES-256-CBC';

        $iv = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

        $chars = array_map('chr', $iv);

        $IVbytes = implode($chars);

        $salt1 = mb_convert_encoding($this->resKey, 'UTF-8');

        $key1 = mb_convert_encoding($this->resKey, 'UTF-8');

        $hash = openssl_pbkdf2(
            $key1,
            $salt1,
            '256',
            '65536',
            'sha512'
        );

        return openssl_decrypt(
            $dataEncypted,
            $method,
            $hash,
            OPENSSL_RAW_DATA,
            $IVbytes
        );
    }
}
