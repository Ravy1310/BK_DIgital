// assets/js/tes/tesbk.js

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi carousel dengan touch support
    initCarousels();
    
    // Tambahkan efek hover yang lebih smooth
    initCardHoverEffects();
});

/**
 * Fungsi untuk menginisialisasi carousel
 */
function initCarousels() {
    // Carousel Tes BK
    const carouselTesBK = document.getElementById('carouselTesBK');
    if (carouselTesBK) {
        initCarouselTouchSupport(carouselTesBK);
    }
    
    // Carousel Riwayat
    const carouselRiwayat = document.getElementById('carouselRiwayat');
    if (carouselRiwayat) {
        initCarouselTouchSupport(carouselRiwayat);
    }
}

/**
 * Fungsi untuk menambahkan touch support pada carousel
 */
function initCarouselTouchSupport(carouselElement) {
    // Hanya aktifkan jika carousel memiliki lebih dari 1 slide
    const carouselItems = carouselElement.querySelectorAll('.carousel-item');
    if (carouselItems.length <= 1) return;
    
    // Inisialisasi carousel Bootstrap
    const carousel = new bootstrap.Carousel(carouselElement, {
        interval: false,
        wrap: true
    });
    
    // Tambahkan touch/swipe support untuk mobile
    let startX = 0;
    let endX = 0;
    const threshold = 50; // Minimum swipe distance
    
    carouselElement.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
    }, { passive: true });
    
    carouselElement.addEventListener('touchmove', function(e) {
        endX = e.touches[0].clientX;
    }, { passive: true });
    
    carouselElement.addEventListener('touchend', function() {
        const diff = startX - endX;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                // Swipe kiri, next slide
                carousel.next();
            } else {
                // Swipe kanan, prev slide
                carousel.prev();
            }
        }
    });
    
    // Auto-hide carousel controls di mobile
    if (window.innerWidth <= 768) {
        setTimeout(() => {
            const controls = carouselElement.querySelectorAll('.carousel-control-prev, .carousel-control-next');
            controls.forEach(control => {
                if (control) {
                    control.style.opacity = '0.6';
                    control.style.transition = 'opacity 0.3s ease';
                }
            });
        }, 3000);
    }
}

/**
 * Fungsi untuk menginisialisasi efek hover pada card
 */
function initCardHoverEffects() {
    const cards = document.querySelectorAll('.test-card, .riwayat-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    });
}

/**
 * Fungsi untuk menampilkan notifikasi
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNote = document.querySelector('.custom-notification');
    if (existingNote) {
        existingNote.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `custom-notification alert alert-${type} alert-dismissible fade show`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Handle session messages
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showNotification('Tes berhasil disubmit!', 'success');
    }
    
    // Remove success parameter from URL
    if (window.history.replaceState && urlParams.has('success')) {
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});

// Handle back button confirmation
const backButton = document.querySelector('.btn-kembali');
if (backButton) {
    backButton.addEventListener('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin keluar?')) {
            e.preventDefault();
        }
    });
}