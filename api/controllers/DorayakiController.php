<?php

use API\DB\Database;
use API\TableGateways\UserGateway;

require_once("Controller.php");
require_once(getcwd()."/db/Database.php");
require_once(getcwd()."/tableGateways/DorayakiGateway.php");
require_once(getcwd()."/tableGateways/DorayakiActivityGateway.php");
require_once(getcwd()."/tableGateways/UserGateway.php");
require_once(getcwd()."/tableGateways/PembelianDorayakiGateway.php");
define('MB', 1048576);

class DorayakiController implements Controller {
    private $dorayakiGateway;
    private $dorayakiActivityGateway;
    private $userGateway;
    private $pembelianDorayakiGateway;
    private $dorayakiId;
    private $requestMethod;
    private $requestBody;
    private $dbConnection;

    public function __construct($dorayakiId)
    {
        $this->dorayakiId = $dorayakiId;
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->requestBody = $_POST;
        $this->dbConnection = Database::getInstance()->getDbConnection();
        $this->reqNama = $this->requestBody["nama"];
        $this->reqDeskripsi = $this->requestBody["deskripsi"];
        $this->reqHarga = $this->requestBody["harga"];
        $this->reqStok = $this->requestBody["stok"];
        $this->dorayakiGateway = new DorayakiGateway();
        $this->dorayakiActivityGateway = new DorayakiActivityGateway();
        $this->userGateway = new UserGateway();
        $this->pembelianDorayakiGateway = new PembelianDorayakiGateway();
    }

