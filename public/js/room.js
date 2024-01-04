function reloadManagersTable() {
    const roomId = document.getElementById('top-block').dataset.roomId
    const managersTableBody = document.getElementById('managers-table-body')
    fetch('/api/rooms/' + roomId + '/managers', {headers: {"Accept": "application/json"}})
        .then(response => response.json())
        .then(managers => {

            managersTableBody.innerHTML = ''

            if (managers.length === 0) {
                const row = document.createElement('tr')
                row.innerHTML = '<td colSpan="2">Žádný správce</td>'
                managersTableBody.appendChild(row)
            } else {
                managers.forEach(manager => {
                    const row = document.createElement('tr')
                    row.innerHTML += `<td>${manager.name}</td>`
                    row.innerHTML += `<td>
                                          <button class="danger" onclick="deleteManagerFromRoom(${manager.id}, '${manager.name}')">
                                              <img class="wide a" src="/images/trash-red.svg" alt="Odebrat správce">
                                              <span>Odebrat</span>
                                          </button>
                                      </td>`
                    managersTableBody.appendChild(row)
                })
            }

            managersTableBody.innerHTML +=
                `<tr>
                    <td colspan="2" class="center"><a id="new-manager-button" href="#">Přidat správce</a></td>
                </tr>`

            new Modal('new-manager-modal', ['new-manager-button'], ['new-manager-save-button'])
        })
}

function deleteManagerFromRoom(managerId, managerName) {
    const roomId = document.getElementById('top-block').dataset.roomId
    const roomName = document.getElementById('top-block').dataset.roomName
    if (confirm(`Skutečně chcete odebrat uživateli ${managerName} správcovská práva pro učebnu ${roomName}?`)) {
        fetch(`/api/rooms/${roomId}/managers/${managerId}`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se odebrat uživateli ${managerName} správcovská práva pro učebnu ${roomName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                const newManagerSelect = document.getElementById("new-manager")
                const newOption = document.createElement('option')
                newOption.setAttribute("value", managerId)
                newOption.innerHTML = managerName
                newManagerSelect.appendChild(newOption)
                return;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return;
        }).then(_ => {
            console.log(`Uživateli ${managerName} byla úspěšně odebrána správcovská práva pro učebnu ${roomName}.`);
            reloadManagersTable()
        }).catch(error => {
            console.error(`Chyba při odebírání správcovských práv uživatele ${managerName} pro učebnu ${roomName}: ${error.message}`)
        })
    }
}

function addRoomManager(roomId, managerId) {
    return fetch(`/api/rooms/${roomId}/managers/${managerId}`, {method: 'PUT'})
}


document.addEventListener('DOMContentLoaded', () => {
    const addManagerButton = document.getElementById('new-manager-save-button')
    if (addManagerButton != null) {
        addManagerButton.addEventListener('click', event => {
            event.preventDefault()
            const roomId = document.getElementById('top-block').dataset.roomId
            const addManagerForm = document.getElementById('new-manager-form')
            const newManagerData = new FormData(addManagerForm)
            const newManagerId = newManagerData.get('new-manager')
            const optionToRemove = document.querySelector(`option[value="${newManagerId}"]`)
            optionToRemove.remove()
            addRoomManager(roomId, newManagerId)
                .then(() => reloadManagersTable())
        })
    }

    new Modal('change-room-name-modal', ['change-room-name-button'], [])
    new Modal('change-building-name-modal', ['change-building-name-button'], [])
    new Modal('change-public-modal', ['change-public-button'], [])
    new Modal('change-group-modal', ['change-group-button'], [])

    reloadManagersTable()
})
