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
            console.error(`Chyba při mazání skupiny ${groupName}: ${error.message}`);
        })
    }
}

function addManagerToGroup(groupId, groupName, managerId, managerName) {
    if (confirm(`Skutečně chcete, aby uživatel ${managerName} spravoval skupinu ${groupName}?`)) {
        fetch(`/api/groups/${groupId}/managers/${managerId}`, {
            method: 'PUT',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se přídat skupinu ${groupName} pro uživatele ${managerName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Skupina ${groupName} byla úspěšně přidána pro uživatele ${managerName}.`);
        }).catch(error => {
            console.error(`Chyba při přidávání skupiny ${groupName}: ${error.message}`);
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
            console.error(`Chyba při mazání skupiny ${groupName}: ${error.message}`);
        })
    }
}

function addMemberToGroup(groupId, groupName, memberId, memberName) {
    if (confirm(`Skutečně chcete, aby uživatel ${memberName} byl členem skupiny ${groupName}?`)) {
        fetch(`/api/groups/${groupId}/members/${memberId}`, {
            method: 'PUT',
            headers: {
                "Content-Type": "application/json"
            },
            body: {}
        }).then(response => {
            if (!response.ok) {
                throw new Error(`Nepodařilo se přidat skupinu ${groupName} pro uživatele ${memberName}: ${response.statusText}`);
            }

            if (response.status === 204) {
                return null;
            }

            console.warn(`Špatný návratový kód: ${response.status}`);
            return null;
        }).then(_ => {
            console.log(`Skupina ${groupName} byla úspěšně přidána pro uživatele ${memberName}.`);
        }).catch(error => {
            console.error(`Chyba při přidávání skupiny ${groupName}: ${error.message}`);
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const addManagerButton = document.getElementById('submit-new-manager-member')
    if (addManagerButton != null) {
        addManagerButton.addEventListener('click', event => {
            event.preventDefault()
            const groupId = document.getElementById('group-form-add').dataset.groupId
            const groupName = document.getElementById('group-form-add').dataset.groupName

            const addManagerForm = document.getElementById('new-group-form')
            const newManagerData = new FormData(addManagerForm)
            const newUserId = newManagerData.get('_user_name')
            const selectedRole = newManagerData.get('_role_option')
            const optionToRemove = document.querySelector(`option[value="${newUserId}"]`)
            const userName = optionToRemove.innerHTML;
            optionToRemove.remove()

            if(selectedRole === '1') {
                addManagerToGroup(groupId, groupName, newUserId, userName);
                reloadManagersTable()
            }
            else {
                addMemberToGroup(groupId, groupName, newUserId, userName);
                reloadMembersTable()
            }
        })
    }

    reloadManagersTable()
    reloadMembersTable()
})

function reloadManagersTable(){
    const groupId = document.getElementById('group-form-add').dataset.groupId
    const groupName = document.getElementById('group-form-add').dataset.groupName

    const managersTableBody = document.getElementById('managers_table_body')
    fetch('/api/group/' + groupId + '/managers', {headers: {"Accept": "application/json"}})
        .then(response => response.json())
        .then(managers => {

            managersTableBody.innerHTML = ''

            if (managers.length === 0) {
                const row = document.createElement('tr')
                row.innerHTML = '<td colSpan="2">Zatím nikdo</td>'
                managersTableBody.appendChild(row)
            } else {
                managers.forEach(manager => {
                    const row = document.createElement('tr')
                    row.innerHTML += `<td>${manager.name}</td>`
                    row.innerHTML += `<td>
                                                      <img class="wide a" src="/images/trash-red.svg" alt="Odebrat správce"
                                                           onclick="deleteManagerFromGroup(${groupId}, '${groupName}',${manager.id}, '${manager.name}')">
                                                  </td>`
                    managersTableBody.appendChild(row)
                })
            }
        })
}

function reloadMembersTable(){
    const groupId = document.getElementById('group-form-add').dataset.groupId
    const groupName = document.getElementById('group-form-add').dataset.groupName

    const managersTableBody = document.getElementById('members_table_body')
    fetch('/api/group/' + groupId + '/members', {headers: {"Accept": "application/json"}})
        .then(response => response.json())
        .then(members => {

            managersTableBody.innerHTML = ''

            if (members.length === 0) {
                const row = document.createElement('tr')
                row.innerHTML = '<td colSpan="2">Zatím nikdo</td>'
                managersTableBody.appendChild(row)
            } else {
                members.forEach(member => {
                    const row = document.createElement('tr')
                    row.innerHTML += `<td>${member.name}</td>`
                    row.innerHTML += `<td>
                                                      <img class="wide a" src="/images/trash-red.svg" alt="Odebrat správce"
                                                           onclick="deleteMemberFromGroup(${groupId}, '${groupName}',${member.id}, '${member.name}')">
                                                  </td>`
                    managersTableBody.appendChild(row)
                })
            }
        })
}

