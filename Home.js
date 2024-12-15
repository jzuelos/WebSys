document.addEventListener('DOMContentLoaded', function () {
  const menuItems = document.querySelectorAll('.menuItem');

  const updateContent = (item) => {
    const brand = item.getAttribute('data-brand');
    const imageUrl = item.getAttribute('data-image');
    const price = item.getAttribute('data-price');
    const name = item.getAttribute('data-name');
    const description = item.getAttribute('data-desc');

    // Select elements to update
    const brandImage = document.getElementById('brandImage');
    const productImage = document.getElementById('productImage');
    const productDesc = document.getElementById('productDesc');
    const priceElement = document.getElementById('price');
    const productPrice = document.getElementById('productPrice');
    const bomxText = document.getElementById('bomxText');
    const productName = document.getElementById('productTitle');

    // Fade out current content
    const fadeOut = (element) => {
      element.style.transition = 'opacity 0.5s';
      element.style.opacity = 0;
    };

    // Fade in the new content
    const fadeIn = (element) => {
      element.style.transition = 'opacity 0.3s';
      element.style.opacity = 1;
    };

    // Apply fade out and fade in to each element
    fadeOut(brandImage);
    fadeOut(productImage);
    fadeOut(productDesc);
    fadeOut(priceElement);
    fadeOut(productPrice);
    fadeOut(bomxText);
    fadeOut(productName);

    // After the fade out, update the content and fade them back in
    setTimeout(() => {
      // Update the content
      brandImage.src = imageUrl;
      productImage.src = imageUrl;
      productDesc.textContent = description;
      priceElement.textContent = price;
      productPrice.textContent = price;
      bomxText.innerHTML = `${name} `;
      productName.innerHTML = `${name}`;

      // Fade in the updated content
      fadeIn(brandImage);
      fadeIn(productImage);
      fadeIn(productDesc);
      fadeIn(priceElement);
      fadeIn(productPrice);
      fadeIn(bomxText);
      fadeIn(productName);
    }, 500); // Wait for the fade-out to complete before updating

    const buyNowButton = document.getElementById('buyNowButton');
    const productDetailsSection = document.getElementById('productDetails');

    buyNowButton.addEventListener('click', function () {
      // Scroll to the product details section
      productDetailsSection.scrollIntoView({ behavior: 'smooth' });
    });
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

  fetch('home.php')
    .then(response => response.text()) // Get the response as text
    .then(data => {
      // Parse the plain HTML response to check the login status
      const isLoggedInElement = new DOMParser().parseFromString(data, 'text/html');
      const isLoggedIn = isLoggedInElement.querySelector('#isLoggedIn').textContent;

      // Use the login status to handle the button logic
      const buyNowBtn = document.getElementById('buyNowBtn');
      if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function () {
          if (isLoggedIn === 'false') {
            // Redirect to the login page if not logged in
            window.location.href = 'userreg.php';
          } else {
            // Proceed with the buy action
            console.log('Proceeding with purchase...');
          }
        });
      }
    })
    .catch(error => {
      console.error('Error fetching login status:', error);
    });
});