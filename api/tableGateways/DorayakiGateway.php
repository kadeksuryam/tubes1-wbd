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
                (nama, deskripsi, harga, stok, terjual, gambar) 
            VALUES 
                (:nama, :deskripsi, :harga, :stok, :terjual, :gambar)
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "nama" => $input["nama"],
            "deskripsi" => $input["deskripsi"],
            "harga" => $input["harga"],
            "stok" => $input["stok"],
            "terjual" => $input["terjual"],
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
                terjual = :terjual,
                gambar = :gambar
            WHERE id = :id;
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        
        $stmt->execute($input);
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

    public function findAllDorayaki($page, $size)
    {
        
        $stmt_count_all = <<<EOS
            SELECT * FROM dorayakis
        EOS;
    
        $stmt_count_all = $this->dbConnection->query($stmt_count_all);
        $count_all = count($stmt_count_all->fetchAll(\PDO::FETCH_ASSOC));
        $num_page = ceil($count_all/$size);
        $start = $size * ($page - 1);

        $stmt = <<<EOS
            SELECT * FROM dorayakis ORDER BY terjual DESC LIMIT :size OFFSET :start
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "size" => $size,
            "start" => $start
        ));
        $allDorayakiPage = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $dorayakiPerPage = count($allDorayakiPage);
        $retPage = [$num_page, $dorayakiPerPage];
        $result = ["page" => $retPage, "payload" => $allDorayakiPage];
        return $result;
    }

    public function findDorayakiByName($query_input, $page, $size)
    {
        $query_name = "%{$query_input}%";

        $stmt_count_all = <<<EOS
            SELECT * FROM dorayakis
        EOS;
    
        $stmt_count_all = $this->dbConnection->query($stmt_count_all);
        $count_all = count($stmt_count_all->fetchAll(\PDO::FETCH_ASSOC));
        $num_page = ceil($count_all/$size);
        $start = $size * ($page - 1);

        $stmt = <<<EOS
            SELECT * FROM dorayakis WHERE LOWER(nama) LIKE LOWER(:query_name) ORDER BY terjual DESC LIMIT :size OFFSET :start
        EOS;

        $stmt = $this->dbConnection->prepare($stmt);
        $stmt->execute(array(
            "query_name" => $query_name,
            "size" => $size,
            "start" => $start
        ));
        $allDorayakiPage = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $dorayakiPerPage = count($allDorayakiPage);
        $retPage = [$num_page, $dorayakiPerPage];
        $result = ["page" => $retPage, "payload" => $allDorayakiPage];
        return $result;
    }
}