let bakeryItems = [];
let menu = [];
let notifier = new AWN();

document.addEventListener("DOMContentLoaded", function () {
  fetchMenu();
  fetchBakeryMenu();
  fetchPendingOrders();
  populateHotelTypesForSelector();
  if (!localStorage.getItem("todayBillCount")) {
    localStorage.setItem("todayBillCount", 0);
  }
  document.getElementById("daily-bill-counter").innerText =
    getBillCount().toString();
});

function increaseBillCount() {
  let count = parseInt(localStorage.getItem("todayBillCount")) || 0;
  count++;
  localStorage.setItem("todayBillCount", count);
}

function getBillCount() {
  let count = parseInt(localStorage.getItem("todayBillCount")) || 0;
  localStorage.setItem("todayBillCount", count);
  return count;
}

function resetBillCount() {
  localStorage.setItem("todayBillCount", 0);
}

function fetchBakeryMenu(category = "") {
  let url = "fetch_bakery_menu.php";

  if (category) {
    url += `?category=${encodeURIComponent(category)}`;
  }

  fetch(url)
    .then((response) => response.json())
    .then((data) => {
      bakeryItems = data;
    })
    .catch((error) => console.error("Error fetching menu:", error));
}

function displayMenuItems(items) {
  const menuContainer = document.getElementById("menuContainer");
  menuContainer.innerHTML = "";

  items.forEach((item) => {
    const itemElement = document.createElement("div");
    itemElement.textContent = `${item.item_name} - ${item.item_price}`;
    menuContainer.appendChild(itemElement);
  });
}

// customer selector hide and display mechanism

const toggleCustomerSelector = () => {
  const customerSelector = document.querySelector(".customer-selector");
  customerSelector.classList.toggle("hide-customer-selector");
  document.getElementById("customer-telephone").focus();
};

