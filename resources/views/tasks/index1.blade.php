@extends('layouts.app')

@section('content')
<div class="container app-container">
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-auto">
            <h1><i class="fas fa-tasks"></i> Lista de Tareas</h1>
        </div>
        <div class="col-auto">
        <button type="button" class="btn btn-success mb-4" data-toggle="modal" data-target="#staticBackdrop"> Crear Nueva Tarea </button>
        </div>
    </div>


<table id="tasks-table" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Usuario Asignado</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
   
    </tbody>
</table>
</div>
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tarea Nueva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="task-form">
        @csrf 
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Título:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Categoría:</label>
                    <select class="form-control" id="category" name="category_id" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        @if($categories)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Ok</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- <div class="modal fade" id="edit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="edit-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modal-label">Editar Tarea</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="edit-title">Título:</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Descripción:</label>
                        <textarea class="form-control" id="edit-description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-category">Categoría:</label>
                        <select class="form-control" id="edit-category" name="category_id" required>
                            <option value="" disabled selected>Selecciona una categoría</option>
                            @if($categories)
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" form="edit-form">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div> -->

<script>
    toastr.options = {
    "positionClass": "toast-top-right", 
    "preventDuplicates": true, 
    "showDuration": 300, 
    "hideDuration": 1000, 
    "timeOut": 5000, 
    "extendedTimeOut": 1000, 
    "toastClass": "toast", 
    };
$(document).ready(function() {

    $('#tasks-table').DataTable({
        "serverSide": true,
        "ajax": "{{ route('tasks.index1') }}",
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
            url: "{{ route('tasks.store') }}", 
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
            url: "{{ route('tasks.toggleStatus', ':taskId') }}".replace(':taskId', taskId),
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
       
        $.ajax({
            url: "{{ route('tasks.show', ':taskId') }}".replace(':taskId', taskId),
            type: 'GET',
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
                        url: "{{ route('tasks.update', ':taskId') }}".replace(':taskId', taskId),
                        type: 'PUT',
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
                    url: "{{ route('tasks.destroy', ':taskId') }}".replace(':taskId', taskId),
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
        }
        toastr.success(message);
    }

    function showErrorMessage(message) {
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
        }
        toastr.error(message);
    }

</script>
@endsection
