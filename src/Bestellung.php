<?php declare(strict_types=1);

require_once './Page.php';


class Bestellung extends Page
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
        $sql = "SELECT* FROM article";
        $recordSet = $this->_database->query($sql);
        if(!$recordSet) {
            throw new Exception("keine Article in der Datenbank");
        }
        $article_List = array();

        while ($record = $recordSet->fetch_assoc()) {
            $article_id = $record["article_id"];
            $name = $record["name"];
            $picture = $record["picture"];
            $price = $record["price"];
            $article_List[] = array(
                "article_id" => $article_id,
                "name" => $name,
                "picture" => $picture,
                "price" => $price
            );
        }

        $recordSet->free();
        return $article_List;
    }

    protected function generateView():void
    {
        $articleList = $this->getViewData();
        $this->generatePageHeader('Bestellungseite','', 'Bestellung.css');

        echo <<< HTML
        <nav>
        <a href="Uebersicht.php">Übersicht</a>
        <a href="Bestellung.php">Bestellung</a>
        <a href="Kunde.php">Kunde</a>
        <a href="Baeker.php">Bäcker</a>
        <a href="Fahrer.php">Fahrer</a>
        </nav>
                
        <h1>Bestellung</h1>
        
        HTML;

        echo "<section id='inhalt'>";
        echo "<section id='speisekarteBereich'>";
        echo "<h2>Speisekarte</h2>";
        $image = "";
        foreach ($articleList as $article) {
            if($article['article_id'] === "1") {
                $image = "img/salami.jpg";
            } else if($article['article_id'] === "2") {
                $image = "img/vegetaria.jpg";
            } else if($article['article_id'] === "3") {
                $image = "img/spinach.jpg";
            }
            echo <<<HTML
            <article>
                <img
                    id="article_{$article['article_id']}"
                    class="article_image"
                    width="150"
                    height="100"
                    src="{$image}" 
                    alt="" 
                    title="$article[name]"
                    data-article-id="{$article['article_id']}"
                    data-article-name="{$article['name']}"
                    data-article-price="{$article['price']}"
                    onclick="addPizza.call(this)"
                >
                <section id="nameAndPrice">
                    <p>{$article['name']}</p>
                    <p>{$article['price']}€</p>
                </section>
            </article>
        HTML;
        }
        echo "</section>";

        echo <<<EOT
        
        
        <section id="warenkorbBereich">
        <h2 id="warenkorbTitel">Warenkorb</h2>
        <form action="Bestellung.php" method="post" accept-charset="UTF-8">
            <select id="warenkorb" name="warenkorb[]" size="3" multiple tabindex="1"></select>
            <p id="totalPrice">Gesamtpreis: 0.00€</p>
            <p><input type="text" id="addressInput" name="address" placeholder="Ihre Adresse" value="" tabindex="2"/></p>
            <section id="buttons">
                <button type="reset" id="resetButton" onclick="deleteAll()" tabindex="3" accesskey="l">Alle löschen</button>
                <button type="button" id="deleteSelectedButton" onclick="deleteSelection()" tabindex="4" accesskey="a">Auswahl löschen</button>
                <button type="submit" id="bestellenButton" tabindex="5" accesskey="b">Bestellen</button>
            </section>
        </form>
        </section>
        EOT;

        echo "</section>";

        echo <<< JS
        <script src="warenkorb.js"></script>
        JS;

        $this->generatePageFooter();
    }


    protected function processReceivedData():void
    {
        parent::processReceivedData();


        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['warenkorb'])) {

            $address = trim($_POST['address']);
            if (empty($address)) {
                $_SESSION['error_message'] = 'Bitte geben Sie Ihre Adresse ein.';
                header('Location: Bestellung.php');
                exit();
            }


            $address = $this->_database->real_escape_string($_POST['address']);

            $insertOrderingSQL = "INSERT INTO ordering (address) VALUES ('$address')";
            $this->_database->query($insertOrderingSQL);


            $orderingId = $this->_database->insert_id;

            $_SESSION["ordering_id"] = $orderingId;


            foreach ($_POST['warenkorb'] as $articleId) {
                $insertOrderedArticleSQL = "INSERT INTO ordered_article (ordering_id, article_id, status) VALUES ('$orderingId', '$articleId', 0)";
                $this->_database->query($insertOrderedArticleSQL);
            }


            header('Location: Bestellung.php');
            exit();
        }

    }


    public static function main():void
    {
        session_start();
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Bestellung::main();

