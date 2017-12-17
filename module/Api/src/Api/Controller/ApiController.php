<?php

namespace Api\Controller;

use Api\Model\Games;
use Api\Model\Participants;
use Zend\Mvc\Controller\AbstractRestfulController;

/**
 *
 */
class ApiController extends AbstractRestfulController
{
    protected $gamesTable;
    protected $playersTable;
    protected $participantsTable;

    /**
     * @return array|object
     */
    public function getGamesTable()
    {
        if (!$this->gamesTable) {
            $sm = $this->getServiceLocator();
            $this->gamesTable = $sm->get('Api\Model\GamesTable');
        }
        return $this->gamesTable;
    }

    /**
     * @return array|object
     */
    public function getPlayersTable()
    {
        if (!$this->playersTable) {
            $sm = $this->getServiceLocator();
            $this->playersTable = $sm->get('Api\Model\PLayersTable');
        }
        return $this->playersTable;
    }

    /**
     * @return array|object
     */
    public function getParticipantsTable()
    {
        if (!$this->participantsTable) {
            $sm = $this->getServiceLocator();
            $this->participantsTable = $sm->get('Api\Model\ParticipantsTable');
        }
        return $this->participantsTable;
    }

	/**
	 * Return list of resources
	 *
	 * @return array
	 */
	public function getList()
	{
	    if (!empty($this->params()->fromQuery('player_games'))) {
	        $playerId = $this->params()->fromQuery('player_games');
	        $gamesList = $this->getGamesTable()->getPlayerGamesList($playerId);
	        return $gamesList;
        }
        if (!empty($this->params()->fromQuery('player_elo'))) {
	        $playerId = $this->params()->fromQuery('player_elo');
	        $playerElo = $this->getPlayersTable()->getPlayerElo($playerId);
	        return $playerElo;
        }
        if (!empty($this->params()->fromQuery('game_between_time'))) {
	        $timeRange = $this->params()->fromQuery('game_between_time');
	        $gamesBetweenTimeRange = $this->getGamesTable()->getGameBetweenTimeRange($timeRange);
	        return $gamesBetweenTimeRange;
        }
        if (empty($this->params()->fromQuery())) {
	        return $this->getGamesTable()->fetchAll();
        }
	}

	/**
	 * Return single resource
	 *
	 * @param mixed $id
	 * @return mixed
	 */
	public function get($id)
    {
        if ($id != null) {
            $game = $this->getGamesTable()->getGame($id);
            return $game;
        }
    }

	/**
	 * Create a new resource
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public function create($data)
    {
        try {
            $data = new Games();
            $data->exchangeArray($this->params()->fromQuery());
            $id = $this->getGamesTable()->saveGames($data);
            if ($id != 0) {
                try {
                    $participants = $this->params()->fromQuery('participants');
                    $winner = $this->params()->fromQuery('winner_id');
                    $data = new Participants();
                    $data->exchangeArray(array('game_id' => $id, 'player_id' => $participants));
                    $this->getParticipantsTable()->saveGameParticipants($data);
                    $this->getPlayersTable()->saveRaitingElo($winner, $data);

                } catch (\Exception $exception) {
                    return $exception;
                }
            }
        } catch (\Exception $exception) {
            return $exception;
        }
    }

	/**
	 * Update an existing resource
	 *
	 * @param mixed $id
	 * @param mixed $data
	 * @return mixed
	 */
	public function update($id, $data)
    {
        try {
            $data = new Games();
            $data->entityId = $id;
            $data->startTime = $this->params()->fromQuery('start_time');
            $data->endTime = $this->params()->fromQuery('end_time');
            $data->winnerId = $this->params()->fromQuery('winner_id');
            $data->log = $this->params()->fromQuery('log');
            $this->getGamesTable()->saveGames($data);
        } catch (\Exception $exception) {
            return $exception;
        }

    }

	/**
	 * Delete an existing resource
	 *
	 * @param  mixed $id
	 * @return mixed
	 */
	public function delete($id)
    {
        $this->getGamesTable()->deleteGame($id);
    }
}
