<?php

namespace App\Http\Controllers\User;

use App\Models\Field;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FieldsController extends Controller
{
    /**
     * Menampilkan daftar lapangan untuk user
     */
    public function index()
    {
        $fields = Field::all();

        return view('users.fields.index', compact('fields'));
    }
    /**
     * Menampilkan detail lapangan
     */
    public function show($id)
    {
        $field = Field::findOrFail($id);

        // Anda bisa menambahkan logika untuk menampilkan jadwal tersedia, review, dll.

        return view('users.fields.show', compact('field'));
    }

    /**
     * Get field availability for specific date
     */



}
