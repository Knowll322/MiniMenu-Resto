// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            const badges = document.querySelectorAll('.cart-count');
            badges.forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            });
        })
        .catch(error => console.error('Error:', error));
}

// Add to cart animation
function addToCart(button) {
    const originalText = button.textContent;
    button.textContent = '✓ Ditambahkan';
    button.disabled = true;
    
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
    }, 1000);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const requiredFields = form.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            return false;
        }
    }
    
    return true;
}

// Confirm action
function confirmAction(message) {
    return confirm(message);
}

// Place order
function placeOrder() {
    const customerName = document.getElementById('customer_name').value;
    const notes = document.getElementById('notes').value;
    
    if (!customerName.trim()) {
        alert('Silakan masukkan nama pemesan!');
        return;
    }
    
    const formData = new FormData();
    formData.append('customer_name', customerName);
    formData.append('notes', notes);
    
    fetch('order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'bill.php?order_id=' + data.order_id;
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pesanan');
    });
}

// Kitchen display functions
function updateCurrentTime() {
    const now = new Date();
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = now.toLocaleTimeString('id-ID');
    }
}

function startCountdown() {
    let countdown = 30;
    const countdownElement = document.getElementById('countdown');
    
    if (!countdownElement) return;
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            location.reload();
        }
    }, 1000);
}

// Initialize kitchen display
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('current-time')) {
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
        startCountdown();
    }
});