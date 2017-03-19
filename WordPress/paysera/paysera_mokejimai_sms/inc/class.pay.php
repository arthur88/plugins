<?php
class pay {
    protected $data;
    private $mysqli, $value;

    /**
     * [qty_by_code get qty by code]
     * @param  [string] $data [keyword]
     * @return [int]       [qty]
     */
    function qty_by_code($data){
        $mysqli = mysqli_query(con(), "SELECT qty FROM payments WHERE keyword = '" . safe($data) . "' ");
        $value = $mysqli->fetch_array();
        if($mysqli == TRUE){
            return safe($value['qty']);
        } else { return FALSE; }

        mysqli_free_result($mysqli);
        mysqli_close(con());
    }


}
?>