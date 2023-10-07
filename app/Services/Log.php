<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Log as LogModel;

class Log
{
    public $platform;
    public $integrationId;

    public function __construct($platform, $integrationId)
    {
        $this->platform = $platform;
        $this->integrationId = $integrationId;
    }

    public function addSuccess($method, $additionalEntityId = null)
    {
        LogModel::insert([
            'status' => 'success',
            'method' => $method,
            'platform' => $this->platform,
            'integration_id' => $this->integrationId,
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
            'integration_id' => $this->integrationId,
            'additional_entity_id' => $additionalEntityId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
