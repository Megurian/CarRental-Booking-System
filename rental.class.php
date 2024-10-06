<?php
    require_once 'database.php';

    class Rental{
        public $id = '';
        public $client_name = '';
        public $rental_date = '';
        public $return_date = '';
        public $car_id = '';
        public $remarks = '';
        public $status = '';
    
    
        protected $db;
    
        function __construct(){
            $this->db = new Database();
        }

        function addRental () {
            // SQL query to insert a new product into the 'product' table.
            $sql = "INSERT INTO rentals (client_name, rental_date, return_date, car_id, remarks, status) 
                                 VALUES (:client_name, :rental_date, :return_date, :car_id, :remarks, :status);";

            // Prepare the SQL statement for execution.
            $query = $this->db->connect()->prepare($sql);

            // Bind the product properties to the named placeholders in the SQL statement.
            $query->bindParam(':client_name', $this->client_name);
            $query->bindParam(':rental_date', $this->rental_date);
            $query->bindParam(':return_date', $this->return_date);
            $query->bindParam(':car_id', $this->car_id);
            $query->bindParam(':remarks', $this->remarks);
            $query->bindParam(':status', $this->status);

            // Execute the query. If successful, return true; otherwise, return false.
            return $query->execute();
        }

        function updateRentalRecord () {
            // SQL query to insert a new product into the 'product' table.
            $sql = "UPDATE rentals 
                    SET client_name = :client_name, 
                        rental_date = :rental_date, 
                        return_date = :return_date, 
                        car_id = :car_id, 
                        remarks = :remarks, 
                        status = :status
                    WHERE id = :id;";

            // Prepare the SQL statement for execution.
            $query = $this->db->connect()->prepare($sql);

            // Bind the product properties to the named placeholders in the SQL statement.
            $query->bindParam(':client_name', $this->client_name);
            $query->bindParam(':rental_date', $this->rental_date);
            $query->bindParam(':return_date', $this->return_date);
            $query->bindParam(':car_id', $this->car_id);
            $query->bindParam(':remarks', $this->remarks);
            $query->bindParam(':status', $this->status);
            $query->bindParam(':id', $this->id);


            // Execute the query. If successful, return true; otherwise, return false.
            return $query->execute();
        }

        function fetchAllRecord($keyword = ''){
            $sql_statement = "SELECT * FROM rentals WHERE client_name LIKE CONCAT('%', :keyword, '%') ORDER BY created_at ASC;";    //sql query to fetch all records
    
            //prepare query for execution
            $query = $this->db->connect()->prepare($sql_statement);
            $query->bindParam(":keyword", $keyword);

            $data = null;    //initialize a variable to hold the fetched data
    
            if($query->execute()){
                $data = $query->fetchAll(); //fetch all rows from the result set
            }
    
            return $data;   //return data after function called
        }

        function fetchCars () {
            // Define the SQL query to select all columns from the 'category' table,
            // ordering the results by the 'name' column in ascending order.
            $sql = "SELECT * FROM cars ORDER BY car_name ASC;";
        
            // Prepare the SQL statement for execution using a database connection.
            $query = $this->db->connect()->prepare($sql);
        
            // Initialize a variable to hold the fetched data. This will store the results of the query.
            $data = null;
        
            // Execute the prepared SQL query.
            // If the execution is successful, fetch all the results from the query's result set.
            // Use fetchAll() to retrieve all rows as an array of associative arrays.
            if ($query->execute()) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as an associative array.
            }
        
            // Return the fetched data. This will be an array of categories, where each category
            // is represented as an associative array with column names as keys.
            return $data;
        }

        function getBookingRecordbyID($ItemID){
            $sql_statement = "SELECT * FROM rentals WHERE id=:ItemID;";    //sql query to fetch a record by ID
    
            //prepare query for execution
            $query = $this->db->connect()->prepare($sql_statement);
            $query->bindParam(":ItemID", $ItemID);
            $data = null;    //initialize a variable to hold the fetched data
    
            if($query->execute()){
                $data = $query->fetch(); //fetch record with specified ItemID from the result set
            }
    
            return $data;   //return data after function called
        }

        function getCarRecordbyID ($carID){
            $sql = "SELECT * FROM cars WHERE id = :car_id;";
            $query = $this->db->connect()->prepare($sql);

            $query->bindParam(':car_id', $carID);

            $data = null;
            if ($query->execute()) {
                $data = $query->fetch(); // Fetch all rows as an associative array.
            }

            return $data;
        }

        function decrementCarQuantity ($carID, $quantity){
            $quantity -= 1;
            // SQL query to update an existing product in the 'product' table.
            $sql = "UPDATE cars SET quantity = :quantity WHERE id = :car_id;";

            // Prepare the SQL statement for execution.
            $query = $this->db->connect()->prepare($sql);

            // Bind the product properties and ID to the SQL statement.
            $query->bindParam(':car_id', $carID);
            $query->bindParam(':quantity', $quantity);
            
            // Execute the query. If successful, return true; otherwise, return false.
            return $query->execute();
        }

        function incrementCarQuantity ($carID, $quantity){
            $quantity += 1;
            // SQL query to update an existing product in the 'product' table.
            $sql = "UPDATE cars SET quantity = :quantity WHERE id = :car_id;";

            // Prepare the SQL statement for execution.
            $query = $this->db->connect()->prepare($sql);

            // Bind the product properties and ID to the SQL statement.
            $query->bindParam(':car_id', $carID);
            $query->bindParam(':quantity', $quantity);
            
            // Execute the query. If successful, return true; otherwise, return false.
            return $query->execute();
        }
    }

    $obj = new Rental();
    /* $obj->incrementCarQuantity (4, 1); */
    /* $obj->addRental(); */
    /* var_dump($obj->incrementCarQuantity(6, 1)); */