<?php

use API\DB\Database;
require_once(getcwd()."/db/Database.php");

class DorayakiGateway {
    private $dbConnection;

    function __construct()
    {
        $this->dbConnection = Database::getInstance()->getDbConnection();
    }

    public function findByNamaHargaStok(Array $input)
    {
        $stmt = <<<EOS
            SELECT * FROM dorayakis WHERE
            nama = :nama AND
            harga = :harga AND
            stok = :stok
        EOS;
        
        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "nama" => $input["nama"],
            "harga" => $input["harga"],
            "stok" => $input["stok"],
        ));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function findById($id)
    {
        $stmt = <<<EOS
            SELECT * FROM dorayakis WHERE
            id = :id
        EOS;
        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "id" => $id
        ));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function insert(Array $input)
    {
        $stmt = <<<EOS
            INSERT INTO dorayakis 
                (nama, deskripsi, harga, stok, gambar) 
            VALUES 
                (:nama, :deskripsi, :harga, :stok, :gambar)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "nama" => $input["nama"],
            "deskripsi" => $input["deskripsi"],
            "harga" => $input["harga"],
            "stok" => $input["stok"],
            "gambar" => $input["gambar"],
        ));
        return $stmt->rowCount();
    }

    public function update($id, Array $input)
    {
        $stmt = <<<EOS
            UPDATE dorayakis
            SET
                nama = :nama,
                deskripsi = :deskripsi,
                harga = :harga,
                stok = :stok,
                gambar = :gambar,
            WHERE id = :id;
        EOS;

        if(!isset($input["gambar"])) $input["gambar"] = "/static/images/dorayakis/default.jpeg";
        if(!isset($input["stok"])) $input["harga"] = 0;
        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "id" => (int)$id,
            "nama" => $input["nama"],
            "deskripsi" => $input["deskripsi"],
            "stok" => $input["stok"],
            "gambar" => $input["gambar"],
        ));
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $stmt = <<<EOS
            DELETE FROM dorayakis
            WHERE id = :id;
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array("id" => (int)$id));
        
        return $stmt->rowCount();
        
    }
}