document.addEventListener("keydown", (e) => {
  if (e.key == "c" && e.ctrlKey) {
    e.preventDefault();
    toggleCustomerSelector();
    document.getElementById("customer-telephone").focus();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const customerInput = document.getElementById("customer-telephone");
  const suggestionsContainer = document.getElementById("customer-suggestions");

  customerInput.addEventListener("input", function () {
    let query = customerInput.value.trim();

    if (query.length > 0) {
      fetch(`./search_customer.php?query=${encodeURIComponent(query)}`)
        .then((response) => response.json())
        .then((data) => {
          displaySuggestions(data);
        })
        .catch((error) => console.error("Error fetching customers:", error));
    } else {
      suggestionsContainer.innerHTML = "";
    }
  });

  function displaySuggestions(customers) {
    suggestionsContainer.innerHTML = "";
    if (customers.length === 0) {
      suggestionsContainer.innerHTML =
        "<div class='no-results'>No matching customers</div>";
      return;
    }

    customers.forEach((customer) => {
      const suggestion = document.createElement("div");
      const customerIdHolder = document.getElementById("customer-id-holder");
      suggestion.classList.add("suggestion-item");
      suggestion.textContent = `${customer.name} (${customer.phone_number})`;

      suggestion.addEventListener("click", function () {
        customerInput.value = customer.name;
        customerIdHolder.value = customer.customer_id;
        getCustomerPayment(customer.customer_id);
        suggestionsContainer.innerHTML = "";
      });

      suggestionsContainer.appendChild(suggestion);
    });
  }
});

// function navigateToPrintKOT(cart) {
//   if (!Array.isArray(cart) || cart.length === 0) {
//     alert("Cart is empty!");
//     return;
//   }
//   const hotelTypeSelect = document.getElementById("hotel-type");
//   const hotelType = hotelTypeSelect.options[hotelTypeSelect.selectedIndex].text; // Get the text instead of value
//   const nextBillId = document
//     .getElementById("next-bill-id")
//     .textContent.replace("#", "");
//   const billId = parseInt(nextBillId) - 1; // Reduce by 1 after converting to integer
//   const cartQuery = encodeURIComponent(JSON.stringify(cart));
//   const url = `print_kot.php?cart=${cartQuery}&hotel_type=${encodeURIComponent(
//     hotelType
//   )}&bill_id=${billId}`;
//   window.open(url, "_blank", "width=800,height=600");
// }

function navigateToPrintKOT(cart) {
  const selectedHotelType = document.getElementById("hotel-selector").value;
  const selectedTable = document.getElementById('table').value;
  if (!Array.isArray(cart) || cart.length === 0) {
    showAlert({
      type: "warning",
      title: "Empty Cart",
      message: "Cannot print KOT bill with an empty cart.",
      confirmButtonText: "OK"
    });
    
    // Debug version - let's find out what buttons exist
    setTimeout(() => {
      console.log("=== DEBUGGING ALERT BUTTONS ===");
      
      // Log all buttons on the page
      const allButtons = document.querySelectorAll('button');
      console.log("All buttons found:", allButtons.length);
      allButtons.forEach((btn, index) => {
        console.log(`Button ${index}:`, {
          text: btn.textContent,
          innerHTML: btn.innerHTML,
          className: btn.className,
          id: btn.id,
          attributes: Array.from(btn.attributes).map(attr => `${attr.name}="${attr.value}"`).join(' ')
        });
      });
      
      // Try to find buttons with "OK" text
      const okButtons = Array.from(allButtons).filter(btn => 
        btn.textContent.trim().toUpperCase().includes('OK') ||
        btn.innerHTML.trim().toUpperCase().includes('OK')
      );
      console.log("Buttons containing 'OK':", okButtons);
      
      // Add Enter key listener
      const handleEnter = function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          console.log("Enter pressed! Looking for OK button...");
          
          // Try multiple approaches to find the OK button
          let okButton = null;
          
          // Method 1: Look for visible buttons with OK text
          const visibleOkButtons = Array.from(document.querySelectorAll('button')).filter(btn => {
            const isVisible = btn.offsetParent !== null;
            const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
            return isVisible && hasOkText;
          });
          
          if (visibleOkButtons.length > 0) {
            okButton = visibleOkButtons[0];
            console.log("Found OK button using Method 1:", okButton);
          }
          
          // Method 2: Try common selectors
          if (!okButton) {
            const selectors = [
              '.swal2-confirm',
              '.alert-confirm', 
              '.btn-confirm',
              '.confirm-button',
              '.ok-button',
              '[data-dismiss="alert"]',
              '[data-dismiss="modal"]',
              '.modal-confirm',
              '.alert-button',
              'button[type="button"]'
            ];
            
            for (const selector of selectors) {
              const element = document.querySelector(selector);
              if (element && element.offsetParent !== null) {
                okButton = element;
                console.log(`Found OK button using selector "${selector}":`, okButton);
                break;
              }
            }
          }
          
          // Method 3: Find the most recently added button (likely the alert button)
          if (!okButton) {
            const allCurrentButtons = document.querySelectorAll('button');
            if (allCurrentButtons.length > 0) {
              okButton = allCurrentButtons[allCurrentButtons.length - 1];
              console.log("Using last button as OK button:", okButton);
            }
          }
          
          // Click the button if found
          if (okButton) {
            console.log("Clicking OK button:", okButton);
            okButton.click();
          } else {
            console.log("No OK button found!");
          }
          
          document.removeEventListener('keydown', handleEnter);
        }
      };
      document.addEventListener('keydown', handleEnter);
    }, 100);
    return;
  } else if (selectedHotelType == "0") {
    showAlert({
      type: "warning",
      title: "Select Hotel Type",
      message: "Please select a hotel type!",
      confirmButtonText: "OK"
    });
    
    // Same debugging logic for second alert
    setTimeout(() => {
      const handleEnter = function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          console.log("Enter pressed on second alert!");
          
          // Look for visible buttons with OK text
          const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
            const isVisible = btn.offsetParent !== null;
            const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
            return isVisible && hasOkText;
          });
          
          if (okButton) {
            console.log("Clicking OK button:", okButton);
            okButton.click();
          } else {
            console.log("No OK button found for second alert!");
          }
          
          document.removeEventListener('keydown', handleEnter);
        }
      };
      document.addEventListener('keydown', handleEnter);
    }, 100);
    return;
  }
  else if (selectedHotelType == "1" && selectedTable == "") {
                showAlert({
                    type: 'warning',
                    title: 'Select Table',
                    message: 'Please select a table!',
                    confirmButtonText: 'OK'
                });
                
                // Add Enter key listener to close alert
                setTimeout(() => {
                    const handleEnter = function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            
                            // Look for visible buttons with OK text
                            const okButton = Array.from(document.querySelectorAll('button')).find(btn => {
                                const isVisible = btn.offsetParent !== null;
                                const hasOkText = btn.textContent.trim().toUpperCase().includes('OK');
                                return isVisible && hasOkText;
                            });
                            
                            if (okButton) {
                                okButton.click();
                            }
                            
                            document.removeEventListener('keydown', handleEnter);
                        }
                    };
                    document.addEventListener('keydown', handleEnter);
                }, 100);
                return; // Exit the function early
            }
  
  
  // Rest of the function remains the same
  const hotelTypeSelect = document.getElementById("hotel-type");
  const hotelType = hotelTypeSelect.options[hotelTypeSelect.selectedIndex].text;
  const hotelTypeId = hotelTypeSelect.value;
  const nextBillIdElement = document.getElementById("next-bill-id");
  const nextBillIdText = nextBillIdElement.textContent;
  const cleanedBillId = nextBillIdText
    .replace("Held Bill #", "")
    .replace("#", "");
  const billId = parseInt(cleanedBillId);
  let referenceNumber = "";
  if (hotelTypeId === "4" || hotelTypeId === "6") {
    const referenceInput =
      document.getElementById("ref-number") ||
      document.getElementById("reference-number");
    if (referenceInput) {
      referenceNumber = referenceInput.value.trim();
    }
  }

  // Get table number
  let tableNumber = "";
  const tableElement = document.getElementById("table");
  if (tableElement) {
    tableNumber = tableElement.value.trim();
  }

  const cartQuery = encodeURIComponent(JSON.stringify(cart));
  const url = `print_kot_single.php?cart=${cartQuery}&hotel_type=${encodeURIComponent(
    hotelType
  )}&bill_id=${billId}&hotel_type_id=${hotelTypeId}&reference_number=${encodeURIComponent(
    referenceNumber
  )}&table_number=${encodeURIComponent(tableNumber)}`;
  window.open(url, "_blank");
}
// function printPosBill(cart, billId, tableId, date, paidAmount, totalAmount) {
//   if (!Array.isArray(cart) || cart.length === 0) {
//     alert("Cart is empty!");
//     return;
//   }
//   const cartQuery = encodeURIComponent(JSON.stringify(cart));
//   const url = `pos_bill.php?cart=${cartQuery}&billId=${billId}&tableId=${tableId}&date=${date}&paidAmount=${paidAmount}&totalAmount=${totalAmount}`;
//   window.location.href = url;
// }

