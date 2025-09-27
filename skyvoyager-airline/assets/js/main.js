document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'red';
                    valid = false;
                    
                    // Add error message if not already present
                    if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = 'red';
                        errorMsg.style.fontSize = '0.8rem';
                        errorMsg.style.display = 'block';
                        errorMsg.style.marginTop = '0.25rem';
                        errorMsg.textContent = 'This field is required';
                        input.insertAdjacentElement('afterend', errorMsg);
                    }
                } else {
                    input.style.borderColor = '';
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = form.querySelector('input[required]:invalid, select[required]:invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
    
    // Flight card hover effect enhancement
    const flightCards = document.querySelectorAll('.flight-card');
    flightCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.15)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'var(--shadow)';
        });
    });
    
    // Mobile menu toggle (for responsive design)
    const menuToggle = document.createElement('button');
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    menuToggle.className = 'menu-toggle';
    menuToggle.style.display = 'none';
    menuToggle.style.background = 'none';
    menuToggle.style.border = 'none';
    menuToggle.style.color = 'white';
    menuToggle.style.fontSize = '1.5rem';
    menuToggle.style.cursor = 'pointer';
    
    const header = document.querySelector('header');
    header.insertBefore(menuToggle, header.firstChild);
    
    const nav = document.querySelector('nav ul');
    
    function toggleMenu() {
        if (nav.style.display === 'flex') {
            nav.style.display = 'none';
        } else {
            nav.style.display = 'flex';
        }
    }
    
    menuToggle.addEventListener('click', toggleMenu);
    
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            menuToggle.style.display = 'block';
            nav.style.display = 'none';
        } else {
            menuToggle.style.display = 'none';
            nav.style.display = 'flex';
        }
    }
    
    window.addEventListener('resize', checkScreenSize);
    checkScreenSize();
});