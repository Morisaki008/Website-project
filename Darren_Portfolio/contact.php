<?php include 'includes/header.php'; ?>

<section class="contact">
    <h2 class="section-title">CONTACT</h2>
    <div class="contact-content">
        <div class="contact-info">
            <div class="info-card">
                <h3>Get in touch via email or social media. I'd love to collaborate or chat about exciting opportunities!</h3>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p><a href="mailto:darrenmamuad24@gmail.com">darrenmamuad24@gmail.com</a></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <p>+63 912 345 6789</p>
                </div>
                <div class="social-links">
                    <a href="https://www.facebook.com/Darren.01z/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/darren0248/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://github.com/Darren01z" target="_blank"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>

        <div class="form-container">
            <div id="alert-message" class="alert" style="display: none;"></div>
            <form id="contact-form" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">
                    <span class="btn-text">SEND MESSAGE</span>
                    <div class="spinner" style="display: none;"></div>
                </button>
            </form>
        </div>
    </div>
</section>

<style>
.contact {
    padding: 4rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--text-light);
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--text-light);
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.contact-item i {
    font-size: 1.2rem;
    color: var(--primary-color);
    width: 20px;
    display: flex;
    justify-content: center;
}

.contact-item p {
    margin: 0;
    display: flex;
    align-items: center;
}

.contact-item a {
    color: var(--text-light);
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-item a:hover {
    color: var(--primary-color);
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.social-links a {
    color: var(--text-light);
    font-size: 1.5rem;
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: var(--primary-color);
}

.contact-form {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    color: var(--text-light);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    background: rgba(255, 255, 255, 0.1);
}

.form-group textarea {
    height: 150px;
    resize: vertical;
}

.submit-btn {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: var(--text-light);
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    background: #357abd;
    transform: translateY(-2px);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.alert {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 1.2rem 2.5rem;
    border-radius: 12px;
    z-index: 1000;
    font-weight: 500;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.3s ease-out, fadeOut 0.3s ease-out 4.7s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert.success {
    background: #4BB543;
    color: white;
    border: none;
}

.alert.success::before {
    content: '✓';
    font-weight: bold;
}

.alert.error {
    background: #ff4d4d;
    color: white;
    border: none;
}

@keyframes slideDown {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
    }
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
    }
    
    .contact {
        padding: 2rem 1rem;
    }
}
</style>

<script>
const form = document.getElementById('contact-form');
const alertMessage = document.getElementById('alert-message');
const submitBtn = form.querySelector('.submit-btn');
const btnText = submitBtn.querySelector('.btn-text');
const spinner = submitBtn.querySelector('.spinner');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Disable form submission and show spinner
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    spinner.style.display = 'block';
    
    const formData = new FormData(form);
    
    fetch('process_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alertMessage.className = `alert ${data.status}`;
        alertMessage.textContent = data.message;
        alertMessage.style.opacity = '1';
        alertMessage.style.display = 'block';
        
        if (data.status === 'success') {
            form.reset();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertMessage.className = 'alert error';
        alertMessage.textContent = 'An error occurred. Please try again.';
        alertMessage.style.opacity = '1';
        alertMessage.style.display = 'block';
    })
    .finally(() => {
        // Re-enable form and hide spinner
        submitBtn.disabled = false;
        btnText.style.display = 'block';
        spinner.style.display = 'none';
        
        // Alert will automatically fade out due to CSS animation
    });
});
</script>

<?php include 'includes/footer.php'; ?>