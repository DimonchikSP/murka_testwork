<?php
namespace Api\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Exception\ErrorException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class ParticipantsTable extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table ='participants';

    /**
     * GamesTable constructor.
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Participants());

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
     * @param $gameId
     * @return array|\ArrayObject|null
     * @throws ErrorException
     */
    public function getGameParticipants($gameId)
    {
        $gameId  = (int) $gameId;

        $rowset = $this->select(array(
            'game_id' => $gameId,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new ErrorException("Could not find row $gameId");
        }

        return $row;
    }

    /**
     * @param $id
     * @param Participants $gameParticipants
     * @param array $players
     */
    public function saveGameParticipants(Participants $gameParticipants)
    {
        $data = array(
            'game_id' => $gameParticipants->gameId,
            'player_id' => $gameParticipants->playerId,
        );
        if (true) {
            foreach ($data['player_id'] as $player) {
                $this->insert(array('game_id' => $data['game_id'], 'player_id' => $player));
            }
        } else {
            throw new Exception('Form id does not exist');
        }
    }

    /**
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getPlayerGames($id)
    {
        $playerId  = (int) $id;
        $select = new Select;
        $select->from($this->getTable())->columns(array('game_id'))->where(array('player_id = ?' => $playerId));
        $result = $this->selectWith($select);
        return $result;
    }
}
