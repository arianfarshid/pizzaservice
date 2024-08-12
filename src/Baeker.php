<?php declare(strict_types=1);

require_once './Page.php';

class Baeker extends Page
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
        $sql = "SELECT ordered_article_id, name, status, ordering_id
                FROM article 
                NATURAL JOIN ordered_article 
                NATURAL JOIN ordering 
                WHERE status <= 2 
                ORDER BY ordering_id, ordered_article_id ASC";

        $recordSet = $this->_database->query($sql);

        $bestellungArray = [];

        while ($record = $recordSet->fetch_assoc()) {
            $bestellungArray[] = [
                "ordered_article_id" => $record["ordered_article_id"],
                "name" => $record["name"],
                "status" => $record["status"],
                "ordering_id" => $record["ordering_id"]
            ];
        }

        $recordSet->free();

        return $bestellungArray;

    }

    protected function generateView():void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Bäckerseite', '', 'Baecker.css');


        echo '<meta http-equiv="refresh" content="10">';

        echo <<< HTML
        <nav>
        <a href="Uebersicht.php">Uebersicht</a>
        <a href="Bestellung.php">Bestellung</a>
        <a href="Kunde.php">Kunde</a>
        <a href="Baeker.php">Bäcker</a>
        <a href="Fahrer.php">Fahrer</a>
        </nav>
         <h1>Bäcker</h1>

        HTML;

        echo "<section id='content'>";

        if (empty($data)) {
            echo '<p>Keine Bestellungen vorhanden.</p>';
        }
        $current_ordering_id = NULL;
        for ($i = 0; $i < count($data); $i++) {
            if ($current_ordering_id != $data[$i]['ordering_id']) {
                $current_ordering_id = $data[$i]['ordering_id'];
                echo <<< HTML
                <h2 id="bestellungsNr">Bestellung: {$data[$i]['ordering_id']}</h2>        
                HTML;
            }
            $status = $data[$i]['status'];
            $isBestellt = ($status == 0) ? 'checked' : '';
            $isImOffen = ($status == 1) ? 'checked' : '';
            $isFertig = ($status == 2) ? 'checked' : '';
            echo <<< HTML
            <article id="pizza">
            <form id="pizza_status_form_{$data[$i]['ordered_article_id']}" action="Baeker.php" method="post">
                <p>{$data[$i]['name']}</p>
                <input type="radio" id="bestellt_{$data[$i]['ordered_article_id']}" name="order_status_{$data[$i]['ordered_article_id']}" onclick="document.forms['pizza_status_form_{$data[$i]['ordered_article_id']}'].submit();" value="bestellt" {$isBestellt}>
                <label for="bestellt_{$data[$i]['ordered_article_id']}">bestellt</label>
                <input type="radio" id="im_offen_{$data[$i]['ordered_article_id']}" name="order_status_{$data[$i]['ordered_article_id']}" onclick="document.forms['pizza_status_form_{$data[$i]['ordered_article_id']}'].submit();" value="im_offen" {$isImOffen}>
                <label for="im_offen_{$data[$i]['ordered_article_id']}">im Ofen</label>
                <input type="radio" id="fertig_{$data[$i]['ordered_article_id']}" name="order_status_{$data[$i]['ordered_article_id']}" onclick="document.forms['pizza_status_form_{$data[$i]['ordered_article_id']}'].submit();" value="fertig" {$isFertig}>                    
                <label for="fertig_{$data[$i]['ordered_article_id']}">fertig</label>
                <input type="hidden" name="ordering_id" value="{$data[$i]['ordering_id']}">
                <input type="hidden" name="ordered_article_id" value="{$data[$i]['ordered_article_id']}">
            </form>
            </article>
            HTML;
        }

        echo "</section>\n";

        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if (isset($_POST['ordering_id']) && isset($_POST['ordered_article_id']) && isset($_POST['order_status_' . $_POST['ordered_article_id']])) {
            $ordering_id = $_POST['ordering_id'];
            $ordered_article_id = $_POST['ordered_article_id'];
            $status = $_POST['order_status_' . $ordered_article_id];
            $status = ($status == 'bestellt') ? 0 : (($status == 'im_offen') ? 1 : 2);
            $query = "UPDATE `ordered_article` SET `status` = $status WHERE `ordered_article`.`ordered_article_id` = $ordered_article_id";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: Baeker.php", true, 303);
            die();
        }

    }

    public static function main():void
    {
        session_start();
        try {
            $page = new Baeker();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Baeker::main();