function printPosBill(
  cart,
  billId,
  tableId,
  date,
  paidAmount,
  totalAmount,
  serviceCharge
) {
  if (!Array.isArray(cart) || cart.length === 0) {
    alert("Cart is empty!");
    return;
  }
  const creditPaymentflag =
    document.getElementById("payment-method").value == "cre" ? true : false;

  // Get hotel type and reference number if applicable
  const hasAdvancePayment = isAdvPaymentChecked();
  const advancePaymentAmount = parseFloat(
    document.getElementById("adv-payment-amount").textContent
  ).toFixed(2);
  const hotelTypeId = document.getElementById("hotel-type").value;
  let referenceNumber = "";
  let hotelTypeName = "";

  // Get hotel type name from select element
  const hotelTypeSelect = document.getElementById("hotel-type");
  if (hotelTypeSelect && hotelTypeSelect.selectedIndex >= 0) {
    hotelTypeName = hotelTypeSelect.options[hotelTypeSelect.selectedIndex].text;
  }

  // Get reference number if it's Uber or Pick Me
  if (hotelTypeId === "4" || hotelTypeId === "6") {
    const referenceInput =
      document.getElementById("ref-number") ||
      document.getElementById("reference-number");
    if (referenceInput) {
      referenceNumber = referenceInput.value.trim();
    }
  }

  const cartQuery = encodeURIComponent(JSON.stringify(cart));
  const url = `pos_bill2.php?cart=${cartQuery}&creditPaymentflag=${creditPaymentflag}&billId=${billId}&hasAdvancePayment=${hasAdvancePayment}&advancePaymentAmount=${advancePaymentAmount}&tableId=${tableId}&date=${date}&paidAmount=${paidAmount}&totalAmount=${totalAmount}&hotelTypeId=${hotelTypeId}&serviceCharge=${serviceCharge}&hotelTypeName=${encodeURIComponent(
    hotelTypeName
  )}&referenceNumber=${encodeURIComponent(referenceNumber)}`;
  window.open(url, "_blank");
}

function collectCartData() {
  const cartItemsBody = document.getElementById("cart-items");
  if (!cartItemsBody) {
    console.error("Cart container not found");
    return [];
  }
  const rows = cartItemsBody.querySelectorAll("tr");
  const cartData = [];
  rows.forEach((row, index) => {
    const cells = row.querySelectorAll("td");
    if (cells.length < 4) return;

    const nameElement = cells[0].querySelector("span");
    const name = nameElement ? nameElement.textContent.trim() : "";

    const priceText = cells[1].textContent.trim();
    const price = parseFloat(priceText.replace(/[^0-9.-]+/g, ""));

    const quantityDiv = cells[2].querySelector(".cart-buttons");
    let quantity = 0;

    if (quantityDiv) {
      const childNodes = Array.from(quantityDiv.childNodes);
      for (let i = 0; i < childNodes.length; i++) {
        const node = childNodes[i];
        if (node.nodeType === 3 && node.textContent.trim() !== "") {
          quantity = parseInt(node.textContent.trim(), 10);
          break;
        }
      }
    }

    const fcDiv = cells[3].querySelector(".cart-buttons");
    let fc = 0;

    if (fcDiv) {
      const childNodes = Array.from(fcDiv.childNodes);
      for (let i = 0; i < childNodes.length; i++) {
        const node = childNodes[i];
        if (node.nodeType === 3 && node.textContent.trim() !== "") {
          fc = parseInt(node.textContent.trim(), 10);
          break;
        }
      }
    }

    const totalText = cells[5].textContent.trim();
    const total = parseFloat(totalText.replace(/[^0-9.-]+/g, ""));

    const remarksInput = cells[0].querySelector(".kot_remarks");
    const remarks = remarksInput ? remarksInput.value.trim() : "";

    const itemCategory = row.dataset.itemCategory || "";

    const item = {
      id: index,
      name: name,
      price: price,
      quantity: quantity,
      total: total,
      itemCategory: itemCategory,
      remarks: remarks,
      fc: fc,
    };
    cartData.push(item);
  });

  return cartData;
}

