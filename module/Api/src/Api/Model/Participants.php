<?php
namespace Api\Model;

class Participants
{
    public $entityId;
    public $gameId;
    public $playerId = array();

    public function exchangeArray($data)
    {
        $this->entityId = (isset($data['entity_id'])) ? $data['entity_id'] : null;
        $this->gameId = (isset($data['game_id'])) ? $data['game_id'] : null;
        $this->playerId = (isset($data['player_id'])) ? $data['player_id'] : null;
    }
}