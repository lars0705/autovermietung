<!-- recommended locations section in index.php -->
<h1 class="section_title">Unsere Standort-Empfehlungen</h1>

<div class="city_recommendations">
    <div class="city" data-video="../assets/videos/berlin.mp4">
        <img src="../assets/images/berlin.png" alt="Berlin">
        <video class="city_video" src="../assets/videos/berlin.mp4" muted loop></video>
        <div class="city_info">
            <h3>Berlin</h3>
            <p>Erleben Sie die pulsierende Hauptstadt mit einzigartiger Kultur und Geschichte.</p>
        </div>
    </div>

    <div class="city" data-video="../assets/videos/hamburg.mp4">
        <img src="../assets/images/hamburg.png" alt="Hamburg">
        <video class="city_video" src="../assets/videos/hamburg.mp4" muted loop></video>
        <div class="city_info">
            <h3>Hamburg</h3>
            <p>Genießen Sie die maritime Atmosphäre und das aufregende Nachtleben.</p>
        </div>
    </div>

    <div class="city" data-video="../assets/videos/muenchen.mp4">
        <img src="../assets/images/muenchen.png" alt="München">
        <video class="city_video" src="../assets/videos/muenchen.mp4" muted loop></video>
        <div class="city_info">
            <h3>München</h3>
            <p>Erleben Sie bayerische Traditionen und atemberaubende Architektur.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cityElements = document.querySelectorAll(".city");
    
        cityElements.forEach(city => {
            const video = city.querySelector(".city_video");
            const img = city.querySelector("img");
    
            // show video and hide image on hover
            city.addEventListener("mouseenter", () => {
                video.style.opacity = "1";  
                img.style.opacity = "0";   
                video.play();
            });
    
            // hide video and show image when hover ends
            city.addEventListener("mouseleave", () => {
                video.style.opacity = "0"; 
                img.style.opacity = "1";   
                video.pause();
                video.currentTime = 0;
            });
        });
    });
</script>