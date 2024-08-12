<?php declare(strict_types=1);

abstract class Page
{

    protected MySQLi $_database;


    protected function __construct()
    {
        error_reporting(E_ALL);

        $host = "localhost";

        if (gethostbyname('mariadb') != "mariadb") {
            $host = "mariadb";
        }

        $this->_database = new MySQLi($host, "public", "public", "pizzaservice");

        if (mysqli_connect_errno()) {
            throw new Exception("Connect failed: " . mysqli_connect_error());
        }

        if (!$this->_database->set_charset("utf8")) {
            throw new Exception($this->_database->error);
        }
    }

    public function __destruct()
    {
        $this->_database->close();
    }


    protected function generatePageHeader(string $title = "", string $jsFile = "", string $style ="", bool $autoreload = false):void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");

        echo <<<EOT
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                <link rel="stylesheet" href="$style" />
            </head>
            <body>
        EOT;
    }

    protected function generatePageFooter():void
    {

        echo <<<EOT
            </body>
        </html>
        EOT;
    }


    protected function processReceivedData():void
    {

    }
}