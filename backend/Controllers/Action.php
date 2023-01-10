<?php
namespace App\Controllers;

use App\Models\Log;
use App\Utils;

class Action
{
    protected $db;
    private $queryParams;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /*
     * Send JSON response
     * */
    private function send($data, int $status = 200)
    {
        header('Content-type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    /*
     * Get and prepare IP address and User-Agent header for a log record
     * */
    private function prepareProperties()
    {
        $ipAddress = ip2long(Utils::getIpAddress());
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        return [
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent
        ];
    }

    /*
     * Action for getting image id
     * */
    private function handleGetImageId()
    {
        $min = 1;
        $max = 4;
        $id = random_int($min, $max);

        $this->send(['data' => [
            'id' => $id
        ]]);
    }

    /*
     * Action for increasing views count
     * */
    private function handleIncreaseCount()
    {
        $props = $this->prepareProperties();
        $log = new Log($this->db, $props['ipAddress'], $props['userAgent'], $this->queryParams['image_id']);
        $isSuccess = $log->createOrInc();

        $this->send(['data' => [
            'success' => $isSuccess
        ]]);
    }

    /*
     * Action for getting views count
     * */
    private function handleGetCount()
    {
        $props = $this->prepareProperties();
        $log = new Log($this->db, $props['ipAddress'], $props['userAgent'], $this->queryParams['image_id']);
        $res = $log->getOne();

        if ($res === false) {
            $this->send(['error' => 'record not found'], 404);
        } else {
            $this->send(['data' => [
                'views_count' => $res['views_count']
            ]]);
        }
    }

    /*
     * Action for handling 404 error
     * */
    private function handleNotFound()
    {
        $this->send(['error' => 'not found'], 404);
    }

    /*
     * Controller run function
     * */
    public function run()
    {
        $queryString = $_SERVER['QUERY_STRING'];
        parse_str($queryString, $this->queryParams);
        $action = $this->queryParams['action'];

        switch ($action) {
            case 'get_image_id':
                $this->handleGetImageId();
                break;
            case 'increase_count':
                $this->handleIncreaseCount();
                break;
            case 'get_count':
                $this->handleGetCount();
                break;
            default:
                $this->handleNotFound();
                break;
        }
    }
}