    public function processRequest()
    {
        switch($this->requestMethod){
            case "GET":
                if(isset($this->dorayakiId))
                    $this->getDorayaki();
                else 
                    if (isset($_GET["input_query"])) 
                        $this->getDorayakiByName();
                    else $this->getAllDorayaki();
                break;
            case "POST":
                if($_GET["type"] == "update") {
                    $this->updateDorayaki();
                }
                else $this->createNewDorayaki();
                break;
            case "DELETE":
                $this->deleteDorayaki();
                break;
            default:
                $response["status_code_header"] = "HTTP/1.1 404 Not Found";
                $response["body"] = ["message" => "Method not found"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
                
        }
    }

    private function getDorayaki() {
        try {
            $result = $this->dorayakiGateway->findById($this->dorayakiId);

            if(!isset($result[0])) {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "Dorayaki not found"]);
                exit();
            }

            header("HTTP/1.1 200 OK");
            echo json_encode($result[0]);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function getAllDorayaki() {
        $page_num = 1;
        try {
            if(isset($_GET["page"]) && $_GET["page"] !== null) { 
                $page_num = $_GET["page"];
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        $size = 8;
        try {
            if(isset($_GET["size"]) && $_GET["size"] !== null) { 
                $size = $_GET["size"];
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        try {
            $result = $this->dorayakiGateway->findAllDorayaki($page_num, $size);

            if(!isset($result)) {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "There's no Dorayaki"]);
                exit();
            }

            header("HTTP/1.1 200 OK");
            echo json_encode($result);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function getDorayakiByName() {
        $page_num = 1;
        try {
            if(isset($_GET["page"]) && $_GET["page"] !== null) { 
                $page_num = $_GET["page"];
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        $size = 8;
        try {
            if(isset($_GET["size"]) && $_GET["size"] !== null) { 
                $size = $_GET["size"];
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        $name = "";
        try {
            if(isset($_GET["input_query"]) && $_GET["input_query"] !== null) { 
                $name = $_GET["input_query"];
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }

        try {
            $result = $this->dorayakiGateway->findDorayakiByName($name, $page_num, $size);

            if(!isset($result)) {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => `Dorayaki with name {$name} not found`]);
                exit();
            }

            header("HTTP/1.1 200 OK");
            echo json_encode($result);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function createNewDorayaki() {
        $cleanFileName = "";
        try {
            $this->validateRequestBody();
            if(isset($_FILES["gambar"]) && $_FILES["gambar"]["size"] != 0) { 
                $this->validateImage();
    
                $fileName = $_FILES["gambar"]["name"];
                $cleanFileName = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $fileName);
                $cleanFileName = time().".".$cleanFileName;
                move_uploaded_file($_FILES["gambar"]["tmp_name"], getcwd()."/static/images/dorayakis/".$cleanFileName);
            }
            else {
                $cleanFileName = "default.jpeg";
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
        
        try {
            $this->dbConnection->beginTransaction();
            $inputDorayaki = ["nama" => $this->requestBody["nama"], 
                "deskripsi" => $this->requestBody["deskripsi"], "harga" => $this->requestBody["harga"],
                "stok" => $this->requestBody["stok"],"gambar" => "/api/static/images/dorayakis/".$cleanFileName, "terjual" => 0];

            $this->dorayakiGateway->insert($inputDorayaki);

            // get inserted dorayaki
            $insertedDorayaki = $this->dorayakiGateway->findByNamaHargaStok($inputDorayaki);
            $stateAfter = [];
            foreach($insertedDorayaki[0] as $key => $val) {
                array_push($stateAfter, $key.":".$val);
            }
            $stateAfter = implode("|", $stateAfter);

            // get current user
            $currUserDetail = $this->userGateway->find($_COOKIE["user_id"]);
            $stateUser = [];
            array_push($stateUser, "email:".$currUserDetail[0]["email"]);
            array_push($stateUser, "username:".$currUserDetail[0]["username"]);
            $stateUser = implode("|", $stateUser);

            $inputDorayakiActivites = [
                "stateUser" => $stateUser, "actionType" => "CREATE", 
                "stateBefore" => "",  "stateAfter" => $stateAfter];
            
            $this->dorayakiActivityGateway->insert($inputDorayakiActivites);
            $this->dbConnection->commit();
            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Successfully created dorayaki"]);
        } catch(\Exception $e) {
            $this->dbConnection->rollBack();
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }
    
    private function updateDorayaki() {
        if(!is_numeric($this->dorayakiId)) {
            $this->badRequestResponse("Dorayaki Id is required");
        }
        $currDorayaki = $this->dorayakiGateway->findById($this->dorayakiId);
        if(!isset($currDorayaki[0])) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["message" => "Dorayaki not found"]);
            exit();
        }
        if(!$_COOKIE["is_admin"]) {
            if(count($this->requestBody) != 1 || 
                !array_key_exists("stok", $this->requestBody) ||
                $this->requestBody["stok"] > $currDorayaki[0]["stok"]) {
            
                $this->forbiddenRequestResponse("admin only operation");
            }
        }

        $cleanFileName = null;
        try {
            if(isset($_FILES["gambar"]) && $_FILES["gambar"]["size"] != 0) { 
                $this->validateImage();
    
                $fileName = $_FILES["gambar"]["name"];
                $cleanFileName = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $fileName);
                $cleanFileName = time().".".$cleanFileName;
                move_uploaded_file($_FILES["gambar"]["tmp_name"], getcwd()."/static/images/dorayakis/".$cleanFileName);
            }
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
        $nama = $this->requestBody["nama"];
        $deskripsi = $this->requestBody["deskripsi"];
        $harga = $this->requestBody["harga"];
        $stok = $this->requestBody["stok"];
        $gambar =  $cleanFileName;
        if($stok-$currDorayaki[0]["stok"] < 0 && !$_COOKIE["is_admin"])
             $terjual = $currDorayaki[0]["stok"]-$stok;
        else $terjual = null;

        $inputPayload = ["nama" => $nama, "deskripsi" => $deskripsi, "harga" => $harga, "stok" => $stok, "gambar" => $gambar, "terjual" => $terjual];
        $updatePayload = $this->dorayakiGateway->findById($this->dorayakiId)[0];
        foreach($inputPayload as $key => $value) {
            if($key == "gambar") {
                if(isset($inputPayload[$key])) {
                    $updatePayload["gambar"]= "/api/static/images/dorayakis/".$inputPayload["gambar"];
                }
            }
            else if(isset($inputPayload[$key]) && strlen($inputPayload[$key]) != 0) {
                if($key == "terjual") $updatePayload[$key] += $value;
                else $updatePayload[$key] = $value;
            }
        }
        unset($updatePayload["created_at"]);
        unset($updatePayload["updated_at"]);

        $this->validateUpdatePayload($inputPayload);
        try {
            $this->dbConnection->beginTransaction();

            $this->dorayakiGateway->update($this->dorayakiId, $updatePayload);
            $riwayatPembelian = ["dorayakiId" => $currDorayaki[0]["id"], "dorayakiNama" => $currDorayaki[0]["nama"],
                "dorayakiHarga" => $currDorayaki[0]["harga"], "userId" => $_COOKIE["user_id"], "jumlah" => $stok-$currDorayaki[0]["stok"]];
            $this->pembelianDorayakiGateway->insert($riwayatPembelian);
            $this->dbConnection->commit();

            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Successfully updated the dorayaki"]);
        } catch(\Exception $e) {
            $this->dbConnection->rollBack();
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function deleteDorayaki() {
        if(!is_numeric($this->dorayakiId)) {
            $this->badRequestResponse("Dorayaki Id is required");
        }

        try {
            $this->dbConnection->beginTransaction();
            $result = $this->dorayakiGateway->findById($this->dorayakiId);
            if(!isset($result[0])) {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "Dorayaki not found"]);
                exit();
            }
            $stateBefore = [];
            foreach($result[0] as $key => $val) {
                array_push($stateBefore, $key.":".$val);
            }
            $stateBefore = implode("|", $stateBefore);

            // get current user
            $currUserDetail = $this->userGateway->find($_COOKIE["user_id"]);
            $stateUser = [];
            array_push($stateUser, "email:".$currUserDetail[0]["email"]);
            array_push($stateUser, "username:".$currUserDetail[0]["username"]);
            $stateUser = implode("|", $stateUser);

            $inputDorayakiActivites = ["stateUser" => $stateUser, "actionType" => "DELETE", 
                "stateBefore" => $stateBefore,  "stateAfter" => ""];

            $this->dorayakiActivityGateway->insert($inputDorayakiActivites);
            $this->dorayakiGateway->delete($this->dorayakiId);
            $this->dbConnection->commit();

            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Successfully deleted dorayaki"]);
        } catch(\Exception $e) {
            $this->dbConnection->rollBack();
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function validateRequestBody() {
        $reqNama = $this->requestBody["nama"];
        $reqHarga = $this->requestBody["harga"];
        $reqStok = $this->requestBody["stok"];
        $reqDeskripsi = $this->requestBody["deskripsi"];
        
        $errorMsg = [];
        
        if(!isset($reqNama) || strlen($reqNama) == 0) {
            array_push($errorMsg, ["nama" => "field 'nama' can't be empty"]);
        }
        if(!isset($reqHarga) || strlen($reqHarga) == 0) {
            array_push($errorMsg, ["harga" => "field 'harga' can't be empty"]);
        }
        if((int)$reqHarga < 0) {
            array_push($errorMsg, ["harga" => "harga can't be negative"]);
        }
        if(!isset($reqStok) || strlen($reqStok) == 0) {
            array_push($errorMsg, ["stok" => "field 'stok' can't be empty"]);
        }
        if((int)$reqStok < 0) {
            array_push($errorMsg, ["stok" => "stok can't be negative"]);
        }
        if(!isset($reqDeskripsi) || strlen($reqDeskripsi) == 0) {
            array_push($errorMsg, ["deskripsi" => "field 'deskripsi' can't be empty"]);
        }
        if(!empty($errorMsg)) $this->badRequestResponse($errorMsg);
    }

    private function validateUpdatePayload($payload) {
        $nama = $payload["nama"];
        $harga = $payload["harga"];
        $stok = $payload["stok"];
        $deskripsi = $payload["deskripsi"];

        $errorMsg = [];
        
        // if(!isset($nama) || strlen($nama) == 0) {
        //     array_push($errorMsg, ["nama" => "field 'nama' can't be empty"]);
        // }
        // if(!isset($harga) || strlen($harga) == 0) {
        //     array_push($errorMsg, ["harga" => "field 'harga' can't be empty"]);
        // }
        if((int)$harga < 0) {
            array_push($errorMsg, ["harga" => "harga can't be negative"]);
        }

        // if(!isset($stok) || strlen($stok) == 0) {
        //     array_push($errorMsg, ["stok" => "field 'stok' can't be empty"]);
        // }
        if((int)$stok< 0) {
            array_push($errorMsg, ["stok" => "stok can't be negative"]);
        }
        // if(!isset($deskripsi) || strlen($deskripsi) == 0) {
        //     array_push($errorMsg, ["deskripsi" => "field 'deskripsi' can't be empty"]);
        // }
        if(!empty($errorMsg)) $this->badRequestResponse($errorMsg);
    }

    private function validateImage() {
        // upload image failed
        if($_FILES["gambar"]["error"] != UPLOAD_ERR_OK) {
            $this->badRequestResponse("Upload failed with error code " . $_FILES["gambar"]['error']);
        }

        // check if file is actual image
        $info = getimagesize($_FILES["gambar"]["tmp_name"]);
        if($info === FALSE) {
            $this->badRequestResponse("Unable to determine image type of uploaded file");
        }

        if(($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
            $this->badRequestResponse("Not a gif/jpeg/png");
        }
    }

    private function badRequestResponse($message) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["message" => $message]);
        exit();
    }

    private function forbiddenRequestResponse($message) {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(["message" => $message]);
        exit();
    }
}