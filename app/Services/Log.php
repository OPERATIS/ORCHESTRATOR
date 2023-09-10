<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Log as LogModel;

class Log
{
    public $platform;
    public $connectId;

    public function __construct($platform, $connectId)
    {
        $this->platform = $platform;
        $this->connectId = $connectId;
    }

    public function addSuccess($method, $additionalEntityId = null)
    {
        LogModel::insert([
            'status' => 'success',
            'method' => $method,
            'platform' => $this->platform,
            'connect_id' => $this->connectId,
            'additional_entity_id' => $additionalEntityId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function addError($method, $message, $additionalEntityId = null)
    {
        LogModel::insert([
            'status' => 'error',
            'method' => $method,
            'platform' => $this->platform,
            'message' => $message,
            'connect_id' => $this->connectId,
            'additional_entity_id' => $additionalEntityId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
