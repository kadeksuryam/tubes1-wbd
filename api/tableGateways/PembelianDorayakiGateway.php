<?php
use API\DB\Database;
require_once(getcwd()."/db/Database.php");

class PembelianDorayakiGateway {
    private $dbConnection;

    function __construct()
    {
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function findByUserId($userId) {
        $stmt = <<<EOS
            SELECT * FROM pembelian_dorayakis
            WHERE user_id = :userId ORDER BY updated_at ASC
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "userId" => $userId
        ));

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function findAll() {
        $stmt = <<<EOS
            SELECT * FROM pembelian_dorayakis ORDER BY updated_at ASC
        EOS;
        $stmt = $this->dbConnection->query($stmt);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO pembelian_dorayakis
                (dorayaki_id, dorayaki_nama, dorayaki_harga, user_id, username, jumlah) 
            VALUES 
                (:dorayakiId, :dorayakiNama, :dorayakiHarga, :userId, :username, :jumlah)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "dorayakiId" => $input["dorayakiId"],
            "dorayakiNama" => $input["dorayakiNama"],
            "dorayakiHarga" => $input["dorayakiHarga"],
            "userId" => $input["userId"],
            "username" => $input["username"],
            "jumlah" => $input["jumlah"],
        ));
        return $stmt->rowCount();
    }
}