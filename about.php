<?php include 'includes/header.php'; ?>

<style>
    #background-video {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -2;
    }

    .overlay-image {
        display: absolute; /* Show the overlay image */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: -1;
        opacity: 0.9; /* Restore transparency */
    }

    .about {
        background-color: transparent;
        background-image: none; /* Remove the background image */
    }
</style>

<video id="background-video" autoplay loop muted>
    <source src="videos/chainsaw-man-girls-moewalls-com.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<section id="about" class="about">
    <div class="about-content-wrapper">
        <h2 class="section-title">About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <div class="section-block">
                    <h3>Background</h3>
                    <p>I'm a creative designer and developer with a passion for building engaging digital experiences. My journey in web development started with a curiosity about how websites work, which led me to dive deep into HTML, CSS, and PHP.</p>
                </div>
                
                <div class="section-block">
                    <h3>Education</h3>
                    <div class="education-item">
                        <h4>Bachelor of Science in Information Technology</h4>
                        <p>Dr. Yanga's Colleges Inc. | 2023 - Present</p>
                    </div>
                </div>

                <div class="section-block">
                    <h3>Skills</h3>
                    <div class="skills-grid">
                        <div class="skill-item">
                            <i class="fab fa-html5"></i>
                            <span>HTML</span>
                        </div>
                        <div class="skill-item">
                            <i class="fab fa-css3-alt"></i>
                            <span>CSS</span>
                        </div>
                        <div class="skill-item">
                            <i class="fab fa-php"></i>
                            <span>PHP</span>
                        </div>
                        <div class="skill-item">
                            <i class="fas fa-database"></i>
                            <span>MySQL</span>
                        </div>
                        <div class="skill-item">
                            <i class="fab fa-js"></i>
                            <span>JavaScript</span>
                        </div>
                        <div class="skill-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Responsive Design</span>
                        </div>
                    </div>
                </div>

                <div class="section-block">
                    <h3>Interests</h3>
                    <p>When I'm not coding, I'm playing games or watching animes</p>
                </div>
            </div>
            <div class="about-image">
                <div class="about-image-container">
                    <img src="Pictures\Darren.jpg" alt="Professional Photo" class="profile-photo">
                    <div class="personal-info">
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">Darren Mamuad</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Age:</span>
                            <span class="info-value">20</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Birthday:</span>
                            <span class="info-value">August 08, 2004</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>