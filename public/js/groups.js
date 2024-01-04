function deleteGroup(groupId, groupName) {
    if (confirm(`Skutečně chcete smazat skupinu ${groupName}?`)) {
        fetch(`/api/groups/${groupId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat skupinu ${groupName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Skupina ${groupName} byla úspěšně smazána.`);
            document.getElementById(`group-${groupId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání skupiny ${groupName}: ${error.message}`)
        })
    }
}

new Modal('new-group-modal', ['new-group-button'], [])