function deleteRoom(roomId, roomName) {
    if (confirm(`Skutečně chcete smazat místnost ${roomName}?`)) {
        fetch(`/api/rooms/${roomId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat místnost ${roomName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Místnost ${roomName} byla úspěšně smazána.`);
            document.getElementById(`room-${roomId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání místnosti ${roomName}: ${error.message}`)
        })
    }
}