document.addEventListener('DOMContentLoaded', function() {
    // Navigation between sections
    const navLinks = document.querySelectorAll('.sidebar nav ul li a');
    const sections = document.querySelectorAll('.section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and sections
            navLinks.forEach(l => l.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding section
            const targetId = this.getAttribute('href');
            document.querySelector(targetId).classList.add('active');
        });
    });
    
    // Auto-hide success message after 5 seconds
    const successAlert = document.querySelector('.alert.success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 300);
        }, 5000);
    }
    
    // Form submission handling
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // You can add form validation here if needed
            // e.preventDefault(); // Uncomment to prevent actual submission for AJAX testing
        });
    });
});