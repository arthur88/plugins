<?php
require_once("funcs.php");

class pay extends users{
    protected $data;
    private $mysqli, $value, $s;


    /**
     * [payValues_byID get paysera values from DB]
     * @param  [int] $data [id of columns]
     * @return [array]       [return array of colun values]
     */
    function payValues_byID($data){
        $mysqli = mysqli_query(con(), "SELECT qty, price FROM payments WHERE id = '" . safe($data) . "' ");
        $value = $mysqli->fetch_array();
        if($mysqli == TRUE){ return array("qty" => $value['qty'], "price" => $value['price']); }
        else { return FALSE; }
        mysqli_free_result($mysqli);
        mysqli_close(con());
    }


    /**
     * [get_self_url paysera function ]
     * @return [string] [return url]
     */
    function get_self_url() {
        $s = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'));

        if (!empty($_SERVER["HTTPS"])) {
            $s .= ($_SERVER["HTTPS"] == "on") ? "s" : "";
        }
        $s .= '://' . $_SERVER['HTTP_HOST'];
        if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
            $s .= ':' . $_SERVER['SERVER_PORT'];
        }
        $s .= dirname($_SERVER['SCRIPT_NAME']);
        return $s;
    }

}
?>