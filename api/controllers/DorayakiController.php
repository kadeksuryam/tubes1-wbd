<?php

use API\DB\Database;

require_once("Controller.php");
require_once(getcwd()."/db/Database.php");
require_once(getcwd()."/tableGateways/DorayakiGateway.php");
require_once(getcwd()."/tableGateways/DorayakiActivityGateway.php");
define('MB', 1048576);

class DorayakiController implements Controller {
    private $dorayakiGateway;
    private $dorayakiActivityGateway;
    private $dorayakiId;
    private $requestMethod;
    private $requestBody;
    private $dbConnection;
    private $reqNama;
    private $reqDeskripsi;
    private $reqHarga;
    private $reqStok;

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
    }

    public function processRequest()
    {
        switch($this->requestMethod){
            case "POST":
                $this->createNewDorayaki();
                break;
            default:
                $response["status_code_header"] = "HTTP/1.1 404 Not Found";
                $response["body"] = ["message" => "Method not found"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
                
        }
    }

    private function createNewDorayaki() {
        $cleanFileName = "";
        try {
            $this->validateRequestBody();
            if(!empty($_FILES["gambar"])) { 
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
            $inputDorayaki = ["nama" => $this->reqNama, 
                "deskripsi" => $this->reqDeskripsi, "harga" => $this->reqHarga, "stok" => $this->reqStok,
                "gambar" => "/api/static/images/dorayakis/".$cleanFileName];
            
            if(!isset($inputDorayaki["deskripsi"])) $inputDorayaki["deskripsi"] = "";

            $this->dorayakiGateway->insert($inputDorayaki);
            $result = $this->dorayakiGateway->findByNamaHargaStok($inputDorayaki);
            
            $stateAfter = [];
            foreach($result[0] as $key => $val) {
                array_push($stateAfter, $key.":".$val);
            }
            $stateAfter = implode("|", $stateAfter);
            $inputDorayakiActivites = ["dorayakiId" => $result[0]["id"], 
                "userId" => $_COOKIE["user_id"], "actionType" => "CREATE", 
                "stateBefore" => "",  "stateAfter" => $stateAfter];
            
            $this->dorayakiActivityGateway->insert($inputDorayakiActivites);

            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Successfully created dorayaki"]);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }
    

    private function validateRequestBody() {
        $reqNama = $this->requestBody["nama"];
        $reqHarga = $this->requestBody["harga"];
        $reqStok = $this->requestBody["stok"];
        
        $errorMsg = [];

        if(!isset($reqNama)) {
            array_push($errorMsg, ["nama" => "field 'nama' can't be empty"]);
        }
        if(!isset($reqHarga)) {
            array_push($errorMsg, ["harga" => "field 'harga' can't be empty"]);
        }
        if(!isset($reqStok)) {
            array_push($errorMsg, ["stok" => "field 'stok' can't be empty"]);
        }

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

        // if ($_FILES["gambar"]["size"] > MB) {
        //     $this->badRequestResponse("File is too large. Max upload file is 5 MB");
        // }
    }

    private function badRequestResponse($message) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["message" => $message]);
        exit();
    }
}