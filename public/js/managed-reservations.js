function deleteReservation(reservationId, reservationRoom, reservationStart) {
    if (confirm(`Skutečně chcete smazat rezervaci do učebny ${reservationRoom} na čas ${reservationStart}?`)) {
        fetch(`/api/reservations/${reservationId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se smazat rezervaci ${reservationRoom}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Rezervace do učebny ${reservationRoom} na čas ${reservationStart} byla úspěšně smazána.`);
            document.getElementById(`reservation-${reservationId}`).remove();
        }).catch(error => {
            console.error(`Chyba při mazání rezervace ${reservationRoom}: ${error.message}`)
        })
    }
}