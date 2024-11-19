<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function listarTarefas(Request $request)
    {
        $query = Task::join('users', 'tasks.user_id', '=', 'users.id')
            ->select('tasks.*', 'users.name');

        // Filtro de status: se não houver status ou o valor for 'ALL', traz todos
        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('tasks.status', '=', $request->input('status'));
        }

        // Ordenação por data de criação ou atualização
        if ($request->has('orderedBy')) {
            $orderBy = $request->input('orderedBy');
            $sortedBy = $request->input('sortedBy', 'asc'); // Padrão é ascendente

            if (in_array($orderBy, ['created_at', 'updated_at']) && in_array($sortedBy, ['asc', 'desc'])) {
                $query->orderBy('tasks.' . $orderBy, $sortedBy);
            }
        }

        // Verifica a role do usuário
        if (Auth::user()->role !== 'admin') {
            $query->where('tasks.user_id', '=', Auth::user()->id);
        }

        $tasks = $query->get();
        return response()->json($tasks);
    }



    public function criarTarefa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,blocked,completed'
        ], [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.required' => 'A descrição é obrigatória.',
            'description.string' => 'A descrição deve ser uma string.',
            'status.required' => 'O status da tarefa é obrigatório.',
            'status.in' => 'O status deve ser um dos seguintes: pending, in_progress, blocked ou completed.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        // Corrigido: Usar auth()->user()->id, não role->id
        $task = Task::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
            'user_id' => Auth::user()->id,  // Corrigido
        ]);

        return response()->json($task, 201);
    }

    public function visualizarTarefa(Task $task)
    {
        return response()->json($task);
    }

    public function atualizarTarefa(Request $request, Task $task)
    {
        
        // Permissão para verificar o usuário ou admin
        if (Auth::user()->id !== $task->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Você não tem permissão para atualizar esta tarefa.'], 403);
        }

        // Validação dos campos da tarefa
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,blocked,completed',
        ], [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser uma string.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.required' => 'A descrição é obrigatória.',
            'description.string' => 'A descrição deve ser uma string.',
            'status.required' => 'O status da tarefa é obrigatório.',
            'status.in' => 'O status deve ser um dos seguintes: pending, in_progress, blocked ou completed.',
        ]);

        // Retorna erros de validação, se houver
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Atualiza a tarefa com os dados validados
        $task->update($validator->validated());

        // Retorna a tarefa atualizada
        return response()->json($task);
    }

    public function atualizarStatusTarefa(Request $request, Task $task)
    {
        // Validação do status da tarefa
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,blocked,completed',
        ], [
            'status.required' => 'O status da tarefa é obrigatório.',
            'status.in' => 'O status deve ser um dos seguintes: pending, in_progress, blocked ou completed.',
        ]);

        // Se houver falhas na validação, retorna um erro 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Atualiza o status da tarefa
        $task->update([
            'status' => $request->status,
        ]);

        // Retorna a tarefa com o novo status atualizado
        return response()->json($task);
    }

    public function deletarTarefa(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Tarefa deletada com sucesso!']);
    }
}