document.addEventListener("DOMContentLoaded", () => {
  const advPaymentWindow = document.querySelector(".adv-paym-indicator");

  document.addEventListener("keydown", (e) => {
    if (e.ctrlKey && e.key == "a") {
      e.preventDefault();
      advPaymentWindow.classList.toggle("show-adv-payment-window");
    }
  });
});

function getCustomerPayment(customerId) {
  const advPaymentWindow = document.querySelector(".adv-paym-indicator");
  const advPaymentAmount = document.getElementById("adv-payment-amount");
  fetch(`./fetch_adv_payment.php?customer_id=${customerId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.log("Error:", data.error);
      } else {
        advPaymentAmount.textContent = parseFloat(data.payment_amount).toFixed(
          2
        );
        advPaymentWindow.classList.add("show-adv-payment-window");
      }
    })
    .catch((error) => console.error("Fetch error:", error));
}

function resetAdvancePaymentCheckbox() {
  document.getElementById("adv-payment-check").checked = false;
}

function deleteCustomerPayment(customerId) {
  fetch("./delete_adv_payment.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `customer_id=${customerId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("Success:", data.success);
      } else {
        console.log("Error:", data.error);
      }
    })
    .catch((error) => console.error("Fetch error:", error));
}

function isAdvPaymentChecked() {
  return document.getElementById("adv-payment-check").checked;
}

function getCartTotalValue() {
  const cartItemsBody = document.getElementById("cart-items");
  let totalPrice = 0;

  cart.forEach((item, index) => {
    const total = item.price * item.quantity;
    totalPrice += total;
  });

  return totalPrice;
}

function handleCheckboxChange() {
  let isChecked = document.getElementById("adv-payment-check").checked;

  const discountElement = document.getElementById("discount-amount");
  const advPaymentElement = document.getElementById("adv-payment-amount");
  const totalBeforeDiscountElement = document.getElementById(
    "total-before-discount"
  );
  const checkoutTotalAmountElement = document.getElementById(
    "checkout-total-amount"
  );
  const totalPriceElement = document.getElementById("total-price");

  const discountAmount = parseFloat(discountElement.textContent) || 0;
  const advPaymentAmount = parseFloat(advPaymentElement.textContent) || 0;
  const totalValue = parseFloat(getCartTotalValue()) || 0;

  if (isChecked) {
    totalBeforeDiscountElement.textContent = (
      totalValue - advPaymentAmount
    ).toFixed(2);
    checkoutTotalAmountElement.textContent = (
      totalValue -
      advPaymentAmount -
      discountAmount
    ).toFixed(2);
    totalPriceElement.textContent = (totalValue - advPaymentAmount).toFixed(2);
  } else {
    totalBeforeDiscountElement.textContent = totalValue.toFixed(2);
    checkoutTotalAmountElement.textContent = (
      totalValue - discountAmount
    ).toFixed(2);
    totalPriceElement.textContent = totalValue.toFixed(2);
  }
}

document.addEventListener("keydown", (e) => {
  if (e.key == "d" && e.ctrlKey) {
    e.preventDefault();
    const sliderWindow = document.getElementById("slider-window-orders");
    sliderWindow.classList.toggle("show-slider");
  }
});

const closeSliderWindow = () => {
  const sliderWindow = document.getElementById("slider-window-orders");
  sliderWindow.classList.remove("show-slider");
};

async function fetchPendingOrders() {
  try {
    const response = await fetch("./get_dine_in_orders.php");
    const orders = await response.json();
    displayOrders(orders);
  } catch (error) {
    console.error("Error fetching orders:", error);
  }
}

