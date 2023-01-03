<?php
class PHPSessions implements SessionHandlerInterface
{
    private $savePath;

    /**
     * a database MySQLi connection resource
     * @var PDO
     */
    protected $dbConnection;

    /**
     * the name of the DB table which handles the sessions
     * @var string
     */
    protected $dbTable;

    private $dbHost;
    private $dbPort;
    private $dbUser;
    private $dbPassword;
    private $dbDatabase;

    /**
     * Set db data if no connection is being injected
     * @param   string  $dbHost
     * @param   string  $dbPort
     * @param   string  $dbUser
     * @param   string  $dbPassword
     * @param   string  $dbDatabase
     * @param   string|null  $dbTable
     */
    public function setDbDetails($dbHost, $dbPort, $dbUser, $dbPassword, $dbDatabase, $dbTable = null)
    {
//        $this->dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);
        $this->dbHost = $dbHost;
        $this->dbPort = $dbPort;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbDatabase = $dbDatabase;

        if(isset($dbTable)){
            $this->dbTable = $dbTable;
        }
    }

    /**
     * Inject DB connection from outside
     * @param   object  $dbConnection   expects MySQLi object
     */
    public function setDbConnection($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Inject DB connection from outside
     * @param   object  $dbConnection   expects MySQLi object
     */
    public function setDbTable($dbTable)
    {
        $this->dbTable = $dbTable;
    }

    #[\ReturnTypeWillChange]
    public function open($path, $name)
    {
        $this->dbConnection = new PDO(
            "mysql:dbname={$this->dbDatabase};host=$this->dbHost;port={$this->dbPort};",
            $this->dbUser,
            $this->dbPassword,
            [PDO::ATTR_PERSISTENT => false]
        );


        $limit = time() - (3600 * 24);
        $sql = "DELETE FROM {$this->dbTable} WHERE timestamp < :ts";
        $params = [ 'ts' => $limit ];
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute($params);

        return true;
    }

    #[\ReturnTypeWillChange]
    public function close()
    {
        $this->dbConnection = null;
        return true;
    }

    #[\ReturnTypeWillChange]
    public function read($id)
    {
        $sql = "SELECT data FROM {$this->dbTable} WHERE id = :id";
        $params = [ 'id' => $id ];

        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute($params);

        if ($result = $stmt->fetch( PDO::FETCH_ASSOC)) {
            return $result['data'];
        } else {
            return '';
        }
    }

    #[\ReturnTypeWillChange]
    public function write($id, $data)
    {
        $sql = "REPLACE INTO {$this->dbTable} (`id`, `data`, `timestamp`) VALUES(:id, :data, :timestamp)";
        $params = [ 'id' => $id, 'data' => $data,  'timestamp' => time() ];
        $stmt = $this->dbConnection->prepare($sql);
        return $stmt->execute($params);
    }

    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        $sql = "DELETE FROM $this->dbTable WHERE id = :id";
        $params = [ 'id' => $id ];
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        $sql = "DELETE FROM $this->dbTable WHERE timestamp < :ts";
        $params = [ 'ts' => time() - intval($maxlifetime) ];
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute($params);
        return true;
    }
}