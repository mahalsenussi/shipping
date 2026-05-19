// Main JavaScript functionality for the shipping management system

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Open modal for adding new items
function openAddNew(type) {
    switch(type) {
        case 'customer':
            $('#addCustomerModal').modal('show');
            break;
        case 'shipping_line':
            $('#addShippingLineModal').modal('show');
            break;
        case 'local_agent':
            $('#addAgentModal').modal('show');
            break;
        case 'vessel':
            $('#addVesselModal').modal('show');
            break;
        case 'port':
            $('#addPortModal').modal('show');
            break;
        default:
            console.error('Unknown type for openAddNew:', type);
    }
}

// Initialize form functionality when document is ready
$(document).ready(function() {
    // Set up form validation
    setupFormValidation();

    // Set up modal form submissions
    setupModalSubmissions();

    // Set up dynamic form behaviors
    setupDynamicBehaviors();
});

function setupFormValidation() {
    // Add real-time validation feedback
    $('input[required], select[required], textarea[required]').on('blur', function() {
        validateField($(this));
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        const $form = $(this);
        let isValid = true;

        $form.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function validateField($field) {
    const value = $field.val().trim();
    const isRequired = $field.attr('required');

    $field.removeClass('is-valid is-invalid');

    if (isRequired && !value) {
        $field.addClass('is-invalid');
        return false;
    } else if (value) {
        $field.addClass('is-valid');
    }

    return true;
}

function setupModalSubmissions() {
    // Customer form submission
    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        submitModalForm($(this), 'customers', 'store_customer', 'customer_id');
    });

    // Shipping Line form submission
    $('#addShippingLineForm').on('submit', function(e) {
        e.preventDefault();
        submitModalForm($(this), 'shipping_lines', 'store_shipping_line', 'shipping_line_id');
    });

    // Agent form submission
    $('#addAgentForm').on('submit', function(e) {
        e.preventDefault();
        submitModalForm($(this), 'agents', 'store_agent', 'local_agent_id');
    });

    // Vessel form submission
    $('#addVesselForm').on('submit', function(e) {
        e.preventDefault();
        submitModalForm($(this), 'vessels', 'add_vessel', 'vessel_id');
    });

    // Port form submission
    $('#addPortForm').on('submit', function(e) {
        e.preventDefault();
        submitModalPortForm($(this));
    });
}

function submitModalForm($form, page, action, selectName) {
    const nameField = $form.find('input[type="text"]').first();
    const name = nameField.val().trim();

    if (!name) {
        nameField.addClass('is-invalid');
        return;
    }

    $.ajax({
        url: `?page=${page}&action=${action}`,
        method: 'POST',
        data: { name: name },
        success: function(response) {
            if (response.success) {
                // Add new option to select
                const select = $(`select[name="${selectName}"]`);
                select.append(`<option value="${response.id}" selected>${escapeHtml(name)}</option>`);

                // Close modal and reset form
                const modalId = $form.closest('.modal').attr('id');
                $(`#${modalId}`).modal('hide');
                $form[0].reset();
                nameField.removeClass('is-invalid');

                // Show success message
                showAlert('تم إضافة العنصر بنجاح', 'success');
            } else {
                showAlert('خطأ في إضافة العنصر: ' + response.message, 'danger');
            }
        },
        error: function() {
            showAlert('خطأ في الاتصال بالخادم', 'danger');
        }
    });
}

function submitModalPortForm($form) {
    const nameField = $form.find('input[type="text"]').first();
    const name = nameField.val().trim();

    if (!name) {
        nameField.addClass('is-invalid');
        return;
    }

    $.ajax({
        url: '?page=ports&action=add_port',
        method: 'POST',
        data: { name: name },
        success: function(response) {
            if (response.success) {
                // Add new option to both port selects (origin and destination)
                const originSelect = $('select[name="origin_port_id"]');
                const destinationSelect = $('select[name="destination_port_id"]');

                const optionHtml = `<option value="${response.id}">${escapeHtml(name)}</option>`;

                // If no port is currently selected, select the new one for origin
                if (!originSelect.val()) {
                    originSelect.append(optionHtml);
                    originSelect.val(response.id);
                } else {
                    originSelect.append(optionHtml);
                }

                // If no port is currently selected, select the new one for destination
                if (!destinationSelect.val()) {
                    destinationSelect.append(optionHtml);
                    destinationSelect.val(response.id);
                } else {
                    destinationSelect.append(optionHtml);
                }

                // Close modal and reset form
                $('#addPortModal').modal('hide');
                $form[0].reset();
                nameField.removeClass('is-invalid');

                // Show success message
                showAlert('تم إضافة الميناء بنجاح', 'success');
            } else {
                showAlert('خطأ في إضافة الميناء: ' + response.message, 'danger');
            }
        },
        error: function() {
            showAlert('خطأ في الاتصال بالخادم', 'danger');
        }
    });
}

function setupDynamicBehaviors() {
    // Auto-format numeric inputs
    $('input[type="number"]').on('input', function() {
        const value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value));
        }
    });

    // Confirm delete actions
    $('[data-confirm]').on('click', function(e) {
        const message = $(this).data('confirm');
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
}

function showAlert(message, type = 'info') {
    // Create alert element
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Add to page (you might want to customize where this appears)
    $('.container').first().prepend(alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        $('.alert').first().fadeOut();
    }, 5000);
}
