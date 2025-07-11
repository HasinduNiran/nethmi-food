let food_categories = [];
let food_subcategories = {};
let menu = [];
let food_cart = [];

document.addEventListener("DOMContentLoaded", () => {
  fetchMenu();
});

const cartCountIndicator = document.querySelector(".cart-item-counter");

function fetchMenu() {
  fetch("./logic/fetch_menu.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error("Error:", data.error);
        return;
      }

      menu = [...data];

      data.forEach((dish) => {
        if (!food_categories.includes(dish.item_type)) {
          food_categories.push(dish.item_type);
        }
        if (!food_subcategories[dish.item_type]) {
          food_subcategories[dish.item_type] = [];
        }

        if (
          dish.sub_item_type &&
          !food_subcategories[dish.item_type].includes(dish.sub_item_type)
        ) {
          food_subcategories[dish.item_type].push(dish.sub_item_type);
        }
      });

      const sideBarMenu = document.getElementById("sidebar-displayer-menu");
      const sideBar = document.getElementById("sidebar-category-menu");
      sideBarMenu.addEventListener("click", () => {
        sideBar.classList.toggle("hide-sidebar");
      });

      const sideBarLinkContainer = document.getElementById("category-links");
      sideBarLinkContainer.innerHTML = "";

      food_categories.forEach((category) => {
        const categoryDiv = document.createElement("div");
        categoryDiv.classList.add("category-group");

        const link = document.createElement("a");
        link.setAttribute("href", `#${category}`);
        link.textContent = category.replace("-", " ");
        link.classList.add("main-category-link");
        categoryDiv.appendChild(link);

        if (
          food_subcategories[category] &&
          food_subcategories[category].length > 0
        ) {
          const subLinksDiv = document.createElement("div");
          subLinksDiv.classList.add("subcategory-links");

          food_subcategories[category].forEach((subcat) => {
            const subLink = document.createElement("a");
            const subcatId = `${category}-${subcat}`.replace(/\s+/g, "-");
            subLink.setAttribute("href", `#${subcatId}`);
            subLink.textContent = subcat.replace("-", " ");
            subLink.classList.add("subcategory-link");
            subLinksDiv.appendChild(subLink);
          });

          categoryDiv.appendChild(subLinksDiv);
        }

        sideBarLinkContainer.appendChild(categoryDiv);
      });

      populateMenu(food_categories);
    })
    .catch((error) => console.error("Fetch error:", error));
}

const populateMenu = (catalog_array) => {
  const menuPartition = document.getElementById("lower-part");
  menuPartition.innerHTML = "";

  catalog_array.forEach((category) => {
    const category_container = document.createElement("div");
    category_container.setAttribute("id", category);
    category_container.setAttribute("class", "dish-container");

    const heading = document.createElement("div");
    heading.setAttribute("class", "dish-container-heading");
    heading.textContent = category;
    category_container.appendChild(heading);

    const hasSubcategories =
      food_subcategories[category] && food_subcategories[category].length > 0;

    if (hasSubcategories) {
      food_subcategories[category].forEach((subcategory) => {
        const subcategoryId = `${category}-${subcategory}`.replace(/\s+/g, "-");
        const subcategory_container = document.createElement("div");
        subcategory_container.setAttribute("id", subcategoryId);
        subcategory_container.setAttribute("class", "subcategory-container");

        const subheading = document.createElement("div");
        subheading.setAttribute("class", "subcategory-heading");
        subheading.textContent = subcategory;
        subcategory_container.appendChild(subheading);

        createMenuHeader(subcategory_container);

        menu.forEach((item) => {
          if (
            item.item_type === category &&
            item.sub_item_type === subcategory
          ) {
            createMenuItem(item, subcategory_container);
          }
        });

        category_container.appendChild(subcategory_container);
      });

      const itemsWithoutSubcategory = menu.filter(
        (item) =>
          item.item_type === category &&
          (!item.sub_item_type ||
            item.sub_item_type === "" ||
            item.sub_item_type === null)
      );

      if (itemsWithoutSubcategory.length > 0) {
        const noSubcat_container = document.createElement("div");
        noSubcat_container.setAttribute(
          "class",
          "subcategory-container no-subcategory"
        );

        const subheading = document.createElement("div");
        subheading.setAttribute("class", "subcategory-heading");
        subheading.textContent = "Other Items";
        noSubcat_container.appendChild(subheading);

        createMenuHeader(noSubcat_container);

        itemsWithoutSubcategory.forEach((item) => {
          createMenuItem(item, noSubcat_container);
        });

        category_container.appendChild(noSubcat_container);
      }
    } else {
      createMenuHeader(category_container);

      menu.forEach((item) => {
        if (item.item_type === category) {
          createMenuItem(item, category_container);
        }
      });
    }

    menuPartition.appendChild(category_container);
  });
};

