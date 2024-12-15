document.addEventListener('DOMContentLoaded', function () {
  const menuItems = document.querySelectorAll('.menuItem');

  // Function to update the content dynamically
  const updateContent = (item) => {
    const brand = item.getAttribute('data-brand');
    const imageUrl = item.getAttribute('data-image');
    const price = item.getAttribute('data-price');
    const name = item.getAttribute('data-name');

    // Update the image
    const brandImage = document.getElementById('brandImage');
    brandImage.src = imageUrl; // Change the product image

    // Add a fallback for missing or broken images
    brandImage.onerror = () => {
      brandImage.src = 'path/to/fallback-image.jpg'; // Replace with a fallback image
    };

    // Update the price
    const priceElement = document.getElementById('productPrice');
    priceElement.textContent = price;

    // Update the product title
    const productTitle = document.getElementById('productTitle');
    productTitle.innerHTML = `${name} <span style="color: red;">NEW!</span>`;

    // Update the product description
    const productDesc = document.getElementById('productDesc');
    productDesc.innerHTML = `This is the ${name}. Additional product details can go here.`;

    // Optionally, update other details like available colors, etc.
    const productColors = document.getElementById('productColors');
    productColors.innerHTML = ''; // Clear the existing color options

    // Add color options dynamically (you can adjust this as needed)
    for (let i = 0; i < 3; i++) {
      const colorDiv = document.createElement('div');
      colorDiv.classList.add('color');
      productColors.appendChild(colorDiv);
    }

    // Highlight the selected menu item
    menuItems.forEach(i => i.classList.remove('active'));
    item.classList.add('active');
  };

  // Add click event listener to each menu item
  menuItems.forEach(item => {
    item.addEventListener('click', function () {
      updateContent(this);
    });
  });

  // Optionally, auto-click the first menu item if there are any
  if (menuItems.length > 0) {
    menuItems[0].click(); // Trigger click on the first item on page load
  }
});