function displayOrders(orders) {
  const container = document.querySelector(".dine-in-order-container");
  container.innerHTML = "";

  orders.forEach((order) => {
    const orderCard = document.createElement("div");
    orderCard.className = "dine-in-order-card";

    const orderHeader = document.createElement("div");
    orderHeader.innerHTML = `
          <div class = "order-card-header">
              <span class="table-indicator">Table: ${order.table_id}</span>
              <span class="order-date">${new Date(
                order.date
              ).toLocaleString()}</span>
          </div>
      `;
    orderCard.appendChild(orderHeader);

    const itemsSection = document.createElement("div");
    const itemsList = document.createElement("ul");
    itemsList.classList.add = "hidden";

    order.order_items.forEach((item) => {
      const itemLi = document.createElement("li");
      itemLi.classList.add("order_item");
      itemLi.style.border = "1px solid grey";
      itemLi.innerHTML = `
              ${item.item_name} (${item.portion_size}) 
              × ${item.quantity} 
              <span style="color: transparent;">${item.item_id}</span>
          `;
      itemsList.appendChild(itemLi);
    });

    const toggleItemsBtn = document.createElement("button");
    toggleItemsBtn.className = "order-toggle";
    toggleItemsBtn.innerHTML = `
          <i class="fas fa-chevron-down" style="margin-right: 10px;"></i> Show Items
      `;
    toggleItemsBtn.addEventListener("click", () => {
      itemsList.classList.toggle("hidden");
      toggleItemsBtn.innerHTML = itemsList.classList.contains("hidden")
        ? '<i class="fas fa-chevron-down" style="margin-right: 10px;"></i> Show Items'
        : '<i class="fas fa-chevron-up" style="margin-right: 10px;"></i> Hide Items';
    });

    itemsSection.appendChild(toggleItemsBtn);
    itemsSection.appendChild(itemsList);
    orderCard.appendChild(itemsSection);

    const actionButtons = document.createElement("div");
    actionButtons.className = "action-btn-cont-orders";
    actionButtons.innerHTML = `
          <button onclick="finalizeOrder(${order.order_id})" 
                  class="btn-order-dine finalized">
              Finalize
          </button>
          <button onclick="passToCart(${order.order_id})" 
                  class="btn-order-dine passToCart">
              Pass to Cart
          </button>
      `;
    orderCard.appendChild(actionButtons);
    container.appendChild(orderCard);
  });
}

function finalizeOrder(orderId) {
  if (!confirm(`Are you sure you want to finalize order ${orderId}?`)) {
    return;
  }
  fetch("finalize_order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `order_id=${orderId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const orderCard = document
          .querySelector(
            `.dine-in-order-card button[onclick="finalizeOrder(${orderId})"]`
          )
          .closest(".dine-in-order-card");

        if (orderCard) {
          orderCard.remove();
        }
        notifier.success("Order finalized!");
      } else {
        notifier.alert(data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while finalizing the order");
    });
}

function passToCart(orderId) {
  const orderCard = document
    .querySelector(
      `.dine-in-order-card button[onclick="passToCart(${orderId})"]`
    )
    .closest(".dine-in-order-card");

  if (!orderCard) {
    console.error(`Order with ID ${orderId} not found`);
    return;
  }

  const orderItems = Array.from(orderCard.querySelectorAll(".order_item"));

  orderItems.forEach((itemElement) => {
    const itemId = itemElement
      .querySelector('span[style*="color: transparent"]')
      .textContent.trim();
    const itemText = itemElement.textContent.trim();
    const [itemNameWithSize, quantityStr] = itemText.split("×");
    const quantity = parseInt(quantityStr.trim());
    const [itemName, portionSize] = itemNameWithSize.trim().split("(");
    const cleanPortionSize = portionSize.replace(")", "").trim();
    const menuItem = menu.find((menuItem) => menuItem.item_id === itemId);

    if (menuItem) {
      let portionPrice = 0;

      switch (cleanPortionSize) {
        case "regular":
          portionPrice = menuItem.regular_price;
          break;
        case "medium":
          portionPrice = menuItem.medium_price;
          break;
        case "large":
          portionPrice = menuItem.large_price;
          break;
        default:
          portionPrice = menuItem.regular_price;
      }
      console.log({
        itemId,
        itemName,
        parseFloat,
        item_cat: menuItem.item_category,
        cleanPortionSize,
      });

      automatedAddToCart(
        itemId,
        itemName,
        parseFloat(portionPrice),
        menuItem.item_category,
        cleanPortionSize,
        quantity
      );
    } else {
      console.warn(`Menu item not found with ID: ${itemId}`);
      return;
    }
  });

  console.log(`Passed order ${orderId} to cart`);
}

function automatedAddToCart(
  itemId,
  itemName,
  price,
  itemCategory,
  portionSize,
  quantity
) {
  const cartItemId = `${itemId}-${portionSize}`;

  let displayName = itemName;

  if (portionSize !== "") {
    displayName = `${itemName} (${
      portionSize.charAt(0).toUpperCase() + portionSize.slice(1)
    })`;
  } else {
    displayName = itemName;
  }

  const existingItem = cart.find((item) => item.uniqueId === cartItemId);
  if (existingItem) {
    existingItem.quantity++;
  } else {
    cart.push({
      id: itemId,
      uniqueId: cartItemId,
      name: displayName,
      price,
      quantity,
      itemCategory,
      portionSize,
    });
  }
  updateCart();
}

