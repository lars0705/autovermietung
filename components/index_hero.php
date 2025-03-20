<!-- hero-video section in index.php -->
<div class="video-container">
    <video id="heroVideo" autoplay muted playsinline></video>
    <div class="video-overlay">
        <img src="../assets/images/sigmacars_logo.png" alt="Sigmacars Logo" class="hero-logo" />
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const video = document.getElementById("heroVideo");

        // array of video sources for looping playback
        const videos = [
            "../assets/videos/Hero_1.mp4",
            "../assets/videos/Hero_2.mp4",
            "../assets/videos/Hero_3.mp4"
        ];

        let currentVideo = 0;

        // cycles through the videos
        function playNextVideo() {
            video.src = videos[currentVideo];
            video.load();
            video.play();
            currentVideo = (currentVideo + 1) % videos.length;
        }

        video.addEventListener("ended", playNextVideo);
        playNextVideo();
    });

    //Video Fade
    window.addEventListener('scroll', () => {
        const videoContainer = document.querySelector('.video-container');
        const scrollY = window.scrollY;
        const fadeStart = 0; // Start fading at top
        const fadeEnd = 700; // Fully faded out after 300px scroll

        // Calculate new opacity
        let opacity = 1 - (scrollY - fadeStart) / (fadeEnd - fadeStart);
        opacity = Math.max(opacity, 0); // Ensure it doesn't go below 0

        videoContainer.style.opacity = opacity;
    });

</script>
