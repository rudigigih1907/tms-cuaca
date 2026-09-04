function initGridEvents() {
    var container = $('#user-grid-container');
    var toggleStatusUrl = container.data('toggle-status-url');
    var changeRoleUrl = container.data('change-role-url');

    function showAlert(message, isSuccess) {
        var alertBox = $('#ajax-alert');
        alertBox.removeClass('d-none alert-success alert-danger')
            .addClass(isSuccess ? 'alert-success' : 'alert-danger');
        $('#ajax-alert-message').text(message);
    }

    // Unbind & Re-bind Handler Toggle Status
    $(document).off('change', '.status-toggle').on('change', '.status-toggle', function () {
        var checkbox = $(this);
        var userId = checkbox.data('user-id');

        $.ajax({
            url: toggleStatusUrl,
            type: 'POST',
            data: { id: userId },
            success: function (response) {
                if (response.success) {
                    showAlert(response.message, true);
                    var labelHtml = (response.status == 10)
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>'
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>';
                    $('#status-label-' + userId).html(labelHtml);
                } else {
                    showAlert(response.message, false);
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            },
            error: function () {
                showAlert('Terjadi kesalahan jaringan.', false);
                checkbox.prop('checked', !checkbox.is(':checked'));
            }
        });
    });

    // Unbind & Re-bind Handler Change Role Dropdown
    $(document).off('change', '.role-dropdown').on('change', '.role-dropdown', function () {
        var select = $(this);
        var userId = select.data('user-id');
        var roleName = select.val();

        $.ajax({
            url: changeRoleUrl,
            type: 'POST',
            data: { userId: userId, roleName: roleName },
            success: function (response) {
                if (response.success) {
                    showAlert(response.message, true);
                } else {
                    showAlert(response.message, false);
                }
            },
            error: function () {
                showAlert('Terjadi kesalahan jaringan saat mengubah role.', false);
            }
        });
    });
}

// Inisialisasi awal saat DOM siap
$(document).ready(function () {
    initGridEvents();
});

// Re-bind listener setelah Pjax merender ulang GridView
$(document).on('pjax:success', function () {
    initGridEvents();
});