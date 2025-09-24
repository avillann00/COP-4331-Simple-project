function runSearch(){
  const input = document.getElementById('search')
  const search = input ? input.value.trim() : ''
  document.getElementById('message').textContent = ''

  fetch('/LAMPAPI/SearchContact.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ search })
  })
  .then(response => response.json())
  .then(data => {
    if(!data.error){
      // result boxes
      const firstNameBox = document.getElementById('first-name-box')
      const lastNameBox = document.getElementById('last-name-box')
      const emailBox = document.getElementById('email-box')
      const phoneBox = document.getElementById('phone-box')
      const deleteBox = document.getElementById('delete-box')

      firstNameBox.textContent = ''
      lastNameBox.textContent = ''
      emailBox.textContent = ''
      phoneBox.textContent = ''
      deleteBox.textContent = ''

      data.results.forEach(contact => {
        // create result divs
        const firstNameDiv = document.createElement('div')
        const lastNameDiv = document.createElement('div')
        const emailDiv = document.createElement('div')
        const phoneDiv = document.createElement('div')
        const changeDiv = document.createElement('div')
        const deleteButton = document.createElement('button')
        const editButton = document.createElement('button')

        // add corresponding info
        firstNameDiv.textContent = contact.firstName
        lastNameDiv.textContent = contact.lastName
        emailDiv.textContent = contact.email
        phoneDiv.textContent = contact.phone
        deleteButton.textContent = 'X'
        editButton.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" 
               fill="none" 
               viewBox="0 0 24 24" 
               stroke-width="1.5" 
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" 
                  d="m16.862 4.487 1.687-1.688a1.875 
                     1.875 0 1 1 2.652 2.652L6.832 
                     19.82a4.5 4.5 0 0 1-1.897 
                     1.13l-2.685.8.8-2.685a4.5 
                     4.5 0 0 1 1.13-1.897L16.863 
                     4.487Zm0 0L19.5 7.125" />
          </svg>
        `

        deleteButton.classList.add('delete-btn')
        // add the delete functionalify
        deleteButton.addEventListener('click', () => {
          const confirmed = confirm(`Are you sure you want to delete ${contact.firstName} ${contact.lastName}`)

          if(!confirmed){
            return
          }

          fetch('/LAMPAPI/RemoveContact.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ firstName: contact.firstName, lastName: contact.lastName, phone: contact.phone, email: contact.email })
          })
          .then(response => response.json())
          .then(data => {
            if(!data.error){
              firstNameDiv.remove()
              lastNameDiv.remove()
              emailDiv.remove()
              phoneDiv.remove()
              deleteButton.remove()
            }
            else{
              document.getElementById('message').textContent = data.error
            }
          })
          .catch(error => console.error('Error deleting contact: ', error))

          location.reload()
        })

        editButton.classList.add('edit-btn')
        editButton.addEventListener('click', () => {
          window.location.href = `add.html?firstName=${encodeURIComponent(contact.firstName)}&lastName=${encodeURIComponent(contact.lastName)}&phone=${encodeURIComponent(contact.phone)}&email=${encodeURIComponent(contact.email)}`
        })

        // add divs to thier result boxes
        firstNameBox.appendChild(firstNameDiv)
        lastNameBox.appendChild(lastNameDiv)
        emailBox.appendChild(emailDiv)
        phoneBox.appendChild(phoneDiv)

        changeDiv.style.display = 'flex'
        changeDiv.style.gap = '0.25rem'
        changeDiv.appendChild(deleteButton)
        changeDiv.appendChild(editButton)
        deleteBox.appendChild(changeDiv)
      })
    }
    else{
      document.getElementById('first-name-box').textContent = 'Error'
      document.getElementById('last-name-box').textContent = 'Error'
      document.getElementById('email-box').textContent = 'Error'
      document.getElementById('phone-box').textContent = 'Error'
    }
  })
  .catch(error => console.error('Error getting contact: ', error))
}

document.getElementById('searchBox').addEventListener('input', function(e){
  e.preventDefault()

  runSearch()
})

document.addEventListener('DOMContentLoaded', function(){
  runSearch()
})
