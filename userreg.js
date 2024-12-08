document.addEventListener('DOMContentLoaded', function () {
    // Automatically remove error alert after 3 seconds
    setTimeout(function () {
        var errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 3000);

    // Automatically remove success alert after 3 seconds
    setTimeout(function () {
        var successAlert = document.getElementById('success-alert');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 3000);
});
