
// Retrieve the value of the 'success' query parameter from the URL
const urlParams = new URLSearchParams(window.location.search);
const Param = urlParams.get('success');

window.onload = function() {
    var urlParams = new URLSearchParams(window.location.search);
    const Param = urlParams.get('success');

    if (Param === '1') {
      var messageContainer = document.getElementById("messageContainer");
      messageContainer.textContent = 'Invalid Verification Code';
      messageContainer.style.display = "block";
      messageContainer.classList.add("message-container");
      
      setTimeout(function() {
        
        messageContainer.style.display = "none";
        messageContainer.classList.remove("message-container");
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);    }, 5000);
    }

    if (Param === '2') {
      var messageContainer = document.getElementById("messageContainer");
      messageContainer.textContent = 'User not found';
      messageContainer.style.display = "block";
      messageContainer.classList.add("message-container");
      
      setTimeout(function() {
        
        messageContainer.style.display = "none";
        messageContainer.classList.remove("message-container");
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);    }, 5000);
    }
}

const home=document.getElementById('home');
home.addEventListener('click', function (event) {
  event.preventDefault()
    window.location.href="../homepage/customerHomepage.php"
  })
 
  
  document.addEventListener('keydown', function(event) {
    const inputs = document.querySelectorAll('input[type="text"]');
    const current = document.activeElement;
    const currentIndex = Array.from(inputs).indexOf(current);

    // Check if the current element is one of the inputs and move focus accordingly
    if (currentIndex !== -1) {
        // Move focus to the next input on right arrow ("ArrowRight")
        if (event.key === "ArrowRight" && currentIndex < inputs.length - 1) {
            event.preventDefault(); // Prevent default behavior of arrow key
            inputs[currentIndex + 1].focus();
        }
        // Move focus to the previous input on left arrow ("ArrowLeft")
        else if (event.key === "ArrowLeft" && currentIndex > 0) {
            event.preventDefault(); // Prevent default behavior of arrow key
            inputs[currentIndex - 1].focus();
        }
    }
});