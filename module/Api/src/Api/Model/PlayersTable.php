<?php
namespace Api\Model;

use Api\Model\RatingElo;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Exception\ErrorException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class PlayersTable extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table ='players';

    /**
     * GamesTable constructor.
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Players());

        $this->initialize();
    }

    /**
     * @return ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws ErrorException
     */
    public function getPlayer($id)
    {
        $id  = (int) $id;

        $rowset = $this->select(array(
            'entity_id' => $id,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new ErrorException("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param Players $player
     * @throws ErrorException
     */
    public function savePlayer(Players $player)
    {
        $data = array(
            'entity_id' => $player->entityId,
            'player_elo' => $player->playerElo,
        );

        $id = (int) $player->entityId;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getPlayer($id)) {
            $this->update(
                $data,
                array(
                    'id' => $id,
                )
            );
        } else {
            throw new Exception('Form id does not exist');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPlayerElo($id)
    {
        $playerId = (int) $id;
        $select = new Select;
        $select->from($this->getTable())->columns(array('player_elo'))->where(array('entity_id = ?' => $playerId));
        $result = $this->selectWith($select);
        return $result->current();
    }

    public function saveRaitingElo($winner, $data)
    {
        $participants = $data->playerId;
        $oldWinnerElo = $this->getPlayer($winner);
        $eloSum = null;
        if (($key = array_search($winner, $participants)) !== false) {
            unset($participants[$key]);
        }
        foreach ($participants as $participant) {

            if ($winner != $participant) {
                $oldLooserElo = $this->getPlayer($participant);
                $rating = new RatingElo($oldLooserElo->playerElo, $oldWinnerElo->playerElo, RatingElo::LOST, RatingElo::WIN);
                $result = $rating->getNewRatings();

                $eloSum += $result['b'] - $oldWinnerElo->playerElo;
                $data = array(
                    'player_elo' => $result['a']
                );

                $id = (int) $participant;

                if ($id == 0) {
                    $this->insert($data);
                } elseif ($this->getPlayer($participant)) {
                    $this->update(
                        $data,
                        array(
                            'entity_id' => $participant,
                        )
                    );
                } else {
                    throw new Exception('Form id does not exist');
                }
            }
        }

        $newWinnerElo = ($eloSum / count($participants)) + $oldWinnerElo->playerElo;


        $data = array(
            'player_elo' => $newWinnerElo
        );

        $id = (int) $winner;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getPlayer($winner)) {
            $this->update(
                $data,
                array(
                    'entity_id' => $winner,
                )
            );
        } else {
            throw new Exception('Form id does not exist');
        }
    }
}
