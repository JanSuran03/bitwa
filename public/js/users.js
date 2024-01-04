document.getElementById('addUser')?.addEventListener('submit', function (event) {
        let password = document.getElementById('password').value;
        let confirm_password = document.getElementById('confirm_password').value;

        if (password !== confirm_password) {
            alert('Hesla se neshodují.');
            event.preventDefault();
        }
    }
)

function deleteUser(userId, userName) {
    if (confirm(`Skutečně chcete smazat uživatele ${userName}?`)) {
        fetch(`/api/users/${userId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat uživatele ${userName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Uživatel ${userName} byl úspěšně smazán.`);
            document.getElementById(`user-${userId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání uživatele ${userName}: ${error.message}`)
        })
    }
}

new Modal('new-user-modal', ['new-user-button'], [])