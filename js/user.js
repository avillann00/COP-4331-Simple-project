document.getElementById('logout').addEventListener('click', function(e){
  e.preventDefault()

  fetch('/LAMPAPI/Logout.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
  })
  .then(response => response.json())
  .then(data => {
    window.location.href = 'login.html'
  })
  .catch(error => console.error('Error logging out: ', error))
})

document.getElementById('deleteAccount').addEventListener('click', function(e){
  e.preventDefault()

  const password = prompt('Enter your password to confirm account deletion')

  if(!password){
    document.getElementById('message').textContent = 'Account deletion canceled'
    return
  }

  fetch('/LAMPAPI/Deregister.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ password })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = data.error
    }
    else{
      document.getElementById('message').textContent = 'Account successfully deleted'
      window.location.href = 'register.html'
    }
  })
  .catch(error => {
    console.error('Error deleting account: ', error)
    document.getElementById('message').textContent = error
  })
})
