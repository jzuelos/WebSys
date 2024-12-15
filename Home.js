document.addEventListener('DOMContentLoaded', function () {
  const menuItems = document.querySelectorAll('.menuItem');

  const updateContent = (item) => {
    const brand = item.getAttribute('data-brand');
    const imageUrl = item.getAttribute('data-image');
    const price = item.getAttribute('data-price');
    const name = item.getAttribute('data-name');

    // Update the image in the oval using the image URL
    const brandImage = document.getElementById('brandImage');
    brandImage.src = imageUrl;

    // Add fallback for missing or broken images
    brandImage.onerror = () => {
      brandImage.src = 'path/to/fallback-image.jpg'; // Replace with your fallback image
    };

    // Update the price
    const priceElement = document.getElementById('price');
    priceElement.textContent = price;

    // Update the BOM X text
    const bomxText = document.getElementById('bomxText');
    bomxText.innerHTML = `${name} <span style="color: red;">NEW!</span>`;

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

  // Auto-click the first menu item if it exists
  if (menuItems.length > 0) {
    menuItems[0].click();
  }
});
