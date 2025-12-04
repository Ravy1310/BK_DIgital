    // Fungsi untuk navigasi dari dashboard ke halaman lain
    function navigateToPage(page, menuId) {
        try {
            console.log(`Navigating to: ${page} with menu: ${menuId}`);
            
            // Cek apakah berada dalam iframe/embedded
            if (window.parent && window.parent !== window) {
                // Metode 1: Coba panggil fungsi di parent window
                if (window.parent.handleDashboardNavigation) {
                    window.parent.handleDashboardNavigation(page, menuId);
                    return;
                }
                
                // Metode 2: Coba panggil loadContent di parent
                if (window.parent.loadContent) {
                    // Cari dan aktifkan menu di sidebar parent
                    const parentMenuLinks = window.parent.document.querySelectorAll('.sidebar-menu a');
                    parentMenuLinks.forEach(link => link.classList.remove('active'));
                    
                    const targetMenu = window.parent.document.querySelector(`a[data-js="${menuId}"]`);
                    if (targetMenu) {
                        targetMenu.classList.add('active');
                    }
                    
                    // Load content
                    const jsType = targetMenu ? targetMenu.dataset.js : '';
                    window.parent.loadContent(page, jsType);
                    return;
                }
                
                // Metode 3: Gunakan custom event
                const navEvent = new CustomEvent('dashboardNavigation', {
                    detail: { page: page, menuId: menuId }
                });
                window.parent.document.dispatchEvent(navEvent);
                
                // Beri waktu untuk event diproses
                setTimeout(() => {
                    // Jika masih di halaman yang sama, gunakan fallback
                    if (window.location === window.parent.location) {
                        window.parent.location.href = page;
                    }
                }, 100);
                
            } else {
                // Jika tidak dalam iframe, redirect biasa
                window.location.href = page;
            }
        } catch (error) {
            console.error('Navigation error:', error);
            // Fallback ke redirect biasa
            window.location.href = page;
        }
    }

    // Auto refresh dashboard setiap 60 detik
    setTimeout(function() {
        location.reload();
    }, 60000); // 60 detik

    // Animasi statistik cards
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.card-stat');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Tambahkan event listener untuk pengaduan item
        document.querySelectorAll('.pengaduan-item[href="#"]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                // Navigasi ke detail pengaduan jika ada
                navigateToPage('detail_pengaduan.php', 'pengaduan');
            });
        });
    });