function createMenuHeader(container) {
  const wrapper_div = document.createElement("div");
  const food_name_holder = document.createElement("span");
  wrapper_div.classList.add("menu-item", "header-row");
  food_name_holder.classList.add("food-name-holder");
  food_name_holder.innerText = "";
  const priceHolder = document.createElement("div");
  priceHolder.setAttribute("class", "price-container");
  const reg_price = document.createElement("span");
  const med_price = document.createElement("span");
  const large_price = document.createElement("span");
  reg_price.setAttribute("class", "portion-name");
  med_price.setAttribute("class", "portion-name");
  large_price.setAttribute("class", "portion-name");
  reg_price.innerText = "Regular";
  med_price.innerText = "Medium";
  large_price.innerText = "Large";
  priceHolder.appendChild(reg_price);
  priceHolder.appendChild(med_price);
  priceHolder.appendChild(large_price);
  wrapper_div.appendChild(food_name_holder);
  wrapper_div.appendChild(priceHolder);
  container.appendChild(wrapper_div);
}

function createMenuItem(item, container) {
  const wrapper_div = document.createElement("div");
  const food_name_holder = document.createElement("span");
  const priceHolder = document.createElement("div");
  priceHolder.setAttribute("class", "price-container");
  const food_price_holder_regular = document.createElement("span");
  const food_price_holder_medium = document.createElement("span");
  const food_price_holder_large = document.createElement("span");

  wrapper_div.classList.add("menu-item");
  food_name_holder.classList.add("food-name-holder");
  food_name_holder.innerText = item.item_name;
  food_price_holder_regular.classList.add("food-price-holder");
  food_price_holder_regular.setAttribute("data-portion", "regular");
  food_price_holder_regular.setAttribute("data-dish-name", `${item.item_name}`);
  food_price_holder_regular.innerText = `${parseFloat(
    item.regular_price
  ).toFixed(2)}`;
  food_price_holder_medium.classList.add("food-price-holder");
  food_price_holder_medium.setAttribute("data-portion", "medium");
  food_price_holder_medium.setAttribute("data-dish-name", `${item.item_name}`);
  food_price_holder_medium.innerText = `${parseFloat(item.medium_price).toFixed(
    2
  )}`;
  food_price_holder_large.classList.add("food-price-holder");
  food_price_holder_large.setAttribute("data-portion", "large");
  food_price_holder_large.setAttribute("data-dish-name", `${item.item_name}`);
  food_price_holder_large.innerText = `${parseFloat(item.large_price).toFixed(
    2
  )}`;

  priceHolder.appendChild(food_price_holder_regular);
  priceHolder.appendChild(food_price_holder_medium);
  priceHolder.appendChild(food_price_holder_large);
  wrapper_div.appendChild(food_name_holder);
  wrapper_div.appendChild(priceHolder);
  container.appendChild(wrapper_div);

  food_price_holder_regular.addEventListener("click", () => {
    addToCart(item.item_name, "regular", item.regular_price, item.item_id);
  });
  food_price_holder_medium.addEventListener("click", () => {
    addToCart(item.item_name, "medium", item.medium_price, item.item_id);
  });
  food_price_holder_large.addEventListener("click", () => {
    addToCart(item.item_name, "large", item.large_price, item.item_id);
  });
}

function countCartItems() {
  const tbody = document.querySelector("#cart-tbody");
  if (!tbody) return 0;
  const rows = tbody.querySelectorAll("tr");
  let totalItems = 0;
  rows.forEach((row) => {
    const quantityCell = row.querySelector(".quantity");
    totalItems += parseInt(quantityCell.textContent, 10);
  });

  return totalItems;
}

