<?php
$pageTitle = 'Templates';
$pageDescription = 'Manage store design templates and themes';
include '../shared/header-admin.php';
?>

<!-- Template Stats -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600">web</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="totalTemplates">-</h3>
        <p class="text-sm text-gray-500">Total Templates</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="activeTemplates">-</h3>
        <p class="text-sm text-gray-500">Active Templates</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">store</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="storesUsingTemplates">-</h3>
        <p class="text-sm text-gray-500">Stores Using Templates</p>
    </div>
</div>

<!-- Header Actions -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <input type="text" id="searchInput" placeholder="Search templates..."
            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            oninput="handleSearch()">
    </div>
    <button onclick="openAddTemplateModal()"
        class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
        <span class="material-symbols-outlined">add</span>
        Add Template
    </button>
</div>

<!-- Templates Grid -->
<div id="templatesGrid">
    <div class="flex items-center justify-center p-12">
        <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
    </div>
</div>

<!-- Pagination -->
<div id="pagination" class="mt-6"></div>

<!-- Add/Edit Template Modal -->
<div id="templateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Template</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="templateForm" onsubmit="handleSubmit(event)" class="p-6">
            <input type="hidden" id="templateId">

            <div class="space-y-4">
                <!-- Template Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Template Name *</label>
                    <input type="text" id="name" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="Modern E-commerce">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Description *</label>
                    <textarea id="description" required rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="A clean, modern template perfect for..."></textarea>
                </div>

                <!-- Preview Image URL -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Preview Image URL</label>
                    <input type="url" id="preview_image"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="https://example.com/preview.jpg">
                    <p class="text-xs text-gray-500 mt-1">URL to template preview image</p>
                </div>

                <!-- HTML Template -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">HTML Template</label>
                    <textarea id="html_template" rows="8"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary font-mono text-sm"
                        placeholder="<div>...</div>"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Base HTML structure for the template</p>
                </div>

                <!-- CSS Template -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">CSS Template</label>
                    <textarea id="css_template" rows="8"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary font-mono text-sm"
                        placeholder=".container { ... }"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Custom CSS styles for the template</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 justify-end mt-6 pt-6 border-t">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Template Details Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Template Details</h2>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="templateDetails" class="p-6">
            <!-- Details will be loaded here -->
        </div>
        <div class="p-6 border-t bg-gray-50 flex gap-3 justify-end">
            <button onclick="closeViewModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<script src="/assets/js/services/template.service.js"></script>
