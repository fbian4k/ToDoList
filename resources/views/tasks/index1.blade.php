@extends('layouts.app')

@section('content')
<div class="container app-container">
    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-md-8">
            <h1><i class="fas fa-tasks"></i> Lista de Tareas</h1>
        </div>
        <div class="col-md-4 text-md-right">
        <button type="button" class="btn btn-success mb-4" data-toggle="modal" data-target="#staticBackdrop"> Crear Nueva Tarea </button>
        </div>
    </div>

    <form id="filter-form">
    <div class="row">
        <br>
        <h4>Filtrar por:</h4>
        <div class="col-md-4">
            <label for="start_date">Fecha Creación:</label>
            <input type="date" class="form-control" id="start_date" name="start_date">
        </div>
        <div class="col-md-4">
            <label for="end_date">Fecha Completada:</label>
            <input type="date" class="form-control" id="end_date" name="end_date">
        </div>
        <div class="col-md-4">
            <label for="activity_type">Tipo de Actividad:</label>
            <select class="form-control" id="activity_type" name="activity_type">
                <option value="">Todos</option>
                <option value="Completada">Completada</option>
                <option value="Pendiente">Pendiente</option>
            </select>
        </div>
    </div>
    <br><br>
</form>

<table id="tasks-table" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Usuario Asignado</th>
            <th>Estado</th>
            <th>Fecha de creación</th>
            <th>Fecha de término</th>
            <th>Tiempo transcurrido</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
   
    </tbody>
</table>
</div>
<div class="modal fade" id="edit-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="edit-modal-label" aria-hidden="true">
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
                    @error('title')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                    @error('description')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
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
                    @error('category_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
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


<script src="{{ asset('js/tasks.js') }}"></script>
@endsection
