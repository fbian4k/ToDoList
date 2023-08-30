@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 mt-4  pd-4">
    <center><h2> ToDoList App</h2></center>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6 offset-lg-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Nueva Tarea</h2>
                    <form id="createTaskForm">
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" id="newTitle" placeholder="Título">
                        </div>
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" id="newDescription" placeholder="Descripción">
                        </div>
                            <button type="submit" class="btn btn-sm btn-primary" id="createTask">Crear</button>
                    </form>
                </div>
            </div>
        </div>
        
            <div class="card mb-4">
                <div class="card-body">
                    <h2>Tareas Pendientes</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                                
                            </tr>
                        </thead>
                        <tbody id="pendingTasksTable">
                        @foreach ($pendingTasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->description }}</td>
                                    <td>
                                        @if ($task->completed)
                                        @else
                                            <button class="btn btn-sm btn-primary edit-task mx-3 my-1" data-id="{{ $task->id }}">Editar</button>
                                            <button class="btn btn-sm btn-success complete-task mx-3 my-1" data-id="{{ $task->id }}">Completar</button>
                                            <button class="btn btn-sm btn-danger delete-task mx-3 my-1" data-id="{{ $task->id }}">Eliminar</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                <h2>Tareas Completadas</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acción</th>
                            
                        </tr>
                    </thead>
                    <tbody id="completedTasksTable">
                        @foreach ($completedTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
           
    </div>
</div>
<div id="editTaskModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tarea</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                @if(isset($task))
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $task->id }}">
                    <div class="form-group">
                        <label for="editTitle">Título</label>
                        <input type="text" class="form-control" id="editTitle" name="title" value="{{ $task->title }}" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Descripción</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required>{{ $task->description }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
                @endif
                </div>
            </div>
        </div>
    </div>


<script>
    $(document).ready(function() {
        loadTasks();
        
    });
    
    function loadTasks() {
    $.ajax({
        url: "{{ route('tasks.getTasks') }}",
        method: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            var completedTasksHtml = [];
            var pendingTasksHtml = [];

            response.completedTasks.forEach(function (task) {
                completedTasksHtml.push('<tr>' +
                    '<td>' + task.title + '</td>' +
                    '<td>' + task.description + '</td>' +
                    '<td>Completada</td>' +
                    '<td>' +
                    '<button class="btn btn-sm btn-danger delete-task" data-id="' + task.id + '">Eliminar</button>' +
                    '</td>' +
                    '</tr>');
            });

            response.pendingTasks.forEach(function (task) {
                pendingTasksHtml.push('<tr>' +
                    '<td>' + task.title + '</td>' +
                    '<td>' + task.description + '</td>' +
                    '<td>Pendiente</td>' +
                    '<td>' +
                    '<button class="btn btn-sm btn-primary edit-task mx-3 my-1" data-id="' + task.id + '">Editar</button>' +
                    '<button class="btn btn-sm btn-success complete-task mx-3 my-1" data-id="' + task.id + '">Completar</button>' +
                    '<button class="btn btn-sm btn-danger delete-task mx-3 my-1" data-id="' + task.id + '">Eliminar</button>' +
                    '</td>' +
                    '</tr>');
            });

            $('#completedTasksTable').html(completedTasksHtml.join(''));
            $('#pendingTasksTable').html(pendingTasksHtml.join(''));
        }
    });
}
    $('#createTask').on('click', function() {
        event.preventDefault();
        var newTitle = $('#newTitle').val();
        var newDescription = $('#newDescription').val();
        
        $.ajax({
            type: 'POST',
            url: '{{ route('tasks.store') }}',
            data: {
                title: newTitle,
                description: newDescription,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#newTitle').val('');
                $('#newDescription').val('');
                loadTasks();
                // toastr.success(response.message)
                Swal.fire(
                'Ok',
                response.message,
                'success'
                )
                
            },
            error: function(error) {
                Swal.fire({
                    title: '):',
                    text: 'Error al crear la tarea.',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
                // toastr.success('Error al crear la tarea.')
            }
        });
    });
    
    $(document).on('click', '.complete-task', function() {
        var taskId = $(this).data('id');
        $.ajax({
            url: "{{ url('tasks') }}/" + taskId + "/complete",
            type: "PUT",
            data: {
                "_token": "{{ csrf_token() }}"
            },
            success: function(response) {
                loadTasks();
                Swal.fire(
                'Ok',
                response.message,
                'success'
                )
            },
            error: function(error) {
                Swal.fire({
                    title: '):',
                    text: 'Error al completar la tarea.',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
            }
        });
    });

    $(document).on('click', '.delete-task', function() {
        var taskId = $(this).data('id');
        $.ajax({
            url: "{{ url('tasks') }}/" + taskId,
            type: "DELETE",
            data: {
                "_token": "{{ csrf_token() }}"
            },
            success: function(response) {
                loadTasks();
                Swal.fire(
                'Ok',
                response.message,
                'success'
                )
            },
            error: function(error) {
                Swal.fire({
                    title: '):',
                    text: 'Error al completar la tarea.',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
            }
        });
    });

    $(document).on('click', '.edit-task', function() {
        var taskId = $(this).data('id');
        $.ajax({
            url: "{{ route('tasks.show', '') }}" + "/" + taskId,
            method: "GET",
            success: function(response) {
                if (response.task) {
                    var task = response.task;
                    $('#editTaskForm input[name="title"]').val(task.title);
                    $('#editTaskForm textarea[name="description"]').val(task.description);
                    
                    $('#editTaskModal').modal('show');
                    
                    $('#editTaskForm').data('id', taskId);
                    
                }
                else {
                console.log("Tarea no encontrada");
                }
            },
            error: function(error) {
                Swal.fire({
                    title: '):',
                    text: 'Error al editar la tarea.',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
            }
        });
    });

    $(document).on('submit', '#editTaskForm', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var taskId = $(this).data('id');
        $.ajax({
            url: "{{ url('tasks') }}/" + taskId,
            type: "PUT",
            data: formData,
            success: function(response) {
                $('#editTaskModal').modal('hide');
                loadTasks();
                Swal.fire(
                'Ok',
                response.message,
                'success'
                )
            },
            error: function(error) {
                Swal.fire({
                    title: '):',
                    text: 'Error al editar la tarea.',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
            }
        });
    });

</script>
@endsection
