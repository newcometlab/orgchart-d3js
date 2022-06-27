<?php
    class DB {
        public $conn = NULL;
        public $servername = "sql5.freemysqlhosting.net";
        public $dbname = "sql5502372";
        public $username = "sql5502372";
        public $password = "zBrWLAmfqa";

        function __construct() {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
			
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        public function getChilds($id = 0) {
            $sql = "SELECT `id`, `desc` FROM `position` WHERE `parent_id`=" . $id;
            $result = $this->conn->query($sql);
            $rows = array();
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    array_push($rows, $row);
                }
            }
 
            return $rows;
        }
    }

    $db = new DB();

    $id = $_GET['id'] != null ? $_GET['id'] : 0;

    $stmt = $db->getChilds($id);

    if($id == 0) 
    {
        // Initial load: Get the first children too
        $row = $stmt[0];

        $stmt2 = $db->getChilds($row['id']);
        $i_j = 0;
        $childs = array();

        foreach ($stmt2 as $childrow) 
        {
            $stmt3 = $db->getChilds($childrow['id']);

            $hasChildRow = count($stmt3);

            if($hasChildRow > 0) 
            {
                $hasChild = true;
            } 
            else 
            {         
                $hasChild = false;
            }
            
            $childs[$i_j] = array("id" => $childrow['id'], "desc" => $childrow['desc'], "hasChild" => $hasChild);
            
            $i_j++;
        }

        echo json_encode(array("id" => $row['id'], "desc" => $row['desc'], "children" => $childs));
    } 
    else 
    {
        // Just check if there are children
        $i_i = 0;

        $childs = array();
        
        foreach ($stmt as $childrow) 
        {
            $stmt3 = $db->getChilds($childrow['id']);

            $hasChildRow = count($stmt3);

            if($hasChildRow > 0) 
            {
                $hasChild = true;
            } 
            else 
            {
                $hasChild = false;
            }
            
            $childs[$i_i] = array("id" => $childrow['id'], "desc" => $childrow['desc'], "hasChild" => $hasChild);

            $i_i++;     
        }
        
        echo json_encode( array("result" => $childs) );
    }
?>