toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(document).ready(function() {

    $('#tasks-table').DataTable({
        "serverSide": true,
        "ajax": "/",
        "columns": [
            { "data": "id" },
            { "data": "title" },
            { "data": "description" },
            { "data": "category.name" },
            { "data": "assigned_user.name" },
            { "data": "completed" },
            { "data": "actions", "orderable": false, "searchable": false }
        ],
        "ordering": true,
        "searching": false,
        "paging": true,
        "language": {
            "sLengthMenu": "Mostrar _MENU_ registros por página",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
        }
    });

    $('#task-form').submit(function(e) {
        e.preventDefault(); 

        var formData = $(this).serialize();

        $.ajax({
            url: "/tareas", 
            type: 'POST',
            data: formData,
            success: function(data) {
                
                $('#staticBackdrop').modal('hide');
                $('#tasks-table').DataTable().ajax.reload();

                showSuccessMessage(data.message)
            },
            error: function(xhr) {
                console.error(xhr);
                showErrorMessage('Error al crear la tarea. Por favor, inténtalo de nuevo.');
            }
        });
    });
});

function toggleTaskStatus(taskId) {
    $.ajax({
        url: "/tareas/"+taskId+"/toggle-estado",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            showSuccessMessage(data.message)
            $('#tasks-table').DataTable().ajax.reload();
        },
        error: function(xhr) {
            console.error(xhr);
            showErrorMessage('Error al crear la tarea. Por favor, inténtalo de nuevo.');
        }
    });
}

$(document).on('change', '.toggle-status-checkbox', function() {
    var taskId = $(this).data('task-id');
    var completed = this.checked;

    $.ajax({
        url: "{{ route('tasks.toggleStatus', ':taskId') }}".replace(':taskId', taskId),
        type: 'POST',
        data: {
            completed: completed
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            showSuccessMessage(data.message)
            $('#tasks-table').DataTable().ajax.reload();
        },
        error: function(xhr) {
            console.error(xhr);
            showErrorMessage('Error al cambiar el estado de la tarea. Por favor, inténtalo de nuevo.');
        }
    });
});

function editTask(taskId) {
    $('#edit-form').off('submit');
    $.ajax({
        url: "/tareas/"+taskId,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log(data)
            $('#edit-title').val(data.task.title);
            $('#edit-description').val(data.task.description);
            $('#edit-category').val(data.task.category_id);

            $('#edit-modal').modal('show');

            $('#edit-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "/tareas/"+taskId,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {

                        $('#edit-modal').modal('hide');

                        $('#tasks-table').DataTable().ajax.reload();

                        showSuccessMessage(response.message);
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        showErrorMessage('Error al editar la tarea. Por favor, inténtalo de nuevo.');
                    }
                });
            });
        },
        error: function(xhr) {
            console.error(xhr);
            showErrorMessage('Error al obtener los detalles de la tarea. Por favor, inténtalo de nuevo.');
        }
    });
}


function deleteTask(taskId) {
    Swal.fire({

        title: '¿Estás seguro de que deseas eliminar esta tarea?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'

    }).then((result) => {

        if (result.isConfirmed) {
            $.ajax({
                url: "/tareas/"+taskId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    showSuccessMessage(data.message)
                    $('#tasks-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    console.error(xhr);
                    showErrorMessage('Error al eliminar la tarea. Por favor, inténtalo de nuevo.');
                }
            });
        }

    });
}

function showSuccessMessage(message) {
    toastr.success(message);
}

function showErrorMessage(message) {
    toastr.error(message);
}

