const urlParams = new URLSearchParams(window.location.search)
const userId = urlParams.get('userId')

document.getElementById('addUserForm').addEventListener('submit', function(e){
  e.preventDefault()

  const firstName = document.getElementById('firstName').value
  const lastName = document.getElementById('lastName').value
  const email = document.getElementById('email').value
  const phone = document.getElementById('phone').value

  if(!firstName || !lastName || !email || !phone){
    document.getElementById('message').textContent = 'All fields are required'
    return
  }

  fetch('AddContact.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId: userId, firstName: firstName, lastName: lastName, email: email, phone: phone })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = data.error
    }
    else{
      document.getElementById('message').textContent = 'Successfully added new contact'

      // redirect to dashboard.html after successful add
      window.location.href = `dashboard.html?userId=${userId}`
    }
  })
  .catch(error => console.error('Error: ', error))

})
