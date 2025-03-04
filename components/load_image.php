<?php 
$imagePath = "../assets/images/cars/type_id_" . $car["type_id"] . ".png";
if (file_exists($imagePath)): ?>
    <img src="<?php echo htmlspecialchars($imagePath); ?>">
<?php else: ?>
    <img src="../assets/images/Placeholder_car.png">
<?php endif; ?>