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
            
            if ($request->filled('start_date')) {
                $tasks->where('created_at', '>=', $request->input('start_date'));
            }
            if ($request->filled('end_date')) {
                $tasks->where('completed_at', '<=', $request->input('end_date'));
            }
            if ($request->filled('activity_type')) {
                $activityType = $request->input('activity_type');
                if ($activityType === 'Completada') {
                    $tasks->where('completed', 1);
                } elseif ($activityType === 'Pendiente') {
                    $tasks->where('completed', 0);
                }
            }
            $tasks = $tasks->get();
            $tasks->transform(function ($task) {
                if ($task->created_at && $task->completed_at) {
                    $elapsedSeconds = $task->created_at->diffInSeconds($task->completed_at);
                    $task->elapsed_time = $this->formatElapsedTime($elapsedSeconds);
                } else {
                    $task->elapsed_time = 'N/A';
                }
            
                $task->created_at = $task->created_at ? $task->created_at : 'N/A';
                $task->completed_at = $task->completed_at ? $task->completed_at : 'N/A';
                return $task;
            });
            
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
                
                ->rawColumns(['actions'])
                ->make(true);

        }
    
        $categories = Category::all();
    
        return view('tasks.index1', compact('categories'));
    }
    private function formatElapsedTime($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    $formattedTime = '';
    if ($hours > 0) {
        $formattedTime .= $hours . ' horas ';
    }
    if ($minutes > 0) {
        $formattedTime .= $minutes . ' minutos ';
    }
    if ($seconds > 0) {
        $formattedTime .= $seconds . ' segundos';
    }

    return trim($formattedTime);
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
        if ($task->completed == 0) {
            $task->completed = 1;
            $task->completed_at = now();
        } else {
            $task->completed = 0;
            $task->completed_at = null; 
        }
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
