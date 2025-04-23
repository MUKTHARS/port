// Update the contact form submission
document.getElementById('contactForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            form.reset();
            alert('Message sent successfully!');
        } else {
            alert(data.message || 'Failed to send message');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
});

// Update the typed.js initialization to work with dynamic content
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Typed !== 'undefined' && document.querySelector('#typed-strings')) {
        new Typed('#typed-strings ~ .typed', {
            stringsElement: '#typed-strings',
            typeSpeed: 50,
            backSpeed: 30,
            backDelay: 1500,
            startDelay: 1000,
            loop: true
        });
    }
    
    // Update project filtering
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const category = tab.textContent.toLowerCase();
            const projectCards = document.querySelectorAll('.project-card');
            
            projectCards.forEach(card => {
                const projectType = card.querySelector('.project-type').textContent.toLowerCase();
                if (category === 'all' || projectType.includes(category)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
// Loading animation
window.addEventListener('load', () => {
    setTimeout(() => {
        document.querySelector('.loading-animation').classList.add('hidden');
    }, 800);
});

// GSAP animations
document.addEventListener('DOMContentLoaded', () => {
    // Register ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);

    // Animate project cards
    gsap.from('.project-card', {
        y: 50,
        opacity: 0,
        duration: 0.8,
        stagger: 0.2,
        ease: "power3.out",
        scrollTrigger: {
            trigger: '.projects-grid',
            start: 'top 80%',
        }
    });

    // Animate heading
    gsap.from('.projects-title', {
        y: 30,
        opacity: 0,
        duration: 0.6,
        ease: "power3.out",
        scrollTrigger: {
            trigger: '.projects-heading',
            start: 'top 80%',
        }
    });

    gsap.from('.projects-subtitle', {
        y: 30,
        opacity: 0,
        duration: 0.6,
        delay: 0.2,
        ease: "power3.out",
        scrollTrigger: {
            trigger: '.projects-heading',
            start: 'top 80%',
        }
    });

    gsap.from('.filter-tabs', {
        y: 30,
        opacity: 0,
        duration: 0.6,
        delay: 0.3,
        ease: "power3.out",
        scrollTrigger: {
            trigger: '.projects-heading',
            start: 'top 80%',
        }
    });
});

// Filter tabs functionality
const filterTabs = document.querySelectorAll('.filter-tab');
filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        // Remove active class from all tabs
        filterTabs.forEach(t => t.classList.remove('active'));
        // Add active class to clicked tab
        tab.classList.add('active');

        // Add filtering logic here
        // This is just a visual demo
        const category = tab.textContent.toLowerCase();

        // Simple animation for tab switching
        gsap.from('.project-card', {
            scale: 0.95,
            opacity: 0.5,
            duration: 0.4,
            stagger: 0.1,
            ease: "power2.out",
        });
    });
});

// Video modal functionality
const videoButtons = document.querySelectorAll('[data-video]');
const videoModal = document.querySelector('.video-modal');
const videoPlayer = document.querySelector('.modal-video-player');
const closeModal = document.querySelector('.close-modal');

videoButtons.forEach(button => {
    button.addEventListener('click', () => {
        const videoId = button.getAttribute('data-video');
        // In a real implementation, you would set the actual video source here
        // For this demo, we're just setting a placeholder
        videoPlayer.src = `https://www.youtube.com/embed/dQw4w9WgXcQ`;
        videoModal.classList.add('active');
    });
});

closeModal.addEventListener('click', () => {
    videoModal.classList.remove('active');
    // Stop video by clearing src
    setTimeout(() => {
        videoPlayer.src = '';
    }, 300);
});

// Close modal when clicking outside the video container
videoModal.addEventListener('click', (e) => {
    if (e.target === videoModal) {
        closeModal.click();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && videoModal.classList.contains('active')) {
        closeModal.click();
    }
});
