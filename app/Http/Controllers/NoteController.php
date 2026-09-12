<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $notes = Note::forCurrentBranch()->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);

        $summary = [
            'total'   => Note::forCurrentBranch()->count(),
            'active'  => Note::forCurrentBranch()->where('status', 1)->count(),
            'inactive'=> Note::forCurrentBranch()->where('status', 0)->count(),
        ];

        return view('note.create', compact('notes', 'summary'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'   => 'required|date',
            'note'   => 'required|string', // Summernote provides HTML string
            'status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $note = new Note();
            $note->date   = $request->date;
            $note->note   = $request->note;
            $note->status = $request->status;
            $note->branch_id  = auth()->user()->branch_id;
            $note->created_by = auth()->user()->name;
            $note->save();

            return $this->successResponse('Note Created Successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Server Error!!');
        }
    }

    public function edit($id)
    {
        $info = Note::forCurrentBranch()->where('id', $id)->first();
        return response()->json($info);
    }

    public function update(Request $request, Note $note)
    {
        $validator = Validator::make($request->all(), [
            'date'   => 'required|date',
            'note'   => 'required|string',
            'status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        if ($note->branch_id !== auth()->user()->branch_id) {
            return $this->errorResponse('Unauthorized Action!');
        }

        $note->date   = $request->date;
        $note->note   = $request->note;
        $note->status = $request->status;
        $note->updated_by = auth()->user()->name;

        if ($note->save()) {
            return $this->successResponse('Note Updated Successfully.');
        }

        return $this->errorResponse('Server Error!!');
    }

    public function destroy(Note $note)
    {
        if ($note->branch_id !== auth()->user()->branch_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Action']);
        }

        if ($note->delete()) {
            return response()->json(['success' => true, 'message' => 'Note Deleted Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Delete Failed']);
    }

    /* ------------------- Helpers ------------------- */

    private function alert(string $type, string $msg): string
    {
        return "<div class='alert alert-{$type}'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <b>{$msg}</b>
                </div>";
    }

    private function successResponse(string $msg)
    {
        return response()->json(['status' => 300, 'message' => $this->alert('success', $msg)]);
    }

    private function errorResponse(string $msg)
    {
        return response()->json(['status' => 303, 'message' => $this->alert('danger', $msg)]);
    }

    private function validationErrorResponse($validator)
    {
        $errors = collect($validator->errors()->all())
            ->map(fn ($e) => "<b>{$e}</b>")
            ->implode('<br>');

        return response()->json([
            'status'  => 303,
            'message' => $this->alert('warning', $errors),
        ]);
    }
}