<script src="/assets/js/services/store.service.js"></script>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let allTemplates = [];

    // Load templates
    async function loadTemplates(page = 1) {
        try {
            currentPage = page;
            const params = {
                page: currentPage,
                limit: 12
            };

            const response = await templateService.getAll(params);
            allTemplates = response.data?.templates || [];
            const pagination = response.data?.pagination || {};

            // Update stats
            document.getElementById('totalTemplates').textContent = pagination.total_items || 0;

            // Calculate active templates (for demo - in real app, add status field)
            const activeCount = allTemplates.length;
            document.getElementById('activeTemplates').textContent = activeCount;

            // Load stores count using templates
            loadStoresCount();

            // Apply search filter if needed
            let filteredTemplates = allTemplates;
            if (currentSearch) {
                filteredTemplates = allTemplates.filter(template =>
                    template.name.toLowerCase().includes(currentSearch.toLowerCase()) ||
                    (template.description && template.description.toLowerCase().includes(currentSearch.toLowerCase()))
                );
            }

            displayTemplates(filteredTemplates);
            displayPagination(pagination);

        } catch (error) {
            console.error('Error loading templates:', error);
            document.getElementById('templatesGrid').innerHTML =
                components.errorState('Failed to load templates');
        }
    }

    // Load stores count
    async function loadStoresCount() {
        try {
            const response = await storeService.getAll({
                limit: 1
            });
            const total = response.data?.pagination?.total || 0;
            document.getElementById('storesUsingTemplates').textContent = total;
        } catch (error) {
            console.error('Error loading stores count:', error);
        }
    }

    // Display templates grid
    function displayTemplates(templates) {
        const container = document.getElementById('templatesGrid');

        if (templates.length === 0) {
            container.innerHTML = components.emptyState(
                currentSearch ? 'No templates found' : 'No templates yet. Add your first template!',
                'web'
            );
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';

        templates.forEach(template => {
            const previewImage = template.preview_image || '';
            const hasPreview = previewImage && previewImage.trim() !== '';

            html += `
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Template Preview -->
                    <div class="aspect-video relative ${hasPreview ? '' : 'bg-gradient-to-br from-primary to-primary/80'}">
                        ${hasPreview ? 
                            `<img src="${previewImage}" alt="${template.name}" class="w-full h-full object-cover">` :
                            `<div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-16 h-16 bg-accent rounded-xl mx-auto mb-3 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary text-3xl">web</span>
                                    </div>
                                    <h3 class="text-xl font-bold">${template.name}</h3>
                                </div>
                            </div>`
                        }
                    </div>

                    <!-- Template Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">${template.name}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2">${template.description || 'No description available'}</p>
                        </div>

                        <div class="text-xs text-gray-500 mb-4">
                            <span class="material-symbols-outlined text-xs align-middle">schedule</span>
                            ${utils.formatDate(template.created_at)}
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button onclick="viewTemplate(${template.id})" 
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold text-sm">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                View
                            </button>
                            <button onclick="editTemplate(${template.id})" 
                                class="px-4 py-2 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 font-semibold text-sm">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="deleteTemplate(${template.id}, '${template.name.replace(/'/g, "\\'")}'))" 
                                class="px-4 py-2 text-red-600 border border-red-200 rounded-lg hover:bg-red-50 font-semibold text-sm">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // Display pagination
    function displayPagination(pagination) {
        const container = document.getElementById('pagination');

        if (!pagination || pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const {
            current_page,
            total_pages
        } = pagination;
        let html = '<div class="flex items-center justify-center gap-2">';

        // Previous
        html += `
            <button onclick="loadTemplates(${current_page - 1})" ${current_page === 1 ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </button>
        `;

        // Pages
        for (let i = 1; i <= total_pages; i++) {
            if (i === 1 || i === total_pages || (i >= current_page - 2 && i <= current_page + 2)) {
                html += `
                    <button onclick="loadTemplates(${i})"
                        class="w-10 h-10 rounded-lg font-semibold ${i === current_page ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            } else if (i === current_page - 3 || i === current_page + 3) {
                html += '<span class="px-2">...</span>';
            }
        }

        // Next
        html += `
            <button onclick="loadTemplates(${current_page + 1})" ${current_page === total_pages ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </button>
        `;

        html += '</div>';
        container.innerHTML = html;
    }

    // Search handler
    const handleSearch = utils.debounce(function() {
        currentSearch = document.getElementById('searchInput').value;
        loadTemplates(1);
    }, 300);

    // Open add template modal
    function openAddTemplateModal() {
        document.getElementById('modalTitle').textContent = 'Add Template';
        document.getElementById('templateForm').reset();
        document.getElementById('templateId').value = '';
        document.getElementById('templateModal').classList.remove('hidden');
    }

    // View template details
    async function viewTemplate(id) {
        try {
            const response = await templateService.getById(id);
            const template = response.data;

            const details = `
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Template Name</label>
                                <p class="text-gray-900">${template.name}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Created</label>
                                <p class="text-gray-900">${utils.formatDate(template.created_at)}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                                <p class="text-gray-900">${template.description || 'No description'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Image -->
                    ${template.preview_image ? `
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Preview Image</h3>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <img src="${template.preview_image}" alt="${template.name}" class="w-full">
                            </div>
                        </div>
                    ` : ''}

                    <!-- HTML Template -->
                    ${template.html_template ? `
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">HTML Template</h3>
                            <pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto text-xs font-mono border border-gray-200">${utils.escapeHtml(template.html_template)}</pre>
                        </div>
                    ` : ''}

                    <!-- CSS Template -->
                    ${template.css_template ? `
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4">CSS Template</h3>
                            <pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto text-xs font-mono border border-gray-200">${utils.escapeHtml(template.css_template)}</pre>
                        </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('templateDetails').innerHTML = details;
            document.getElementById('viewModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading template details:', error);
            utils.toast('Failed to load template details', 'error');
        }
    }

    // Edit template
    async function editTemplate(id) {
        try {
            const response = await templateService.getById(id);
            const template = response.data;

            document.getElementById('modalTitle').textContent = 'Edit Template';
            document.getElementById('templateId').value = template.id;
            document.getElementById('name').value = template.name;
            document.getElementById('description').value = template.description || '';
            document.getElementById('preview_image').value = template.preview_image || '';
            document.getElementById('html_template').value = template.html_template || '';
            document.getElementById('css_template').value = template.css_template || '';

            document.getElementById('templateModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading template:', error);
            utils.toast('Failed to load template details', 'error');
        }
    }

    // Handle form submit
    async function handleSubmit(event) {
        event.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        const templateId = document.getElementById('templateId').value;
        const data = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            preview_image: document.getElementById('preview_image').value || null,
            html_template: document.getElementById('html_template').value || null,
            css_template: document.getElementById('css_template').value || null
        };

        try {
            if (templateId) {
                await templateService.update(templateId, data);
                utils.toast('Template updated successfully!', 'success');
            } else {
                await templateService.create(data);
                utils.toast('Template created successfully!', 'success');
            }

            closeModal();
            loadTemplates(currentPage);

        } catch (error) {
            console.error('Error saving template:', error);
            utils.toast(error.message || 'Failed to save template', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Template';
        }
    }

    // Delete template
    async function deleteTemplate(id, name) {
        if (!confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) return;

        try {
            await templateService.delete(id);
            utils.toast('Template deleted successfully!', 'success');
            loadTemplates(currentPage);
        } catch (error) {
            console.error('Error deleting template:', error);
            utils.toast('Failed to delete template', 'error');
        }
    }

    // Close modals
    function closeModal() {
        document.getElementById('templateModal').classList.add('hidden');
        document.getElementById('templateForm').reset();
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
    }

    // Initialize
    loadTemplates();
</script>