<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FillInTheBlankRequest;
use App\Interfaces\Services\Admin\FillInTheBlankServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FillInTheBlankController extends Controller
{
    protected $fillInTheBlankService;

    public function __construct(FillInTheBlankServiceInterface $fillInTheBlankService)
    {
        $this->fillInTheBlankService = $fillInTheBlankService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $fillInTheBlanks = $this->fillInTheBlankService->all();
            return view('admin.fill-in-the-blank.index', compact('fillInTheBlanks'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Bir hata oluştu!');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.fill-in-the-blank.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FillInTheBlankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FillInTheBlankRequest $request)
    {
        try {
            $this->fillInTheBlankService->create($request->validated());
            return redirect()->route('admin.fill-in-the-blank.index')->with('success', 'Boşluk doldurma sorusu başarıyla oluşturuldu!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Boşluk doldurma sorusu oluşturulurken bir hata oluştu!')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $fillInTheBlank = $this->fillInTheBlankService->find($id);
            if (!$fillInTheBlank) {
                return redirect()->back()->with('error', 'Boşluk doldurma sorusu bulunamadı!');
            }
            return view('admin.fill-in-the-blank.show', compact('fillInTheBlank'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Bir hata oluştu!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $fillInTheBlank = $this->fillInTheBlankService->find($id);
            if (!$fillInTheBlank) {
                return redirect()->back()->with('error', 'Boşluk doldurma sorusu bulunamadı!');
            }
            return view('admin.fill-in-the-blank.edit', compact('fillInTheBlank'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Bir hata oluştu!');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FillInTheBlankRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FillInTheBlankRequest $request, $id)
    {
        try {
            $updated = $this->fillInTheBlankService->update($id, $request->validated());
            if (!$updated) {
                return redirect()->back()->with('error', 'Boşluk doldurma sorusu bulunamadı!');
            }
            return redirect()->route('admin.fill-in-the-blank.index')->with('success', 'Boşluk doldurma sorusu başarıyla güncellendi!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Boşluk doldurma sorusu güncellenirken bir hata oluştu!')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->fillInTheBlankService->delete($id);
            if (!$deleted) {
                return redirect()->back()->with('error', 'Boşluk doldurma sorusu bulunamadı!');
            }
            return redirect()->route('admin.fill-in-the-blank.index')->with('success', 'Boşluk doldurma sorusu başarıyla silindi!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Boşluk doldurma sorusu silinirken bir hata oluştu!');
        }
    }

    /**
     * Toggle the status of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        try {
            $status = $this->fillInTheBlankService->toggleStatus($id);
            if ($status === false) {
                return response()->json(['error' => 'Boşluk doldurma sorusu bulunamadı!'], 404);
            }
            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Bir hata oluştu!'], 500);
        }
    }
} 