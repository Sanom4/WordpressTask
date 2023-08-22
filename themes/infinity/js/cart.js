(function () {
  // Add to Cart Interaction - by CodyHouse.co
  var cart = document.getElementsByClassName("js-cd-cart");
  if (cart.length > 0) {
    var cartAddBtns = document.getElementsByClassName("js-cd-add-to-cart"),
      cartBody = cart[0].getElementsByClassName("cd-cart__body")[0],
      cartList = cartBody.getElementsByTagName("ul")[0],
      cartListItems = cartList.getElementsByClassName("cd-cart__product"),
      cartTotal = cart[0]
        .getElementsByClassName("cd-cart__checkout")[0]
        .getElementsByTagName("span")[0],
      cartCount = cart[0].getElementsByClassName("cd-cart__count")[0],
      cartCountItems = cartCount.getElementsByTagName("li"),
      productId = cartList.getAttribute("data-id"),
      cartTimeoutId = false,
      animatingQuantity = false;
    initCartEvents();

function initCartEvents() {
  // add products to cart
  for (var i = 0; i < cartAddBtns.length; i++) {
    (function (i) {
      cartAddBtns[i].addEventListener("click", addToCart);
    })(i);
  }

  // open/close cart
  cart[0]
    .getElementsByClassName("cd-cart__trigger")[0]
    .addEventListener("click", function (event) {
      event.preventDefault();
      toggleCart();
    });

  cart[0].addEventListener("click", function (event) {
    if (event.target == cart[0]) {
      // close cart when clicking on bg layer
      toggleCart(true);
    } else if (event.target.closest(".cd-cart__delete-item")) {
      // remove product from cart
      event.preventDefault();
      removeProduct(event.target.closest(".cd-cart__product"));
    }
  });

  // update product quantity inside cart
  cart[0].addEventListener("input", function (event) {
    if (event.target.className == "cd-cart__select-quantity") {
      quickUpdateCart();
    }
  });
}

    // Fetch cart items and update the cart UI on page load
    window.addEventListener("load", function () {
      updateCartUI();
    });

function updateCartUI() {
  fetch("/wp-admin/admin-ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "action=get_cart_items",
  })
    .then((response) => response.json())
    .then((data) => {
      // Remove existing cart items
      while (cartList.firstChild) {
        cartList.removeChild(cartList.firstChild);
      }

      // Add fetched cart items to the cart
      data.forEach((item) => {
        addProduct(item);
      });

      // Update the cart count and total
      quickUpdateCart();

      // Check if cart is empty, if not, show the cart UI
      if (Number(cartCountItems[0].innerText) > 0)
        Util.removeClass(cart[0], "cd-cart--empty");
    });
}

function addToCart(event) {
  event.preventDefault();
  if (animatingQuantity) return;

  var productID = this.getAttribute("data-id");
  var quantity = 1; // Set your quantity here, or retrieve it from an input field

  // Check if product already exists in cart
  var existingProduct = cartList.querySelector(
    `.cd-cart__product[data-id="${productID}"]`
  );
  if (existingProduct) {
    // Product already exists, update quantity
    var quantityInput = existingProduct.querySelector(
      ".cd-cart__select-quantity"
    );
    var currentQuantity = parseInt(quantityInput.value, 10);
    quantityInput.value = currentQuantity + quantity;
    quickUpdateCart();
  } else {
    // Product does not exist in cart, add it
    // Make an AJAX request to add the product to the cart
    fetch("/wp-admin/admin-ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body:
        "action=add_to_cart&product_id=" + productID + "&quantity=" + quantity,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update the cart UI
          var cartIsEmpty = Util.hasClass(cart[0], "cd-cart--empty");
          addProduct(data.data.product);
          updateCartCount(cartIsEmpty);
          updateCartTotal(data.data.total, true);
          Util.removeClass(cart[0], "cd-cart--empty");
        } else {
          console.error("Error adding product to cart:", data.error);
        }
      });
  }
}

    function toggleCart(bool) {
      // toggle cart visibility
      var cartIsOpen =
        typeof bool === "undefined"
          ? Util.hasClass(cart[0], "cd-cart--open")
          : bool;

      if (cartIsOpen) {
        Util.removeClass(cart[0], "cd-cart--open");
        // reset undo
        if (cartTimeoutId) clearInterval(cartTimeoutId);
        removePreviousProduct(); // if a product was deleted, remove it definitively from the cart

        setTimeout(function () {
          cartBody.scrollTop = 0;
          // check if cart empty to hide it
          if (Number(cartCountItems[0].innerText) == 0)
            Util.addClass(cart[0], "cd-cart--empty");
        }, 500);
      } else {
        Util.addClass(cart[0], "cd-cart--open");
      }
    }

    function removeProduct(product) {
      var productID = product.getAttribute("data-id");
      console.log(productID);

      // Make an AJAX request to remove the product from the cart
      fetch("/wp-admin/admin-ajax.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=remove_from_cart&product_id=" + productID,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Update the cart UI
            var topPosition = product.offsetTop,
              productQuantity = Number(
                product.getElementsByTagName("input")[0].value
              ),
              productTotPrice =
                Number(
                  product
                    .getElementsByClassName("cd-cart__price")[0]
                    .innerText.replace("$", "")
                ) * productQuantity;

            product.style.top = topPosition + "px";
            Util.addClass(product, "cd-cart__product--deleted");

            // Update cart totals
            updateCartTotal(productTotPrice, false);
            updateCartCount(true, -productQuantity);

            // Remove the product from the DOM after some delay
            setTimeout(function () {
              product.remove();
            }, 800);
          } else {
            console.error("Error removing product from cart:", data.error);
          }
        });
    }

