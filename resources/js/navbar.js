document.getElementById('logout-link').addEventListener('click', function(e) {
    e.preventDefault();
    try {
        var myModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        myModal.show();
    } catch (error) {
        console.error('Error initializing modal: ', error);
    }
});

document.getElementById('confirm-logout-btn').addEventListener('click', function() {
    document.getElementById('logout-form').submit();
});

document.addEventListener('hidden.bs.modal', function (event) {
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
});