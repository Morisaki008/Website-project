<?php include 'includes/header.php'; ?>

<div class="modal" id="projectModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <img src="" alt="Project Preview" id="modalImage">
    </div>
</div>

<section class="projects">
    <h2 class="section-title">Projects</h2>
    <div class="projects-grid">
        <div class="project-card">
            <div class="project-image">
                <img src="Project picture/HomePagePortfolio.jpg" alt="Project 1">
            </div>
            <div class="project-info">
                <h3>🌐Personal Portfolio Website</h3>
                <p>A responsive portfolio website built with HTML, CSS, and PHP. Features include:</p>
                <ul class="project-features">
                    <li>Responsive design for all devices</li>
                    <li>Contact form with PHP validation</li>
                    <li>Dynamic content management</li>
                    <li>Modern animations and transitions</li>
                </ul>
                <div class="project-links"> 
                    <a href="#" class="btn small secondary view-project" data-img="Project picture/HomePagePortfolio.jpg">View Project</a>
                </div>
            </div>
        </div>
        
        <div class="project-card">
            <div class="project-image">
                <img src="Project picture/DYCI Navigation Tour System.jpg" alt="Project 2">
            </div>
            <div class="project-info">
                <h3>🔎DYCI Navigation Tour System</h3>
                <p>An Map with interactable features:</p>
                <ul class="project-features">
                    <li>Buildings</li>
                    <li>Map</li>
                    <li>Student log In</li>
                    <li>Detail about the Rooms</li>
                </ul>
                <div class="project-links">
                    <a href="#" class="btn small secondary view-project" data-img="Project picture/DYCI Navigation Tour System.jpg">View Project</a>
                </div>
            </div>
        </div>
        
        <div class="project-card">
            <div class="project-image">
                <img src="Project picture/project3.jpg" alt="Project 3">
            </div>
            <div class="project-info">
                <h3>🏨Barangay Information System </h3>
                <p> This App Based Project help Barangay Official to do less Work. Features include:</p>
                <ul class="project-features">
                    <li>Real-time data updates</li>
                    <li>Record list of Residents</li>
                    <li>Announcement </li>
                    <li>Information About the Barangay</li>
                </ul>
                <div class="project-links">
                    <a href="#" class="btn small secondary view-project" data-img="Project picture/project3.jpg">View Project</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    backdrop-filter: blur(8px);
}

.modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90vh;
    margin: 2% auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal img {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 0 30px rgba(0, 168, 255, 0.3);
}

.close-modal {
    position: absolute;
    top: -40px;
    right: 0;
    color: #fff;
    font-size: 35px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-modal:hover {
    color: var(--primary-color);
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('projectModal');
    const modalImg = document.getElementById('modalImage');
    const closeModal = document.querySelector('.close-modal');
    const viewButtons = document.querySelectorAll('.view-project');

    viewButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const imgSrc = button.getAttribute('data-img');
            modal.style.display = 'block';
            modalImg.src = imgSrc;
            document.body.style.overflow = 'hidden';
        });
    });

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>