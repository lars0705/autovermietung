<?php 
// Build the image path based on the car's type_id
$imagePath = "../assets/images/cars/type_id_" . $car["type_id"] . ".png";

// Check if the specific car image exists
if (file_exists($imagePath)): ?>
    <!-- If the image exists, display it -->
    <img src="<?php echo htmlspecialchars($imagePath); ?>">
<?php else: ?>
    <!-- If not, show a placeholder image -->
    <img src="../assets/images/Placeholder_car.png">
<?php endif; ?>