function addToCart(itemName, portion, price, itemId) {
  const foodCartHolder = document.querySelector(".food-cart-holder");
  let table = foodCartHolder.querySelector("table");
  let tbody;

  if (!table) {
    table = document.createElement("table");
    table.setAttribute("class", "cart-table");
    table.innerHTML = `
      <thead>
        <tr>
          <th>Dish Name</th>
          <th>Quantity</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="cart-tbody"></tbody>
    `;
    foodCartHolder.appendChild(table);
  }

  tbody = table.querySelector("#cart-tbody");

  let existingRow = null;
  const rows = tbody.querySelectorAll("tr");
  rows.forEach((row) => {
    const dishText = row.querySelector("td:nth-child(1)").textContent;
    const match = dishText.match(/^(.*)\s\((.*)\)$/);

    if (match) {
      const dishName = match[1].trim();
      const dishPortion = match[2].trim();

      if (dishName === itemName && dishPortion === portion) {
        existingRow = row;
      }
    }
  });

  if (existingRow) {
    const quantityCell = existingRow.querySelector("td:nth-child(2)");
    const currentQuantity = parseInt(quantityCell.textContent, 10);
    quantityCell.textContent = currentQuantity + 1;
    cartCountIndicator.innerHTML = countCartItems();
  } else {
    const newRow = document.createElement("tr");
    newRow.setAttribute("data-item-id", itemId);
    newRow.innerHTML = `
      <td>${itemName} (${portion})</td>
      <td class="quantity">1</td>
      <td class="action-buttons">
        <button class="decrement-btn">-</button>
        <button class="increment-btn">+</button>
        <button class="remove-btn">×</button>
      </td>
      <td style="color: transparent;">${itemId}</td>
    `;
    const incrementBtn = newRow.querySelector(".increment-btn");
    const decrementBtn = newRow.querySelector(".decrement-btn");
    const removeBtn = newRow.querySelector(".remove-btn");

    incrementBtn.addEventListener("click", function () {
      const quantityCell =
        this.parentNode.parentNode.querySelector(".quantity");
      const currentQty = parseInt(quantityCell.textContent, 10);
      quantityCell.textContent = currentQty + 1;
      cartCountIndicator.innerHTML = countCartItems();
    });

    decrementBtn.addEventListener("click", function () {
      const quantityCell =
        this.parentNode.parentNode.querySelector(".quantity");
      const currentQty = parseInt(quantityCell.textContent, 10);
      if (currentQty > 1) {
        quantityCell.textContent = currentQty - 1;
      } else {
        this.parentNode.parentNode.remove();
        if (tbody.querySelectorAll("tr").length === 0) {
          const finalizeButton =
            foodCartHolder.querySelector(".finalize-button");
          if (finalizeButton) {
            finalizeButton.remove();
          }
        }
      }
      cartCountIndicator.innerHTML = countCartItems();
    });

    removeBtn.addEventListener("click", function () {
      this.parentNode.parentNode.remove();
      if (tbody.querySelectorAll("tr").length === 0) {
        const finalizeButton = foodCartHolder.querySelector(".finalize-button");
        if (finalizeButton) {
          finalizeButton.remove();
        }
      }
      cartCountIndicator.innerHTML = countCartItems();
    });

    tbody.appendChild(newRow);
    cartCountIndicator.innerHTML = countCartItems();
  }

  const finalizeButton = foodCartHolder.querySelector(".finalize-button");
  if (!finalizeButton) {
    const button = document.createElement("button");
    button.classList.add("finalize-button");
    button.textContent = "Finalize Order";
    button.addEventListener("click", () => {
      logCartData();
    });
    foodCartHolder.appendChild(button);
  }
}

function logCartData() {
  const tableIdHolder = document.getElementById("table-id-holder");
  const tableId = document.getElementById("table-id-holder").value;
  const tbody = document.querySelector("#cart-tbody");
  const rows = tbody.querySelectorAll("tr");

  if (rows.length === 0) {
    console.error("Cart is empty!");
    return;
  }

  const cartData = [];

  const foodCart = document.querySelector(".food-cart-holder");
  rows.forEach((row) => {
    const itemId = row.getAttribute("data-item-id");
    const dishText = row.querySelector("td:nth-child(1)").textContent;
    const quantity = parseInt(row.querySelector(".quantity").textContent, 10);
    const match = dishText.match(/^(.*)\s\((.*)\)$/);
    const itemName = match ? match[1].trim() : dishText;
    const portion = match ? match[2].trim() : "";

    cartData.push({
      name: itemName,
      portion: portion,
      quantity: quantity,
      itemId: itemId,
    });
  });

  fetch("./logic/create_dine_in_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ table_id: tableId, cart_data: cartData }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        const orderId = data.order_id;

        return fetch("./logic/create_dine_in_record.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            order_id: orderId,
            cart_data: cartData,
          }),
        });
      } else {
        throw new Error("Failed to create order: " + data.message);
      }
    })
    .then((res) => res.json())
    .then((response) => {
      console.log(response);

      let notifier = new AWN();
      foodCart.classList.remove("show-food-cart");
      tableIdHolder.value = "";
      notifier.success("Your order has been placed!");
    })
    .catch((error) => console.error("Error:", error));
}

const foodCartDisplayer = document.querySelector(".client-cart-icon");

foodCartDisplayer.addEventListener("click", () => {
  const foodCart = document.querySelector(".food-cart-holder");
  foodCart.classList.toggle("show-food-cart");
});
