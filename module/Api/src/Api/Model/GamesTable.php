<?php
namespace Api\Model;

use Api\Model\RatingElo;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Exception\ErrorException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class GamesTable extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table ='games';

    /**
     * GamesTable constructor.
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Games());

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
    public function getGame($id)
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
     * @param Games $games
     * @throws ErrorException
     */
    public function saveGames(Games $games)
    {
        $data = array(
            'entity_id' => $games->entityId,
            'start_time' => $games->startTime,
            'end_time' => $games->endTime,
            'winner_id' => $games->winnerId,
            'log' => $games->log
        );
        $id = (int) $games->entityId;
        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getGame($id)) {
            $this->update(
                $data,
                array(
                    'entity_id' => $id,
                )
            );
        } else {
            throw new Exception('Game id does not exist');
        }
        return $this->getLastInsertValue();
    }

    /**
     * @param $id
     */
    public function deleteGame($id)
    {
        $this->delete(array(
            'entity_id' => $id,
        ));
    }

    /**
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getPlayerGamesList($id)
    {
        //SELECT games.* from games inner join (select game_id from participants where player_id = 99) participants ON games.entity_id = participants.game_id
        $select = new Select;
        $select->from($this->getTable())->columns(array('*'))->join('participants','participants.game_id = games.entity_id', array(), 'left')->where(array('player_id = ?' => $id));
        $result = $this->selectWith($select);
        return $result;
    }

    public function getGameBetweenTimeRange($timeRange = array())
    {
        $select = new Select;
        $select->from($this->getTable())->columns(array('*'))->where->between('start_time', $timeRange['from'], $timeRange['to']);
        $result = $this->selectWith($select);
        return $result;
    }
}