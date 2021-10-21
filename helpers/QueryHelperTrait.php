<?php
trait QueryHelperTrait {

    /**
     * Execute a query & return the resulting data as an array of assoc arrays
     * @param string $sql query to execute
     * @return boolean|array array of associative arrays - query results for select
     *     otherwise true or false for insert/update/delete success
     */
    function query($sql) {
        global $mysqli_db;
        $result = $mysqli_db->query($sql);
        if (!is_object($result)) {
            return $result;
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
