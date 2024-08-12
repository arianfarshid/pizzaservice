<?php declare(strict_types=1);

require_once './Page.php';


class Fahrer extends Page
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

        $sql = "SELECT ordered_article_id,ordering_id, address, status, name, price
                FROM `ordered_article`
                NATURAL JOIN `article` 
                NATURAL JOIN `ordering` 
                WHERE ordered_article.status >=2
                ORDER BY ordering_id, ordered_article_id ASC" ;

        $recordset = $this->_database->query($sql);


        $result = array();
        while ($record = $recordset->fetch_assoc()) {
            $result[] = [
                "ordered_article_id" => $record["ordered_article_id"],
                "ordering_id" => $record["ordering_id"],
                "address" => $record["address"],
                "status" => $record["status"],
                "name" => $record["name"],
                "price" => $record["price"]
            ];
        }

        $recordset->free();
        return $result;
    }

    protected function generateView():void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Fahrerseite', '', 'Fahrer.css');

        echo '<meta http-equiv="refresh" content="10">';

        echo <<<HTML
        <body>
            <nav>
                <a href="Uebersicht.php">Uebersicht</a>
                <a href="Bestellung.php">Bestellung</a>
                <a href="Kunde.php">Kunde</a>
                <a href="Baeker.php">Bäcker</a>
                <a href="Fahrer.php">Fahrer</a>
            </nav>
            <h1>Fahrer</h1>
        HTML;

        echo "<section id='content'>";

        $current_order_id = NULL;
        $totalPrice = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($current_order_id != $data[$i]['ordering_id']) {
                if ($current_order_id !== NULL) {
                    echo "</form><p>Gesamtpreis: {$totalPrice} €</p>";
                }
                $current_order_id = $data[$i]['ordering_id'];
                $totalPrice = 0;
                echo <<< HTML
                <h2 id="bestellungsNr">Bestellung: {$data[$i]['ordering_id']}</h2>
                <h3>Ihre Adresse: {$data[$i]['address']}</h3>
                HTML;
            }
            $totalPrice += $data[$i]['price'];
            $status = $data[$i]['status'];
            $isFertig = ($status == 2) ? 'checked' : '';
            $isUnterwegs = ($status == 3) ? 'checked' : '';
            $isGeliefert = ($status == 4) ? 'checked' : '';

            echo <<< HTML
            <form id="lieferung_{$data[$i]['ordered_article_id']}" action="Fahrer.php" method="post">
                <p>{$data[$i]['name']} (Preis: {$data[$i]['price']} €)</p>               
                <input type="radio" name="status" onclick="document.forms['lieferung_{$data[$i]['ordered_article_id']}'].submit();" value="fertig" {$isFertig}>
                <label for="html">fertig</label>
                <input type="radio" name="status" onclick="document.forms['lieferung_{$data[$i]['ordered_article_id']}'].submit();" value="unterwegs" {$isUnterwegs}>
                <label for="html">unterwegs</label>
                <input type="radio" name="status" onclick="document.forms['lieferung_{$data[$i]['ordered_article_id']}'].submit();" value="geliefert" {$isGeliefert}>
                <label for="html">geliefert</label>
                <input type="hidden" name="ordering_id" value="{$data[$i]['ordering_id']}">
                <input type="hidden" name="ordered_article_id" value="{$data[$i]['ordered_article_id']}">
            </form>   
            
            HTML;
        }
        if ($current_order_id !== NULL) {
            echo "<p>Gesamtpreis: {$totalPrice} €</p>";
        }

        echo "</section>";
        $this->generatePageFooter();
    }


    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if (isset($_POST['ordering_id']) && isset($_POST['status']) && isset($_POST['ordered_article_id'])) {
            $status = $_POST['status'];
            $status = ($status == 'fertig') ? 2 : (($status == 'unterwegs') ? 3 : 4);
            $ordering_id = $_POST['ordering_id'];
            $ordered_article_id = $_POST['ordered_article_id'];
            $query = "UPDATE `ordered_article` SET `status` = '$status' WHERE `ordered_article`.`ordered_article_id` = $ordered_article_id";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: fahrer.php", true, 303);
            die();
        }
    }


    public static function main():void
    {
        session_start();
        try {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Fahrer::main();

