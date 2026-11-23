<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        $contacts = Contact::with(['category', 'tags'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $contacts,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'integer', Rule::in([1, 2, 3])],
            'email' => ['required', 'email', 'max:255'],
            'tel' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'detail' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $contact = Contact::create($validated);

        if (array_key_exists('tags', $validated)) {
            $contact->tags()->sync($validated['tags'] ?? []);
        }

        $contact->load(['category', 'tags']);

        return response()->json([
            'message' => 'Contact created successfully.',
            'data' => $contact,
        ], 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        $contact->load(['category', 'tags']);

        return response()->json([
            'data' => $contact,
        ], 200);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'gender' => ['sometimes', 'required', 'integer', Rule::in([1, 2, 3])],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'tel' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'detail' => ['sometimes', 'required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $contact->update($validated);

        if (array_key_exists('tags', $validated)) {
            $contact->tags()->sync($validated['tags'] ?? []);
        }

        $contact->load(['category', 'tags']);

        return response()->json([
            'message' => 'Contact updated successfully.',
            'data' => $contact,
        ], 200);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->tags()->detach();
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully.',
        ], 200);
    }
}
