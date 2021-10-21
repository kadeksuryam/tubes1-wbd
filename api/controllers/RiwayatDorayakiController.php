<?php
require_once(getcwd()."/tableGateways/PembelianDorayakiGateway.php");

class RiwayatDorayakiController implements Controller {
    private $requestMethod;
    private $userId;
    private $pembelianDorayakiGateway;

    public function __construct()
    {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->pembelianDorayakiGateway = new PembelianDorayakiGateway();
        $this->userId = $_GET["user_id"];
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case "GET":
                if(isset($this->userId))
                    $this->getRiwayatDorayakiByUserId();
                else $this->getAllRiwayatDorayaki();
                break; 
            default:
                $response["status_code_header"] = "HTTP/1.1 404 Not Found";
                $response["body"] = ["message" => "Method not found"];
                header($response["status_code_header"]);
                echo json_encode($response["body"]);
                exit();
        }
    }

    private function getRiwayatDorayakiByUserId() {
        try {
            $result = $this->pembelianDorayakiGateway->findByUserId($this->userId);
            header("HTTP/1.1 200 OK");
            echo json_encode($result);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }

    private function getAllRiwayatDorayaki() {
        try {
            $result = $this->pembelianDorayakiGateway->findAll();
            header("HTTP/1.1 200 OK");
            echo json_encode($result);
        } catch(\Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => $e->getMessage()]);
            exit();
        }
    }
}