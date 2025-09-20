const urlParams = new URLSearchParams(window.location.search)
const userId = urlParams.get('userId')

if(document.querySelector('nav a[href="add.html"]') && userId){
  document.querySelector('nav a[href="add.html"]').href = `add.html?userId=${encodeURIComponent(userId)}`
}

document.getElementById('searchBox').addEventListener('input', function(e){
  e.preventDefault()

  search = document.getElementById('search').value
  document.getElementById('message').textContent = ''

  if(search.trim() === ''){
    document.getElementById('first-name-box').textContent = ''
    document.getElementById('last-name-box').textContent = ''
    document.getElementById('email-box').textContent = ''
    document.getElementById('phone-box').textContent = ''
    document.getElementById('delete-box').textContent = ''
    document.getElementById('message').textContent = ''

    return
  }

  fetch('/LAMPAPI/SearchContact.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId, search })
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
        const deleteButton = document.createElement('button')

        // add corresponding info
        firstNameDiv.textContent = contact.firstName
        lastNameDiv.textContent = contact.lastName
        emailDiv.textContent = contact.email
        phoneDiv.textContent = contact.phone
        deleteButton.textContent = 'X'

        deleteButton.classList.add('delete-btn')
        // add the delete functionalify
        deleteButton.addEventListener('click', () => {
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
        })

        // add divs to thier result boxes
        firstNameBox.appendChild(firstNameDiv)
        lastNameBox.appendChild(lastNameDiv)
        emailBox.appendChild(emailDiv)
        phoneBox.appendChild(phoneDiv)
        deleteBox.appendChild(deleteButton)
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
})
