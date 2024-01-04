document.getElementById('changePassword').addEventListener('submit', function (event) {
        let password = document.getElementById('new-password').value;
        let confirm_password = document.getElementById('confirm-password').value;

        if (password !== confirm_password) {
            alert('Hesla se neshodují.');
            event.preventDefault();
        }
    }
)