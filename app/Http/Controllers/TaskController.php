<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
class TaskController extends Controller
{
    
    // public function index()
    // {
    //     $user = Auth::user();
    //     $completedTasks = $user->tasks()->where('completed', 1)->get();
    //     $pendingTasks = $user->tasks()->where('completed', 0)->get();
    
    //     return view('tasks.index', compact('completedTasks', 'pendingTasks'));
    // }
    public function index1(Request $request){
        if ($request->ajax()) {

            $tasks = Task::with(['category', 'assignedUser'])
            ->orderBy('created_at', 'desc');
            // $request->input('search.value')

            

            return DataTables::of($tasks)
                ->addColumn('completed', function ($task) {
                    return $task->completed ? 'Completada' : 'Pendiente';
                })
                ->addColumn('actions', function ($task) {

                    return '<input type="checkbox" class="toggle-status-checkbox custom-checkbox" data-task-id="' . $task->id . '" ' . ($task->completed ? 'checked' : '') . '>
                            <button class="btn btn-primary btn-sm" style="margin-right: 10px;" onclick="editTask(' . $task->id . ')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteTask(' . $task->id . ')">
                                <i class="fas fa-trash-alt"></i>
                            </button>';

                })
                // <button class="btn btn-primary btn-sm" onclick="editTask(' . $task->id . ')">Editar</button>
                ->rawColumns(['actions'])
                ->make(true);

        }
    
        $categories = Category::all();
    
        return view('tasks.index1', compact('categories'));
    }
    // public function getTasks()
    // {
    //     $user = Auth::user();
    //     $completedTasks = $user->tasks()->where('completed', 1)->get();
    //     $pendingTasks = $user->tasks()->where('completed', 0)->get();
    
    //     return response()->json([
    //         'completedTasks' => $completedTasks,
    //         'pendingTasks' => $pendingTasks,
    //     ]);
    // }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json(['task' => $task]);
    }
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'description' => 'required|string|max:600'
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Tarea actualizada exitosamente']);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:600',
            'category_id' => 'required|integer', 
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no debe ser mayor de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no debe ser mayor de 600 caracteres.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.integer' => 'La categoría debe ser un número entero.'
        ]);

        
        $task = new Task([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
        ]);
        // if($validatedData['assigned_to'])
        $task->user_id = auth()->user()->id;

    
        $task->save();
        return response()->json(['message' => 'Tarea creada exitosamente']);
    }

    

    // public function complete($id)
    // {
    //     $task = Task::findOrFail($id);
    //     $task->update(['completed' => 1]);

    //     return response()->json(['message' => 'Tarea completada exitosamente']);
    // }

    public function toggleStatus($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Tarea eliminada exitosamente']);
    }
}
