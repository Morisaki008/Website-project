<?php include 'includes/header.php'; ?>

<style>
    /* Video Background Styles */
    .video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    #myVideo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: -1;
        object-fit: cover;
    }

    .video-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(12, 20, 69, 0.7);
        z-index: -1;
    }

    section.hero {
        position: relative;
        z-index: 1;
        background: transparent;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Add transparent footer style specific to index page */
    .footer {
        background: rgba(10, 10, 46, 0.25) !important;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(78, 0, 255, 0.25);
    }
    
    .footer p {
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
</style>

<!-- Video Background -->
<div class="video-background">
    <video id="myVideo" autoplay muted loop playsinline>
        <source src="videos/chainsaw-man-girls-moewalls-com.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
</div>

<section class="hero">
    <div class="hero-content">
        <h1 class="glitch" data-text="Welcome">Hello, Welcome To My Portfolio</h1>
        <p class="subtitle">Creative Designer & Developer</p>
        <p class="intro">I'm a passionate web developer who loves creating  functional websites.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>