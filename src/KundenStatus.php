<?php declare(strict_types=1);

require_once './Page.php';


class KundenStatus extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }


    protected function getViewData():array
    {

        $pizza = array();
        if(isset($_SESSION["ordering_id"])) {
            $current_ordering_id = (string)$_SESSION["ordering_id"];

            $query = "SELECT * FROM `ordered_article`
                    NATURAL JOIN `article` 
                    NATURAL JOIN `ordering` 
                    WHERE `ordering_id` = $current_ordering_id
                    ORDER BY ordered_article_id ASC";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            while ($record = $recordset->fetch_assoc()) {
                $pizza[] = [
                    "ordered_article_id" => $record["ordered_article_id"],
                    "name" => $record["name"],
                    "status" => $record["status"],
                    "ordering_id" => $record["ordering_id"],
                    "address" => $record["address"]
                ];
            }
            $recordset->free();
            return $pizza;
        }
        return array();
    }


    protected function generateView():void
    {
        header("Content-Type: application/json; charset=UTF-8");


        try {
            $data = $this->getViewData();
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }

    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
    }


    public static function main():void
    {
        session_start();
        try {
            $page = new KundenStatus();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


KundenStatus::main();
