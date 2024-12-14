document.addEventListener('DOMContentLoaded', function () {
  const menuItems = document.querySelectorAll('.menuItem');
  
  // Function to handle the menu item click event
  const updateImage = (item) => {
    const brand = item.getAttribute('data-brand');
    const imageUrl = item.getAttribute('data-image');
    
    // Update the image in the oval using the image URL
    const brandImage = document.getElementById('brandImage');
    brandImage.src = imageUrl;

    // Update the text inside the oval
    const ovalText = document.querySelector('.bg-primary p');
    ovalText.textContent = `Showing ${brand} products`;
  };

  // Add click event listener to each menu item
  menuItems.forEach(item => {
    item.addEventListener('click', function () {
      updateImage(this);  // Update image when a menu item is clicked
    });
  });

  // Auto-click the first menu item if it exists
  if (menuItems.length > 0) {
    menuItems[0].click();  // Trigger the click event on the first menu item
  }
});
