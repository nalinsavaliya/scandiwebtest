<?php

namespace App;
use PDO;

class Connection
{
    /**
     * @var PDO
     */
    protected $con;
    /**
     * @var string
     */
    private $user = 'root';
    /**
     * @var string
     */
    private $pass = 'root';

    /**
     * Connection constructor.
     */
    public function __construct() {
        $this->con = new PDO('mysql:host=localhost;dbname=webfiaxf_scandiwebtest', $this->user, $this->pass);
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return string
     */
    public function getBaseUrl(){
        return "http://localhost/testexam/";
    }

}