function fetchMenu() {
  fetch("./fetch_menu.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error("Error:", data.error);
        return;
      }

      menu = [...data];
      console.log(menu);
    })
    .catch((error) => console.error("Fetch error:", error));
}

$(document).ready(function () {
  function fetchPendingOrdersCounter() {
    $.ajax({
      url: "./new_order_checker.php",
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (data && data.pending_count !== undefined) {
          let count = data.pending_count;
          let counterElement = $("#new-order-counter");
          counterElement.text(count < 10 ? "0" + count : count);
          if (count > 0) {
            $(".new-orders-indicator").addClass("active-indicator");
          } else {
            $(".new-orders-indicator").removeClass("active-indicator");
          }
        }
      },
    });
  }
  setInterval(fetchPendingOrdersCounter, 5000);
  setInterval(fetchPendingOrders, 5000);
  fetchPendingOrdersCounter();
});

// document.getElementById("adv-pay-slider").addEventListener("click", () => {
//   const advPaymentWindow = document.querySelector(".adv-paym-indicator");
//   advPaymentWindow.classList.toggle("show-adv-payment-window");
// });

document.getElementById("dine-orders-slider").addEventListener("click", () => {
  const sliderWindow = document.getElementById("slider-window-orders");
  sliderWindow.classList.toggle("show-slider");
});

document.getElementById("food-catalog-btn").addEventListener("click", () => {
  const foodCatlog = document.getElementById("food-catalogue");
  foodCatlog.classList.toggle("show-food-catalog");
});

document.querySelector(".fc-close-btn").addEventListener("click", () => {
  const foodCatlog = document.getElementById("food-catalogue");
  foodCatlog.classList.toggle("show-food-catalog");
});

document.addEventListener("DOMContentLoaded", function () {
  fetch("pos_get_mc.php")
    .then((response) => response.json())
    .then((data) => {
      populateMainCategories(data);
    })
    .catch((error) => console.error("Error fetching data:", error));
});

