<?php declare(strict_types=1);

require_once './Page.php';


class Kunde extends Page
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
        $query = "SELECT * FROM `ordered_article`
                NATURAL JOIN `article` 
                NATURAL JOIN `ordering` 
                ORDER BY ordering_id, ordered_article_id ASC";
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


    protected function generateView():void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Kundeseite', '', 'Kunde.css');
        echo <<< HTML
        <nav>
        <a href="Uebersicht.php">Uebersicht</a>
        <a href="Bestellung.php">Bestellung</a>
        <a href="Kunde.php">Kunde</a>
        <a href="Baeker.php">Bäcker</a>
        <a href="Fahrer.php">Fahrer</a>
        </nav>
        <h1>Kunde</h1>
        HTML;
        echo '<script src="ajax.js"></script>';
        echo "<section id='status_section'></section>";
        echo <<< HTML
            <form action="Bestellung.php" method="POST">
                <button type="submit">Neue Bestellung</button>
            </form>
        HTML;


        $this->generatePageFooter();
    }


    protected function processReceivedData():void
    {
        parent::processReceivedData();
    }


    public static function main():void
    {
        session_start();
        try {
            $page = new Kunde();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Kunde::main();

