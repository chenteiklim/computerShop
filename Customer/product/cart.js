
var homeButton = document.getElementById("home");

homeButton.addEventListener("click", function(event) {
  // Perform the navigation action here
  event.preventDefault()
  window.location.href = "../homepage/mainpage.php";
});

var checkOutBtn = document.getElementById("checkOutBtn");

checkOutBtn.addEventListener("click", function(event) {
  // Perform the navigation action here
  event.preventDefault()
  window.location.href = "../order/checkOut.php";
});

window.onload = function() {
  var urlParams = new URLSearchParams(window.location.search);
  const message2 = urlParams.get('message2');

  if (message2) {
    var messageContainer = document.getElementById("messageContainer");
    messageContainer.textContent = decodeURIComponent(message2); // Decode the URL-encoded message
    messageContainer.style.display = "block";
    messageContainer.classList.add("message-container");
    
    setTimeout(function() {
      messageContainer.style.display = "none";
      messageContainer.classList.remove("message-container");
      
      // Clear the message from the URL
      const url = new URL(window.location);
      url.searchParams.delete('message2');
      window.history.replaceState({}, document.title, url);
    }, 3000);
  }
}