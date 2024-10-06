<?php
// Include the functions.php file for utility functions like clean_input, and the product.class.php for database operations.
require_once('functions.php');
require_once('rental.class.php');

// Initialize variables to hold form input values and error messages.
$client_name = $rental_date = $return_date = $car_id = $remarks = $quantity = '';
$client_nameE = $rental_dateE = $return_dateE = $car_idE = $remarksE = '';

// Create an instance of the Product class for database interaction.
$rentalObj = new Rental();

// Check if the form was submitted using the POST method.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // Clean and assign the input values to variables using the clean_input function to prevent XSS or other malicious input.
    $client_name = clean_input($_POST['client_name']);
    $rental_date = clean_input($_POST['rental_date']);
    $return_date = clean_input($_POST['return_date']);
    $car_id = clean_input($_POST['car_id']);
    $remarks = clean_input($_POST['remarks']);

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
    if(empty($rental_date)) {
        $return_dateE = 'Return date is required';
    } elseif($inputRentalDate < $currentTimestamp) {
        $rental_dateE = "The date of rental cannot be in the past. Please enter a valid date.";
    } elseif(empty($return_date)) {
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

    

    // If there are no validation errors, proceed to add the product to the database.
    if(empty($client_nameE) && empty($rental_dateE) && empty($return_dateE) && empty($car_idE)){
        // Assign the sanitized inputs to the product object.
        $rentalObj->client_name = $client_name;
        $rentalObj->rental_date = $rental_date;
        $rentalObj->return_date = $return_date;
        $rentalObj->car_id = $car_id;
        $rentalObj->remarks = $remarks;
        $rentalObj->status = 'Booked';

        // Attempt to add the product to the database.
        if($rentalObj->addRental()){
            $rentalObj->decrementCarQuantity($car_id, $quantity);
            // If successful, redirect to the product listing page.
            header('Location: rentals.table.php');
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
        <hr><br>

        <label for="client_name"> Client's Name: </label><br>
        <input type="text" name="client_name" id="" value="<?= $client_name ?>">
        <?php if(!empty($client_nameE)): ?>
            <span class="error"><?= $client_nameE ?></span><br><br>
        <?php endif; ?>

        <label for="rental_date"> Rental Date: </label><br>
        <input type="date" name="rental_date" id="" value="<?= $rental_date ?>">
        <?php if(!empty($rental_dateE)): ?>
            <span class="error"><?= $rental_dateE ?></span><br><br>
        <?php endif; ?>

        <label for="return_date"> Return Date: </label><br>
        <input type="date" name="return_date" id="" value="<?= $return_date ?>">
        <?php if(!empty($return_dateE)): ?>
            <span class="error"><?= $return_dateE ?></span><br><br>
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
        <?php if(!empty($car_idE)): ?>
            <span class="error"><?= $car_idE ?></span><br><br>
        <?php endif; ?>
        <br>

        <label for="remarks"> Remark/Specific Instructions (Optional): </label><br>
        <textarea name="remarks" id="" cols="30" rows="10" placeholder="You may request specific features or condition here..." value="<?= $remarks ?>"></textarea>
        <?php if(!empty($remarksE)): ?>
            <span class="error"><?= $remarksE ?></span><br><br>
        <?php endif; ?>

        <input type="submit" value="Book">
    </form>
</body>
</html>