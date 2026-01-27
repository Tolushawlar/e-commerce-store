/**
 * Shared Components
 * Reusable UI components
 */

const components = {
  /**
   * Loading spinner
   */
  spinner: `
    <div class="flex items-center justify-center p-8">
      <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
    </div>
  `,

  /**
   * Empty state
   */
  emptyState(message, icon = "inbox") {
    return `
      <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-gray-300">${icon}</span>
        <p class="mt-4 text-gray-500">${message}</p>
      </div>
    `;
  },

  /**
   * Error state
   */
  errorState(message) {
    return `
      <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-red-300">error</span>
        <p class="mt-4 text-red-500">${message}</p>
      </div>
    `;
  },

  /**
   * Status badge
   */
  statusBadge(status) {
    const badgeClass = utils.getStatusClass(status);
    return `<span class="px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">${status}</span>`;
  },

  /**
   * Pagination
   */
  pagination(currentPage, totalPages, onPageChange) {
    let html = '<div class="flex items-center gap-2 justify-center mt-6">';

    // Previous button
    const prevDisabled = currentPage === 1;
    html += `
      <button 
        ${!prevDisabled ? `onclick="${onPageChange}(${currentPage - 1})"` : ""}
        ${prevDisabled ? "disabled" : ""}
        class="px-4 py-2 border rounded-lg ${prevDisabled ? "bg-gray-100 text-gray-400 cursor-not-allowed" : "hover:bg-gray-50"}">
        Previous
      </button>
    `;

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
      const activeClass =
        i === currentPage ? "bg-primary text-white" : "hover:bg-gray-50";
      html += `
        <button 
          onclick="${onPageChange}(${i})"
          class="px-4 py-2 border rounded-lg ${activeClass}">
          ${i}
        </button>
      `;
    }

    // Next button
    const nextDisabled = currentPage === totalPages;
    html += `
      <button 
        ${!nextDisabled ? `onclick="${onPageChange}(${currentPage + 1})"` : ""}
        ${nextDisabled ? "disabled" : ""}
        class="px-4 py-2 border rounded-lg ${nextDisabled ? "bg-gray-100 text-gray-400 cursor-not-allowed" : "hover:bg-gray-50"}">
        Next
      </button>
    `;

    html += "</div>";
    return html;
  },

  /**
   * Modal
   */
  modal(id, title, content, footer = "") {
    return `
      <div id="${id}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-auto">
          <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">${title}</h2>
            <button onclick="components.closeModal('${id}')" class="text-gray-400 hover:text-gray-600">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <div class="p-6">
            ${content}
          </div>
          ${footer ? `<div class="p-6 border-t bg-gray-50">${footer}</div>` : ""}
        </div>
      </div>
    `;
  },

  /**
   * Open modal
   */
  openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }
  },

  /**
   * Close modal
   */
  closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.add("hidden");
      document.body.style.overflow = "";
    }
  },

  /**
   * Table
   */
  table(headers, rows, actions = []) {
    let html =
      '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';

    // Headers
    html += '<thead class="bg-gray-50"><tr>';
    headers.forEach((header) => {
      html += `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${header}</th>`;
    });
    if (actions.length > 0) {
      html +=
        '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>';
    }
    html += "</tr></thead>";

    // Body
    html += '<tbody class="bg-white divide-y divide-gray-200">';
    rows.forEach((row) => {
      html += '<tr class="hover:bg-gray-50">';
      row.forEach((cell) => {
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cell}</td>`;
      });
      if (actions.length > 0) {
        html +=
          '<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">';
        html += '<div class="flex items-center justify-end gap-2">';
        actions.forEach((action) => {
          html += action;
        });
        html += "</div></td>";
      }
      html += "</tr>";
    });
    html += "</tbody></table></div>";

    return html;
  },
};
