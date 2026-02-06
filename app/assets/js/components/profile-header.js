/**
 * Profile Header Component
 * Adds profile dropdown to store headers for authenticated users
 */

(function () {
  // Only run if CustomerAuth is available
  if (typeof CustomerAuth === "undefined") {
    console.warn("CustomerAuth not loaded, profile header disabled");
    return;
  }

  // Add profile dropdown to header
  function addProfileDropdown() {
    // Find cart button or header container
    const header = document.querySelector("header");
    if (!header) return;

    // Check if already added
    if (document.getElementById("customer-profile-dropdown")) return;

    // Create profile dropdown HTML
    const profileHTML = `
            <div id="customer-profile-dropdown" class="relative">
                <!-- Profile Button -->
                <button id="profile-toggle-btn" class="p-2 rounded-lg hover:bg-gray-100 hover:bg-opacity-20 transition-colors relative">
                    <span class="material-symbols-outlined">person</span>
                </button>

                <!-- Dropdown Menu -->
                <div id="profile-menu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900" id="dropdown-name">Loading...</p>
                        <p class="text-xs text-gray-500 truncate" id="dropdown-email">Loading...</p>
                    </div>
                    
                    <a href="profile.html" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-gray-600">person</span>
                        <span class="text-sm font-medium text-gray-700">My Profile</span>
                    </a>
                    
                    <a href="orders.html" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-gray-600">receipt_long</span>
                        <span class="text-sm font-medium text-gray-700">My Orders</span>
                    </a>
                    
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <button id="dropdown-logout-btn" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined text-red-600">logout</span>
                            <span class="text-sm font-medium text-red-600">Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

    // Find where to insert (next to cart button or at end of header nav)
    let insertTarget =
      header.querySelector('[href="cart.html"]')?.parentElement;

    // If no cart link, look for other common patterns
    if (!insertTarget) {
      insertTarget = header.querySelector(
        ".flex.items-center.gap-2, .flex.items-center.gap-4",
      );
    }

    if (insertTarget) {
      // Create temp element
      const temp = document.createElement("div");
      temp.innerHTML = profileHTML;
      const profileElement = temp.firstElementChild;

      // Insert into DOM
      insertTarget.appendChild(profileElement);

      // Set up event listeners
      setupProfileDropdown();
    }
  }

  // Setup dropdown functionality
  function setupProfileDropdown() {
    const toggleBtn = document.getElementById("profile-toggle-btn");
    const menu = document.getElementById("profile-menu");
    const logoutBtn = document.getElementById("dropdown-logout-btn");

    if (!toggleBtn || !menu) return;

    // Toggle dropdown
    toggleBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.classList.toggle("hidden");
    });

    // Close on click outside
    document.addEventListener("click", (e) => {
      if (!menu.contains(e.target) && !toggleBtn.contains(e.target)) {
        menu.classList.add("hidden");
      }
    });

    // Logout handler
    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => {
        menu.classList.add("hidden");
        if (confirm("Are you sure you want to logout?")) {
          CustomerAuth.logout();
        }
      });
    }

    // Load customer info
    loadCustomerInfo();
  }

  // Load customer information
  function loadCustomerInfo() {
    if (!CustomerAuth.isAuthenticated()) return;

    const customer = CustomerAuth.getCustomer();
    if (customer) {
      const nameEl = document.getElementById("dropdown-name");
      const emailEl = document.getElementById("dropdown-email");

      if (nameEl) {
        const fullName =
          `${customer.first_name || ""} ${customer.last_name || ""}`.trim();
        nameEl.textContent = fullName || customer.email || "User";
      }

      if (emailEl) {
        emailEl.textContent = customer.email || "";
      }
    }
  }

  // Show/hide profile dropdown based on auth status
  function updateProfileVisibility() {
    const profileDropdown = document.getElementById(
      "customer-profile-dropdown",
    );

    if (CustomerAuth.isAuthenticated()) {
      if (!profileDropdown) {
        addProfileDropdown();
      }
    } else {
      if (profileDropdown) {
        profileDropdown.remove();
      }
    }
  }

  // Initialize on page load
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", updateProfileVisibility);
  } else {
    updateProfileVisibility();
  }

  // Make available globally for manual updates
  window.ProfileHeader = {
    update: updateProfileVisibility,
    load: loadCustomerInfo,
  };
})();
