<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TaskController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $completedTasks = $user->tasks()->where('completed', 1)->get();
        $pendingTasks = $user->tasks()->where('completed', 0)->get();
    
        return view('tasks.index', compact('completedTasks', 'pendingTasks'));
    }
    public function getTasks()
    {
        $user = Auth::user();
        $completedTasks = $user->tasks()->where('completed', 1)->get();
        $pendingTasks = $user->tasks()->where('completed', 0)->get();
    
        return response()->json([
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
        ]);
    }
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json(['task' => $task]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $user = auth()->user();

        $user->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => 0
        ]);

        return response()->json(['message' => 'Tarea creada exitosamente']);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Tarea actualizada exitosamente']);
    }

    public function complete($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['completed' => 1]);

        return response()->json(['message' => 'Tarea completada exitosamente']);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Tarea eliminada exitosamente']);
    }
}
