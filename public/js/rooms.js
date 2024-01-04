function deleteRoom(roomId, roomName) {
    if (confirm(`Skutečně chcete smazat učebnu ${roomName}?`)) {
        fetch(`/api/rooms/${roomId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat učebnu ${roomName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Učebna ${roomName} byla úspěšně smazána.`);
            document.getElementById(`room-${roomId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání učebny ${roomName}: ${error.message}`)
        })
    }
}

new Modal('new-room-modal', ['new-room-button'], [])