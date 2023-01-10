<?php
namespace App\Models;

class Log
{
    protected $db;
    protected $hash;
    protected $ipAddress;
    protected $userAgent;
    protected $viewDate;
    protected $imageId;

    public function __construct($db, $ipAddress, $userAgent, $imageId)
    {
        $this->db = $db;
        $this->hash = sha1($imageId . $ipAddress . $userAgent);
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->imageId = $imageId;
    }

    /*
     * Add a new record or increase if it exists
     * */
    public function createOrInc()
    {
        $sql = "
            INSERT INTO logs (
                hash,
                ip_address,
                user_agent,
                image_id
            ) VALUES (
                :hash,
                :ip_address,
                :user_agent,
                :image_id
            )
            ON DUPLICATE KEY UPDATE views_count = views_count + 1";

        $res = $this->db->prepare($sql);
        $res->execute([
            'hash' => $this->hash,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'image_id' => $this->imageId
        ]);

        $count = $res->rowCount();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /*
     * Get one record if it exists
     * */
    public function getOne()
    {
        $sql = 'SELECT * FROM logs WHERE hash = :hash';
        $res = $this->db->prepare($sql);
        $res->execute([
            'hash' => $this->hash
        ]);

        $row = $res->fetch();

        return $row;
    }
}
