function deleteManagerFromGroup(groupId, groupName, managerId, managerName) {
    if (confirm(`Skutečně chcete pro uživatele ${managerName} smazat skupinu ${groupName}?`)) {
        fetch(`/api/groups/${groupId}/managers/${managerId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat skupinu ${groupName} pro uživatele ${managerName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Skupina ${groupName} byla úspěšně smazána z uživatele ${managerName}.`);
            document.getElementById(`manager-${managerId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání skupiny ${groupName}: ${error.message}`)
        })
    }
}

function deleteMemberFromGroup(groupId, groupName, memberId, memberName) {
    if (confirm(`Skutečně chcete pro uživatele ${memberName} smazat skupinu ${groupName}?`)) {
        fetch(`/api/groups/${groupId}/members/${memberId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat skupinu ${groupName} pro uživatele ${memberName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Skupina ${groupName} byla úspěšně smazána z uživatele ${memberName}.`);
            document.getElementById(`member-${memberId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání skupiny ${groupName}: ${error.message}`)
        })
    }
}

new Modal('new-user-modal', ['new-member-button', 'new-manager-button'], [])