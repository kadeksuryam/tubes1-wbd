<?php
use API\DB\Database;
require_once(getcwd()."/db/Database.php");

class PembelianDorayakiGateway {
    private $dbConnection;

    function __construct()
    {
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO pembelian_dorayakis
                (dorayaki_id, dorayaki_nama, dorayaki_harga, user_id, jumlah) 
            VALUES 
                (:dorayakiId, :dorayakiNama, :dorayakiHarga, :userId, :jumlah)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "dorayakiId" => $input["dorayakiId"],
            "dorayakiNama" => $input["dorayakiNama"],
            "dorayakiHarga" => $input["dorayakiHarga"],
            "userId" => $input["userId"],
            "jumlah" => $input["jumlah"],
        ));
        return $stmt->rowCount();
    }
}