<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }


    public function getTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks);
      
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tasks'
        ]);

        $task = new Task();
        $task->name = $request->name;
        $task->save();

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $task->status = $request->completed ? 1 : 0; 
        $task->save();
    
        return response()->json($task);
    }
    
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
