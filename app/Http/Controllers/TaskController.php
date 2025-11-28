<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Task;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->filled('assignee')) {
            $query->whereIn('assignee', explode(',', $request->assignee));
        }
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('due_date', [$request->start, $request->end]);
        }
        if ($request->filled('min') && $request->filled('max')) {
            $query->whereBetween('time_tracked', [$request->min, $request->max]);
        }
        if ($request->filled('status')) {
            $query->whereIn('status', explode(',', $request->status));
        }
        if ($request->filled('priority')) {
            $query->whereIn('priority', explode(',', $request->priority));
        }

        return response()->json($query->get());
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'numeric|min:0',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        // defaults
        $validated['time_tracked'] = $validated['time_tracked'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'pending';

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'data'    => $task
        ], 201);
    }

   
    public function export(Request $request)
    {
        $query = Task::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->filled('assignee')) {
            $query->whereIn('assignee', explode(',', $request->assignee));
        }
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('due_date', [$request->start, $request->end]);
        }
        if ($request->filled('min') && $request->filled('max')) {
            $query->whereBetween('time_tracked', [$request->min, $request->max]);
        }
        if ($request->filled('status')) {
            $query->whereIn('status', explode(',', $request->status));
        }
        if ($request->filled('priority')) {
            $query->whereIn('priority', explode(',', $request->priority));
        }

    $tasks = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->fromArray(['Title','Assignee','Due Date','Time Tracked','Status','Priority'], NULL, 'A1');

        // Task data
        $row = 2;
        $totalTime = 0;
        foreach($tasks as $task){
            $sheet->fromArray([
                $task->title,
                $task->assignee,
                $task->due_date,
                $task->time_tracked,
                $task->status,
                $task->priority
            ], NULL, 'A'.$row);
            $totalTime += $task->time_tracked;
            $row++;
        }

        // Summary row
        $sheet->setCellValue('A'.$row, 'Total Tasks');
        $sheet->setCellValue('B'.$row, $tasks->count());
        $sheet->setCellValue('D'.$row, $totalTime);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'tasks.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        $writer->save('php://output');
        exit;
    }
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 200);
    }


}
