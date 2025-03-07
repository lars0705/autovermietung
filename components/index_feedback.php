<?php
require_once "db_connect.php";

// Zufällige 5 Feedbacks abrufen
$sql = "SELECT f.feedback_text, f.rating, u.username FROM feedback f 
        JOIN users u ON f.user_id = u.user_id 
        ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$conn->close();
?>

<div class="feedback_section">
    <?php foreach ($feedbacks as $index => $fb): ?>
        <div class="feedback_slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <p class="feedback_rating">
                <?php
                $rating = (int) $fb["rating"];
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        echo '<span class="star filled">★</span>'; // Gelbe Sterne
                    } else {
                        echo '<span class="star empty">☆</span>'; // Graue Sterne
                    }
                }
                ?>
            </p>
            <p class="feedback_text">"<?php echo htmlspecialchars($fb["feedback_text"]); ?>"</p>
            <p class="feedback_user">~ <?php echo htmlspecialchars($fb["username"]); ?></p>
        </div>
    <?php endforeach; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll(".feedback_slide");
    let currentIndex = 0;

    function showNextSlide() {
        slides[currentIndex].classList.remove("active");
        currentIndex = (currentIndex + 1) % slides.length;
        slides[currentIndex].classList.add("active");
    }

    setInterval(showNextSlide, 5000);
});
</script>