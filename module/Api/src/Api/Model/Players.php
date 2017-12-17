<?php
namespace Api\Model;

class Players
{
    public $entityId;
    public $playerElo;

    public function exchangeArray($data)
    {
        $this->entityId = (isset($data['entity_id'])) ? $data['entity_id'] : null;
        $this->playerElo = (isset($data['player_elo'])) ? $data['player_elo'] : null;
    }
}