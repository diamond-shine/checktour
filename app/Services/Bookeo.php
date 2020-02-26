<?php
namespace App\Services;

class Bookeo
{
    protected $apiKey;
    protected $secretKey;

    public function __construct($apiKey, $secretKey)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }

    public function getBookings($filterParams = [])
    {
        $queryParams = ['apiKey' => $this->apiKey, 'secretKey' => $this->secretKey];

        if (isset($filterParams['startTime'])) {
            $queryParams['startTime'] = $filterParams['startTime'];
        }

        if (isset($filterParams['endTime'])) {
            $queryParams['endTime'] = $filterParams['endTime'];
        }

        if (isset($filterParams['expand_customer'])) {
            $queryParams['expandCustomer'] = $filterParams['expand_customer'];
        }

        return self::query('https://api.bookeo.com/v2/bookings', $queryParams, 'GET');
    }

    public function getBooking($bookingNumber, $filterParams = [])
    {
        $queryParams = ['apiKey' => $this->apiKey, 'secretKey' => $this->secretKey];

        if (isset($filterParams['expand_customer'])) {
            $queryParams['expandCustomer'] = $filterParams['expand_customer'];
        }

        $url = 'https://api.bookeo.com/v2/bookings/' . $bookingNumber;
        return self::query($url, $queryParams, 'GET');
    }

    public function getWebhooks()
    {
        $queryParams = ['apiKey' => $this->apiKey, 'secretKey' => $this->secretKey];
        return self::query('https://api.bookeo.com/v2/webhooks', $queryParams, 'GET');
    }

    public function setWebook($data = [])
    {
        $queryParams = ['apiKey' => $this->apiKey, 'secretKey' => $this->secretKey];
        return self::query('https://api.bookeo.com/v2/webhooks', $queryParams, 'POST', $data);
    }


    protected static function query($url, $queryParams = [], $type = 'GET', $json = [])
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($type, $url, [
            'query'=> $queryParams,
            'json' => $json
        ]);

        if (!in_array($response->getStatusCode(), [200, 201])) {
            throw new \Exception('API error: response ' . $response->getStatusCode());
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }
}
