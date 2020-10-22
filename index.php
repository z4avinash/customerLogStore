<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CustomerLog | STORE</title>
    <!-- Stylesheet -->
    <style>
        body>.container {
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .submit {
            padding: 10px;
            background-color: rgb(0, 192, 58);
            color: white;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
        }

        label {
            padding: 10px;
            background-color: rgb(0, 140, 255);
            color: white;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Customer Log Store</h1>
    <hr><br><br>

    <div class="container">
        <form method="POST" enctype="multipart/form-data" id="formId">
            <label for="filePicker">Select a File</label>
            <input type="file" id="filePicker" name="filePicker" style="display: none;"><br><br><br>
            <input type="submit" value="Upload File" class="submit">
        </form>
        <br><br><br>
    </div>


    <?php include "upload.php" ?>

    <?php

    //database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "world";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $myfile = fopen($target_file, "r");
    $data = array();
    while (!feof($myfile)) {
        $getTextLine = fgets($myfile);
        $exploidLine = explode(":offline request: params=>", $getTextLine);

        if (isset($exploidLine[1])) {
            $data = json_decode($exploidLine[1], true);
            $machine_id = $data['offline_data_arr'][0]['machine_id']; //machine id
            $hotel_name = $data['offline_data_arr'][0]['receipt_json']['hotel_name']; //hotel name
            $time = $data['offline_data_arr'][0]['receipt_json']['time']; //time
            $payment_method = $data['offline_data_arr'][0]['receipt_json']['method']; //payment method
            $amount = $data['offline_data_arr'][0]['receipt_json']['tickets'][0]['amount']; //amount
            $quantity = $data['offline_data_arr'][0]['receipt_json']['tickets'][0]['quantity']; //quantity
            $hotel_id = $data['offline_data_arr'][0]['prepaid_tickets'][0][0]['hotel_id']; // hotel id
            $ticket_id = $data['offline_data_arr'][0]['prepaid_tickets'][0][0]['ticket_id']; //ticket id

            //inserting into database
            $sql = "INSERT INTO customers (time, hotel_name,payment_method,amount,ticket_quantity,machine_id,ticket_id,hotel_id)
            VALUES ('{$time}','{$hotel_name}','{$payment_method}','{$amount}','{$quantity}','{$machine_id}','{$ticket_id}','{$hotel_id}')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }


    fclose($myfile);

    $conn->close();

    ?>



</body>

</html>