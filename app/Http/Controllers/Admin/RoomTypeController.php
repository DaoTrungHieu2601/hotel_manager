<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(): View
    {
        $types = RoomType::query()->orderBy('name')->paginate(15);

        return view('admin.room-types.index', compact('types'));
    }

    public function create(): View
    {
        return view('admin.room-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $data = $this->stripImagePayload($validated);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('room-types', 'public');
        }

        RoomType::query()->create($data);

        return redirect()->route('admin.room-types.index')->with('status', __('Đã tạo loại phòng.'));
    }

    public function edit(RoomType $room_type): View
    {
        return view('admin.room-types.edit', ['type' => $room_type]);
    }

    public function update(Request $request, RoomType $room_type): RedirectResponse
    {
        $validated = $this->validated($request);
        $data = $this->stripImagePayload($validated);

        if ($request->boolean('remove_image')) {
            if ($room_type->image_path) {
                Storage::disk('public')->delete($room_type->image_path);
            }
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($room_type->image_path) {
                Storage::disk('public')->delete($room_type->image_path);
            }
            $data['image_path'] = $request->file('image')->store('room-types', 'public');
        }

        $room_type->update($data);

        return redirect()->route('admin.room-types.index')->with('status', __('Đã cập nhật.'));
    }

    public function destroy(RoomType $room_type): RedirectResponse
    {
        if ($room_type->image_path) {
            Storage::disk('public')->delete($room_type->image_path);
        }
        $room_type->delete();

        return redirect()->route('admin.room-types.index')->with('status', __('Đã xóa.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'facilities' => ['nullable', 'string', 'max:4000'],
            'amenities' => ['nullable', 'string', 'max:4000'],
            'default_price' => ['required', 'numeric', 'min:0'],
            'beds' => ['required', 'integer', 'min:1'],
            'max_occupancy' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:20480'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function stripImagePayload(array $validated): array
    {
        unset($validated['image'], $validated['remove_image']);

        return $validated;
    }
}
