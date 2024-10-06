<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Sheet</title>
    <link rel="stylesheet" href="https://classless.de/classless.css">
    <style>
        th, td {
            text-align: left; /* Aligns the text horizontally to the center */
            vertical-align: middle; /* Aligns the text vertically to the middle */
        }
    </style>
</head>
<body>
    <button onclick="window.location.href='booking.php';">Book Rental</button>

    <?php
        require_once 'rental.class.php'; //Create an instance of books class so that we can use the methods
        require_once 'functions.php';

        //Create an instance where to put the properties and method of Books class from books.class.php
        $rentalObj = new Rental(); 
        //Call the fetchRecord() method to retrieve all database and populate into an array
        $array = $rentalObj->fetchAllRecord();

        $keyword = '';
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])){
            $keyword = clean_input($_POST['keyword']);
            $array = $rentalObj->fetchAllRecord($keyword);
        }
    ?>
    <form action="" method="post">
        <label for="keyword">Search Client</label>
        <input type="text" name="keyword" placeholder="Enter keyword to search" value="<?= $keyword ?>">
        <input type="submit" value="Search" name="search">
    </form>
    <h3>RENTAL SHEET</h3>
    <hr>
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Rental Date</th>
                <th>Return Date</th>
                <th>Car</th>
                <th>Remark</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <?php
            
            foreach ($array as $arr) { //for loop for the array data
            $car = $rentalObj->getCarRecordbyID($arr['car_id'])
        ?>
        <tbody>
            <tr>
                <!--HINT: The variable inside the $arr[''] must be the column name from the database -->
                <td><?= $arr['client_name'] ?></td>
                <td><?= $arr['rental_date'] ?></td>
                <td><?= $arr['return_date'] ?></td>
                <td><?= $car['car_name']?> - <?= $car['car_model']?></td>
                <td><?= $arr['remarks'] ?></td>
                <td><?= $arr['status'] ?></td>
                <td><a href="edit.booking.php?id=<?= $arr['id'] ?>">Edit</a></td>
            </tr>
        </tbody>
        <?php
            }
        ?>
    </table>

    
</body>
</html>