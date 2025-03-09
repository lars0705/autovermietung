<div class="video-container">
    <video id="heroVideo" autoplay muted playsinline></video>
    <div class="video-overlay">
        <img src="../assets/images/sigmacars_logo.png" alt="Sigmacars Logo" class="hero-logo" />
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const video = document.getElementById("heroVideo");
        const videos = [
            "../assets/videos/Hero_1.mp4",
            "../assets/videos/Hero_2.mp4",
            "../assets/videos/Hero_3.mp4"
        ];

        let currentVideo = 0;

        function playNextVideo() {
            video.src = videos[currentVideo];
            video.load();
            video.play();
            currentVideo = (currentVideo + 1) % videos.length;
        }

        video.addEventListener("ended", playNextVideo);
        playNextVideo();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const videoContainer = document.querySelector(".video-container");

        window.addEventListener("scroll", function () {
            let scrollTop = window.scrollY;
            let maxScroll = window.innerHeight / 2; // Bis zur Hälfte des Screens soll das Video verblassen
            let opacity = Math.max(0, 1 - scrollTop / maxScroll);

            videoContainer.style.opacity = opacity;
        });
    });
</script>
