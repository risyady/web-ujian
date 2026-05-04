<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUjianRequest;
use App\Http\Requests\UpdateUjianRequest;
use App\Models\Ujian;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Ujian::class);

        $user = auth()->user();

        $ujians = Ujian::with('guru')->when($user->isGuru(), function ($query) use ($user) {
                $query->where('guru_id', $user->id);
            })->latest()->paginate(10);

        return $this->successResponse($ujians);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUjianRequest $request)
    {
        $currentUser = auth()->user()->id;

        $data = $request->validated();
        $data['guru_id'] = $currentUser;
        
        $ujian = Ujian::create($data);

        $ujian->pengaturan()->create([]);
        
        return $this->createdResponse($ujian->load('guru'), 'Ujian berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ujian $ujian)
    {
        $this->authorize('view', $ujian);
        return $this->successResponse($ujian->load('guru'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUjianRequest $request, Ujian $ujian)
    {
        $data = $request->validated();

        $ujian->update($data);

        return $this->successResponse($ujian->load('guru'), 'Ujian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ujian $ujian)
    {
        $ujian->delete();

        return $this->deletedResponse('Ujian berhasil dihapus');
    }
}
