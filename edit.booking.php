<?php
// Include the functions.php file for utility functions like clean_input, and the product.class.php for database operations.
require_once('functions.php');
require_once('rental.class.php');

// Initialize variables to hold form input values and error messages.
$client_name = $rental_date = $return_date = $car_id = $remarks = $quantity = $status = '';
$client_nameE = $rental_dateE = $return_dateE = $car_idE = $remarksE = $statusE = '';

// Create an instance of the Product class for database interaction.
$rentalObj = new Rental();

//getting information of a specific record
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $id = clean_input($_GET['id']);
        $record = $rentalObj->getBookingRecordbyID($id);
        if(isset($record) && !empty($record)){
            $client_name = $record['client_name'];
            $rental_date = $record['rental_date'];
            $return_date = $record['return_date'];
            $car_id = $record['car_id'];
            $remarks = $record['remarks'];
            $status = $record['status'];
        } else {
            echo "Record does not exist";
            exit;
        }
    } else {
        echo "Record does not exist";
        exit;
    }
}

// Check if the form was submitted using the POST method.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // Clean and assign the input values to variables using the clean_input function to prevent XSS or other malicious input.
    $client_name = clean_input($_POST['client_name']);
    $rental_date = clean_input($_POST['rental_date']);
    $return_date = clean_input($_POST['return_date']);
    $car_id = clean_input($_POST['car_id']);
    $remarks = clean_input($_POST['remarks']);
    $status = clean_input($_POST['status']);

    if(empty($client_name)){
        $client_nameE = 'Client name is required to book a rental';
    }

    // Validate the 'name' field: it must not be empty.
    if(empty($rental_date)){
        $rental_dateE = 'Rental date is required';
    }

    // Validate the 'category' field: it must not be empty.
    

    $inputRentalDate= strtotime($rental_date); //Convert the user input pub_date to a timestamp
    $inputReturnDate= strtotime($return_date);
    $currentTimestamp = time();       
    if($inputRentalDate < $currentTimestamp) {
        $rental_dateE = "The date of rental cannot be in the past. Please enter a valid date.";
    }
    if(empty($return_date)){
        $return_dateE = 'Return date is required';
    } elseif($inputRentalDate > $inputReturnDate) {
        $return_dateE = "Return date can't be before rental date";
    }

    if (empty($car_id)) {
        $car_idE = 'Please select a car to rent';
    } else {
        $car = $rentalObj->getCarRecordbyID($car_id);
        $quantity = $car['quantity'];
        if ($quantity <= 0) {
            $car_idE = 'Car selected is not available';
        }
    }
    $id = clean_input($_GET['id']);
    $rentalRecord = $rentalObj->getBookingRecordbyID($id);
    if(empty($status)) {
        $statusE = 'Please identify rental status';
    } elseif($rentalRecord['status'] == 'Completed' || $rentalRecord['status'] == 'Cancelled') {
        $statusE = 'This rental is no longer active, cannot change status';
        $status = $rentalRecord['status'];
    }

    

    // If there are no validation errors, proceed to add the product to the database.
    if(empty($client_nameE) && empty($rental_dateE) && empty($return_dateE) && empty($car_idE) && empty($statusE)){
        // Assign the sanitized inputs to the product object.
        $rentalObj->id = clean_input($_GET['id']);
        $rentalObj->client_name = $client_name;
        $rentalObj->rental_date = $rental_date;
        $rentalObj->return_date = $return_date;
        $rentalObj->car_id = $car_id;
        $rentalObj->remarks = $remarks;
        $rentalObj->status = $status;

        // Attempt to add the product to the database.
        if($rentalObj->updateRentalRecord()){
            $id = clean_input($_GET['id']);
            $rentalRecord = $rentalObj->getBookingRecordbyID($id);
            $carRecord = $rentalObj->getCarRecordbyID($rentalRecord['car_id']);
            if($car_id == $rentalRecord['car_id']){
                header('Location: rentals.table.php');
            } else {
                $rentalObj->incrementCarQuantity($rentalRecord['car_id'], $carRecord['quantity']);
                $rentalObj->decrementCarQuantity($car_id, $quantity);
                header('Location: rentals.table.php');
            }
            
        } else {
            // If an error occurs during insertion, display an error message.
            echo 'Something went wrong when adding the new product.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking: Car Rental</title>
    <link rel="stylesheet" href="https://classless.de/classless.css">
    <style>
        /* Error message styling */
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <button onclick="window.location.href='rentals.table.php';">Back</button>
    <form action="" method="post">
        <h2>Book a Rental</h2>
        <?php if(!empty($statusE)): ?>
            <span class="error"><?= $statusE ?></span><br>
        <?php endif; ?>
        <hr><br>

        <label for="client_name"> Client's Name: </label><br>
        <input type="text" name="client_name" id="" value="<?= $client_name ?>">
        <br>
        <?php if(!empty($client_nameE)): ?>
            <span class="error"><?= $client_nameE ?></span><br>
        <?php endif; ?>


        <label for="rental_date"> Rental Date: </label><br>
        <input type="date" name="rental_date" id="" value="<?= $rental_date ?>">
        <br>
        <?php if(!empty($rental_dateE)): ?>
            <span class="error"><?= $rental_dateE ?></span><br>
        <?php endif; ?>


        <label for="return_date"> Return Date: </label><br>
        <input type="date" name="return_date" id="" value="<?= $return_date ?>">
        <br>
        <?php if(!empty($return_dateE)): ?>
            <span class="error"><?= $return_dateE ?></span><br>
        <?php endif; ?>


        <label for="car_id"> Select Car: </label><br>
        <select name="car_id" id="">
            <option value="">-- Selec a Car --</option>
            <?php
                $carList = $rentalObj->fetchCars();
                foreach ($carList as $car){
            ?>
                <option value="<?= $car['id'] ?>" <?= ($car_id == $car['id']) ? 'selected' : '' ?>><?= $car['car_name']?> - <?= $car['car_model']?></option>
            <?php
                }
            ?>
        </select>
        <br>
        <?php if(!empty($car_idE)): ?>
            <span class="error"><?= $car_idE ?></span><br>
        <?php endif; ?>


        <label for="remarks"> Remark/Specific Instructions (Optional): </label><br>
        <textarea name="remarks" id="" cols="30" rows="10" placeholder="You may request specific features or condition here..." value="<?= $remarks ?>"></textarea>
        <br>
        <?php if(!empty($remarksE)): ?>
            <span class="error"><?= $remarksE ?></span><br>
        <?php endif; ?>


        <label for="status"> Rental Status: </label><br>
        <select name="status" id="">
            <option value="Booked" <?= ($status == 'Booked') ? 'selected' : '' ?>>Booked</option>
            <option value="Completed" <?= ($status == 'Completed') ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= ($status == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <br>
        

        <input type="submit" value="Update Booking">
    </form>
</body>
</html>