const callForSubCategories = (mainCatId, event) => {
  const foodContainer = document.getElementById("food-items-displayer");
  foodContainer.innerHTML = "";
  fetch(`./pos_get_sc.php?mainCatId=${mainCatId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.length == 0) {
        const subCatCont = document.getElementById("fc-sub-cat-cont");
        subCatCont.innerHTML = "";
        const placeholder = document.createElement("div");
        placeholder.classList.add("empty-placeholder");
        placeholder.textContent = "No sub categories!";
        subCatCont.appendChild(placeholder);
      } else {
        populateSubCategories(data);
      }
    })
    .catch((error) => console.error("Error fetching sub menu types:", error));
  setActiveWrapper(event);
};

const populateMainCategories = (data) => {
  const mainCatContainer = document.getElementById("fc-main-cat-cont");
  data.forEach((food) => {
    const catContainer = document.createElement("div");
    catContainer.setAttribute("class", "main-category-wrapper");
    catContainer.textContent = food.item_type_name;
    catContainer.setAttribute(
      "onclick",
      `callForSubCategories(${food.item_type_id}, event)`
    );
    mainCatContainer.appendChild(catContainer);
  });
};

function getActiveCategory() {
  const categoryElements = document.querySelectorAll(".main-category-wrapper");
  for (let i = 0; i < categoryElements.length; i++) {
    if (categoryElements[i].classList.contains("active-mc-wrapper")) {
      return categoryElements[i].textContent;
    }
  }
  return null;
}

const foodFilter = (type, event) => {
  console.log(getActiveCategory());

  const hotelType = document.getElementById("hotel-selector").value;
  const foodContainer = document.getElementById("food-items-displayer");
  foodContainer.innerHTML = "";
  const targetFoods = menu.filter(
    (food) =>
      food.sub_item_type === type && food.item_type === getActiveCategory()
  );
  if (targetFoods.length === 0) {
    const placeholderWrapper = document.createElement("div");
    placeholderWrapper.classList.add("pholder-wrapper");

    const emptyText = document.createElement("span");
    emptyText.innerText = "No foods for this category!";
    emptyText.classList.add("empty-text");

    const emptyPlaceholder = document.createElement("img");
    emptyPlaceholder.setAttribute("src", "../images/food-pholder.png");
    emptyPlaceholder.classList.add("food-pholder");

    placeholderWrapper.append(emptyPlaceholder, emptyText);
    foodContainer.appendChild(placeholderWrapper);
    setActiveWrapperSub(event);
    return;
  }

  targetFoods.forEach((item) => {
    let regularPrice = 0,
      mediumPrice = 0,
      largePrice = 0;

    switch (parseInt(hotelType)) {
      case 1:
        regularPrice = item.regular_price;
        mediumPrice = item.medium_price;
        largePrice = item.large_price;
        break;
      case 4:
      case 6:
        regularPrice = item.uber_pickme_regular;
        mediumPrice = item.uber_pickme_medium;
        largePrice = item.uber_pickme_large;
        break;
      case 7:
        regularPrice = item.takeaway_regular;
        mediumPrice = item.takeaway_medium;
        largePrice = item.takeaway_large;
        break;
      case 11:
        regularPrice = item.delivery_service_regular;
        mediumPrice = item.delivery_service_medium;
        largePrice = item.delivery_service_large;
        break;
      default:
        regularPrice = item.regular_price;
        mediumPrice = item.medium_price;
        largePrice = item.large_price;
    }

    const hasRegularPrice = regularPrice > 0;
    const hasMediumPrice = mediumPrice > 0;
    const hasLargePrice = largePrice > 0;

    const portionSelectId = `portion-${item.item_id}`;
    const isAvailable = hasRegularPrice || hasMediumPrice || hasLargePrice;

    let portionOptionsHTML = "";
    if (isAvailable) {
      portionOptionsHTML = `
        <div>
            <select id="${portionSelectId}" class="portion-select" data-item-id="${
        item.item_id
      }">
                ${
                  hasRegularPrice
                    ? `<option value="regular" data-price="${regularPrice}">Family - LKR ${regularPrice}</option>`
                    : ""
                }
                ${
                  hasMediumPrice
                    ? `<option value="medium" data-price="${mediumPrice}">Medium - LKR ${mediumPrice}</option>`
                    : ""
                }
                ${
                  hasLargePrice
                    ? `<option value="large" data-price="${largePrice}">Large - LKR ${largePrice}</option>`
                    : ""
                }
            </select>
            <button class="portion-card-btn" onclick="addToCartWithPortion('${
              item.item_id
            }', '${item.item_name}', '${
        item.item_category
      }', '${portionSelectId}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Add to Cart
            </button>
        </div>
      `;
    } else {
      portionOptionsHTML = `<p class="not-available">Not available for this service type</p>`;
    }

    const menuItem = document.createElement("div");
    const portionEmbelm = document.createElement("div");
    portionEmbelm.classList.add("portion-emblem");
    portionEmbelm.innerHTML = `<i class="fa-solid fa-pizza-slice"></i>`;
    menuItem.classList.add("portion-container-new");
    menuItem.innerHTML = `
    <h6 class="item-name-card">${item.item_name}</h6>
    <div class="item-details">
    <p>Category: <span>${item.item_category}</span></p>
    </div>
    ${portionOptionsHTML}
    `;
    menuItem.appendChild(portionEmbelm);
    foodContainer.appendChild(menuItem);
  });

  setActiveWrapperSub(event);
};

const foodFilterRecursive = (type, event) => {
  const hotelType = document.getElementById("hotel-selector").value;
  const foodContainer = document.getElementById("food-items-displayer");
  foodContainer.innerHTML = "";
  const targetFoods = menu.filter((food) => food.sub_item_type === type);

  if (targetFoods.length === 0) {
    const placeholderWrapper = document.createElement("div");
    placeholderWrapper.classList.add("pholder-wrapper");

    const emptyText = document.createElement("span");
    emptyText.innerText = "No foods for this category!";
    emptyText.classList.add("empty-text");

    const emptyPlaceholder = document.createElement("img");
    emptyPlaceholder.setAttribute("src", "../images/food-pholder.png");
    emptyPlaceholder.classList.add("food-pholder");

    placeholderWrapper.append(emptyPlaceholder, emptyText);
    foodContainer.appendChild(placeholderWrapper);
    setActiveWrapperSub(event);
    return;
  }

  targetFoods.forEach((item) => {
    let regularPrice = 0,
      mediumPrice = 0,
      largePrice = 0;

    switch (parseInt(hotelType)) {
      case 1:
        regularPrice = item.regular_price;
        mediumPrice = item.medium_price;
        largePrice = item.large_price;
        break;
      case 4:
      case 6:
        regularPrice = item.uber_pickme_regular;
        mediumPrice = item.uber_pickme_medium;
        largePrice = item.uber_pickme_large;
        break;
      case 7:
        regularPrice = item.takeaway_regular;
        mediumPrice = item.takeaway_medium;
        largePrice = item.takeaway_large;
        break;
      case 11:
        regularPrice = item.delivery_service_regular;
        mediumPrice = item.delivery_service_medium;
        largePrice = item.delivery_service_large;
        break;
      default:
        regularPrice = item.regular_price;
        mediumPrice = item.medium_price;
        largePrice = item.large_price;
    }

    const hasRegularPrice = regularPrice > 0;
    const hasMediumPrice = mediumPrice > 0;
    const hasLargePrice = largePrice > 0;

    const portionSelectId = `portion-${item.item_id}`;
    const isAvailable = hasRegularPrice || hasMediumPrice || hasLargePrice;

    let portionOptionsHTML = "";
    if (isAvailable) {
      portionOptionsHTML = `
        <div>
            <select id="${portionSelectId}" class="portion-select" data-item-id="${
        item.item_id
      }">
                ${
                  hasRegularPrice
                    ? `<option value="regular" data-price="${regularPrice}">Family - LKR ${regularPrice}</option>`
                    : ""
                }
                ${
                  hasMediumPrice
                    ? `<option value="medium" data-price="${mediumPrice}">Medium - LKR ${mediumPrice}</option>`
                    : ""
                }
                ${
                  hasLargePrice
                    ? `<option value="large" data-price="${largePrice}">Large - LKR ${largePrice}</option>`
                    : ""
                }
            </select>
            <button class="portion-card-btn" onclick="addToCartWithPortion('${
              item.item_id
            }', '${item.item_name}', '${
        item.item_category
      }', '${portionSelectId}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Add to Cart
            </button>
        </div>
      `;
    } else {
      portionOptionsHTML = `<p class="not-available">Not available for this service type</p>`;
    }

    const menuItem = document.createElement("div");
    const portionEmbelm = document.createElement("div");
    portionEmbelm.classList.add("portion-emblem");
    portionEmbelm.innerHTML = `<i class="fa-solid fa-pizza-slice"></i>`;
    menuItem.classList.add("portion-container-new");
    menuItem.innerHTML = `
    <h6 class="item-name-card">${item.item_name}</h6>
    <div class="item-details">
    <p>Category: <span>${item.item_category}</span></p>
    </div>
    ${portionOptionsHTML}
    `;
    menuItem.appendChild(portionEmbelm);
    foodContainer.appendChild(menuItem);
  });

  setActiveWrapperSub(event);
};

function applyDiscount(index, discountValue) {
  discountValue = parseFloat(discountValue) || 0;
  if (discountValue < 0) discountValue = 0;

  cart[index].discount = discountValue;
  const item = cart[index];
  const itemTotal = item.price * item.quantity - discountValue;
  document.getElementById(`item_total_${index}`).textContent =
    itemTotal.toFixed(2);
  let totalPrice = 0;
  cart.forEach((itm) => {
    const discount = itm.discount || 0;
    totalPrice += itm.price * itm.quantity - discount;
  });

  document.getElementById("total-price").textContent = totalPrice.toFixed(2);
  updateTotalBillDiscount();
  savePersistedCart();
  updateCart();
}

function clearPersistantCart() {
  localStorage.removeItem("restaurant_cart");
  cart = [];
  updateCart();
}

function updateTotalBillDiscount() {
  let totalDiscount = 0;
  cart.forEach((item) => {
    const discount = item.discount || 0;
    totalDiscount += Number(discount);
  });
  document.getElementById("discount-input").value =
    parseFloat(totalDiscount).toFixed(2);
}

const populateSubCategories = (data) => {
  const subCatContainer = document.getElementById("fc-sub-cat-cont");
  subCatContainer.innerHTML = "";
  data.forEach((cat) => {
    const catContainer = document.createElement("div");
    catContainer.setAttribute("class", "sub-category-wrapper");
    catContainer.textContent = cat.sub_type_name;
    catContainer.setAttribute(
      "onclick",
      `foodFilter('${cat.sub_type_name}', event)`
    );
    subCatContainer.appendChild(catContainer);
  });
};

const setActiveWrapper = (event) => {
  const wrappers = document.querySelectorAll(
    "#fc-main-cat-cont .main-category-wrapper"
  );
  wrappers.forEach((btn) => {
    btn.classList.remove("active-mc-wrapper");
  });
  event.target.classList.add("active-mc-wrapper");
};

const setActiveWrapperSub = (event) => {
  const wrappers = document.querySelectorAll(
    "#fc-sub-cat-cont .sub-category-wrapper"
  );
  wrappers.forEach((btn) => {
    btn.classList.remove("active-sc-wrapper");
  });
  event.target.classList.add("active-sc-wrapper");
};

const serviceChargeHandler = () => {
  const totalAmount =
    parseFloat(document.getElementById("checkout-total-amount").textContent) ||
    0;

  if (isNaN(totalAmount)) {
    console.error("Invalid total amount");
    return;
  }
  let serviceCharge = 0;

  if (parseInt(document.getElementById("hotel-selector").value) == 1) {
    serviceCharge = (totalAmount * 10) / 100;
  } else {
    serviceCharge = 0;
  }

  document.getElementById("service_charge").textContent =
    serviceCharge.toFixed(2);
  document.getElementById("checkout-sub-total").textContent = (
    totalAmount + serviceCharge
  ).toFixed(2);

  console.log({
    total: totalAmount,
    service_charge: serviceCharge,
  });
};
