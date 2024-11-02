<style>
    /* Home Button */
    .home-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #172433;
        border: 1px solid #01cfbe;
        color: #01cfbe;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .home-button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }
    .home-button i{
        color: #01fce7 !important;
    }

    /* Scroll-to-Top Button */
    .scroll-top-button {
        position: fixed;
        bottom: 80px; /* Positioned above the Home button */
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #01cfbe6b;
        border: 1px solid #01cfbe;
        color: #172433;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1001; /* Ensure it appears above the Home button */
        opacity: 0;
        visibility: hidden;
    }

    .scroll-top-button.show {
        opacity: 1;
        visibility: visible;
    }

    .scroll-top-button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(3, 142, 160, 0.3);
        border: #0A74DA;
    }

    /* Social Icons Container */
    .social-icons {
        position: fixed;
        bottom: 20px;
        right: 80px; /* Offset from home button */
        display: flex;
        gap: 10px;
        opacity: 0;
        visibility: hidden;
        transform: translateX(20px);
        transition: all 0.4s ease;
        z-index: 1000;
    }

    .social-icons.show {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    /* Individual Social Icon */
    .social-icon {
        width: 50px;
        height: 50px;
        background-color: #2e2a63; /* Default color */
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .social-icon:hover {
        transform: scale(1.1) rotate(15deg);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .social-icon.ig {
        background-color: #E4405F; /* Instagram color */
    }

    .social-icon.link {
        background-color: #333; /* Link color */
    }

    .social-icon.phone {
        background-color: #0A74DA; /* Phone color */
    }

    /* Popup Overlay */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 2000;
    }

    .popup-overlay.show {
        visibility: visible;
        opacity: 1;
    }

    /* Popup Content */
    .popup-content {
        width: 350px;
        align-content: center;
        background-color: #191929;
        color: #ffffff;
        padding: 30px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        position: relative;
        text-align: center;
    }

    .popup-content h2 {
        margin-top: 0;
        font-size: 12pt;
    }
    .popup-content p{
        margin: 0;
        font-size: 11pt;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Close Button */
    .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        color: #333;
    }
    .info a{
        font-size: 12pt;
        text-decoration: none;
        margin: 4px;
        gap: 3px
    }
    .info a:hover{
        color: #01cfbe !important;
    }
    .info{
        margin-top: 20px;
    }

</style>

<!-- Home Button -->
<div class="home-button" onclick="toggleSocialIcons()">
    <i class="bi bi-house-fill text-white" style="font-size: 24px;"></i>
</div>

<!-- Scroll-to-Top Button -->
<div class="scroll-top-button" id="scrollTopButton" onclick="scrollToTop()">
    <!-- Arrow-up Icon -->
    <i class="bi bi-arrow-up-short text-white" style="font-size: 24px;"></i>
</div>

<!-- Social Icons -->
<div class="social-icons" id="socialIcons">
    <!-- Discord Icon -->
    <div class="social-icon wa" onclick="redirectToDiscord()">
        <i class="bi bi-discord text-white" style="font-size: 24px;"></i>
    </div>
    <!-- Instagram Icon -->
    <div class="social-icon ig" onclick="redirectToInstagram()">
        <i class="bi bi-instagram text-white" style="font-size: 24px;"></i>
    </div>

    <!-- Admin Icon -->
    @if (auth()->check() && auth()->user()->hasRole('admin'))
    <a href="/admin" class="social-icon link" >
        <i class="bi bi-laptop text-white" style="font-size: 24px;"></i>
    </a>
    @endif
    <!-- Info Icon -->
    <div class="social-icon phone" onclick="showPopup()">
        <i class="bi bi-exclamation-circle" style="font-size: 24px;"></i>
    </div>
</div>

<!-- Popup Overlay -->
<div class="popup-overlay" id="popupOverlay" onclick="hidePopup(event)">
    <div class="popup-content">
        <span class="popup-close" onclick="hidePopup()">×</span>
        <h4>Kontak Kami</h4>
        <p>Jangan ragu untuk bertanya kepada kami</p>

        <div class="info">
            <a href=""><i class="bi bi-whatsapp text-white"> Humas</i></a>
            <a href=""><i class="bi bi-whatsapp text-white"> ketua</i></a>
            <a href=""><i class="bi bi-whatsapp text-white"> pemateri</i></a>
        </div>
        
    </div>
</div>

<script>
    // Toggle social icons visibility
    function toggleSocialIcons() {
        var socialIcons = document.getElementById('socialIcons');
        socialIcons.classList.toggle('show');
    }

    // Redirect to Discord
    function redirectToDiscord() {
        var url = 'https://discord.gg/VWbeeYf7Xr';
        window.open(url, '_blank');
    }

    // Redirect to Instagram
    function redirectToInstagram() {
        var url = 'https://www.instagram.com/ukm_cyber';
        window.open(url, '_blank');
    }

    // Show popup with contact details
    function showPopup() {
        document.getElementById('popupOverlay').classList.add('show');
    }

    // Hide popup
    function hidePopup(event) {
        if (event.target === document.getElementById('popupOverlay') || event.target.classList.contains('popup-close')) {
            document.getElementById('popupOverlay').classList.remove('show');
        }
    }

    // Show the scroll-to-top button when the user scrolls down 100px from the top
    window.onscroll = function() {
        var scrollTopButton = document.getElementById('scrollTopButton');
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            scrollTopButton.classList.add('show');
        } else {
            scrollTopButton.classList.remove('show');
        }
    };

    // Smooth scroll to the top of the page
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
</script>