function addProduct(product) {
  var productQuantity = product.quantity || 1; // If product.quantity is undefined, it will use 1 as the default value
  var productAdded = `<li class="cd-cart__product" data-id="${product.product_id}"><span class="cd-cart__image"><a href="/product/${product.product_id}"><img src="${product.thumbnail}" alt="placeholder"></a></span><h3 class="cd-cart__title"><a href="/product/${product.product_id}">${product.title}</a></h3><span class="cd-cart__price">$${product.price}</span>`;
  productAdded += `<div class="cd-cart__select"><span class="cd-cart__select-label">Qty</span><input type="text" value="${productQuantity}" class="cd-cart__select-quantity" data-id="${product.product_id}"></div>`;
  productAdded += `<a href="#0" class="cd-cart__delete-item"><i class="fa fa-trash" aria-hidden="true"></i></a></li>`;
  cartList.insertAdjacentHTML("beforeend", productAdded);
}

    function removePreviousProduct() {
      // definitively removed a product from the cart (undo not possible anymore)
      var deletedProduct = cartList.getElementsByClassName(
        "cd-cart__product--deleted"
      );
      if (deletedProduct.length > 0) deletedProduct[0].remove();
    }

    function updateCartCount(emptyCart, quantity) {
      if (typeof quantity === "undefined") {
        var actual = Number(cartCountItems[0].innerText) + 1;
        var next = actual + 1;

        if (emptyCart) {
          cartCountItems[0].innerText = actual;
          cartCountItems[1].innerText = next;
          animatingQuantity = false;
        } else {
          Util.addClass(cartCount, "cd-cart__count--update");

          setTimeout(function () {
            cartCountItems[0].innerText = actual;
          }, 150);

          setTimeout(function () {
            Util.removeClass(cartCount, "cd-cart__count--update");
          }, 200);

          setTimeout(function () {
            cartCountItems[1].innerText = next;
            animatingQuantity = false;
          }, 230);
        }
      } else {
        var actual = Number(cartCountItems[0].innerText) + quantity;
        var next = actual + 1;

        cartCountItems[0].innerText = actual;
        cartCountItems[1].innerText = next;
        animatingQuantity = false;
      }
    }

    function updateCartTotal(price, bool) {
      cartTotal.innerText = bool
        ? (Number(cartTotal.innerText) + Number(price)).toFixed(2)
        : (Number(cartTotal.innerText) - Number(price)).toFixed(2);
    }

function quickUpdateCart() {
  var quantity = 0;
  var price = 0;

  for (var i = 0; i < cartListItems.length; i++) {
    if (!Util.hasClass(cartListItems[i], "cd-cart__product--deleted")) {
      var singleQuantity = Number(
        cartListItems[i].getElementsByTagName("input")[0].value
      );
      quantity = quantity + singleQuantity;
      price =
        price +
        singleQuantity *
          Number(
            cartListItems[i]
              .getElementsByClassName("cd-cart__price")[0]
              .innerText.replace("$", "")
          );

      // Send an AJAX request to update the cart quantity
      jQuery.ajax({
        type: "POST",
        url: "wp-admin/admin-ajax.php",
        data: {
          action: "update_cart_quantity",
          product_id: cartListItems[i].getAttribute("data-id"),
          quantity: singleQuantity,
        },
        success: function (response) {
          if (response.success) {
            console.log(response.data.message);
          } else {
            console.error(response.data.message);
          }
        },
      });
    }
  }

  cartTotal.innerText = price.toFixed(2);
  cartCountItems[0].innerText = quantity;
  cartCountItems[1].innerText = quantity + 1;
}
  }
})();
