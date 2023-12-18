<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOT</title>
    <!-- Include HTML file into the current HTML website with PHP -->
    <?php include ("./navbar.html"); ?>
</head>
<body>
    <center><h1 style="background-color: orange;">This is Staff page</h1></center>
</body>
</html>

<?php
    session_destroy()
?>