<?php
namespace Api\Model;

class Games
{
    public $entityId;
    public $startTime;
    public $endTime;
    public $winnerId;
    public $log;

    public function exchangeArray($data)
    {
        $this->entityId = (isset($data['entity_id'])) ? $data['entity_id'] : null;
        $this->startTime = (isset($data['start_time'])) ? $data['start_time'] : null;
        $this->endTime = (isset($data['end_time'])) ? $data['end_time'] : null;
        $this->winnerId = (isset($data['winner_id'])) ? $data['winner_id'] : null;
        $this->log = (isset($data['log'])) ? $data['log'] : null;
    }
}