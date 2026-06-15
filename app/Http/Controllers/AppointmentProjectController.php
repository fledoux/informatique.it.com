<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentProjectStoreRequest;
use App\Http\Requests\AppointmentProjectUpdateRequest;
use App\Models\AppointmentBooking;
use App\Models\AppointmentProject;
use App\Models\AppointmentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $projects = AppointmentProject::query()->latest('id')->paginate(20);
        return view('appointment-project.index', compact('projects'));
    }

    public function create()
    {
        $project = new AppointmentProject([
            'serial' => Str::uuid()->toString(),
            'slot_duration_minutes' => 30,
            'day_start_time' => '08:00',
            'day_end_time' => '17:00',
            'single_registration_per_person' => true,
            'is_active' => true,
        ]);

        return view('appointment-project.create', compact('project'));
    }

    public function store(AppointmentProjectStoreRequest $request)
    {
        $data = $request->validated();
        $data['single_registration_per_person'] = $request->boolean('single_registration_per_person');
        $data['is_active'] = $request->boolean('is_active');

        AppointmentProject::create($data);

        return redirect()->route('appointment-project.index')->with('success', __('global.messages.created'));
    }

    public function show(int $id)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            $manualSlots = $project->manualSlots()
                ->orderBy('slot_date')
                ->orderBy('slot_time')
                ->get();

            return view('appointment-project.show', compact('project', 'manualSlots'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit(int $id)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            return view('appointment-project.edit', compact('project'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(AppointmentProjectUpdateRequest $request, int $id)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            $data = $request->validated();
            $data['single_registration_per_person'] = $request->boolean('single_registration_per_person');
            $data['is_active'] = $request->boolean('is_active');

            $project->update($data);

            return redirect()->route('appointment-project.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy(int $id)
    {
        try {
            AppointmentProject::query()->findOrFail($id);
            AppointmentProject::query()->whereKey($id)->delete();

            return redirect()->route('appointment-project.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }

    public function storeSlot(Request $request, int $id)
    {
        $project = AppointmentProject::query()->findOrFail($id);

        $validated = $request->validate([
            'slot_date' => ['required', 'date_format:Y-m-d'],
            'slot_time' => ['required', 'date_format:H:i'],
            'break_text' => ['nullable', 'string', 'max:50'],
        ]);

        AppointmentSlot::updateOrCreate([
            'appointment_project_id' => $project->id,
            'slot_date' => $validated['slot_date'],
            'slot_time' => $validated['slot_time'],
        ], [
            'break_text' => $validated['break_text'] ?? null,
        ]);

        return redirect()->route('appointment-project.show', $project->id)
            ->with('success', 'Créneau ajouté.');
    }

    public function generateSlots(Request $request, int $id)
    {
        $project = AppointmentProject::query()->findOrFail($id);

        $validated = $request->validate([
            'generate_date' => ['required', 'date_format:Y-m-d'],
            'break_time_minutes' => ['required', 'integer', 'min:0', 'max:60'],
        ]);

        $times = $project->generateTimesForDay($validated['break_time_minutes']);
        $created = 0;

        foreach ($times as $time) {
            $slot = AppointmentSlot::firstOrCreate([
                'appointment_project_id' => $project->id,
                'slot_date' => $validated['generate_date'],
                'slot_time' => $time,
            ]);

            if ($slot->wasRecentlyCreated) {
                $created++;
            }
        }

        return redirect()->route('appointment-project.show', $project->id)
            ->with('success', $created . ' créneau(x) généré(s) avec ' . $validated['break_time_minutes'] . ' min de pause. Supprimez ceux qui ne vous conviennent pas.');
    }

    public function bookings(int $id)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            $bookings = AppointmentBooking::query()
                ->where('appointment_project_id', $project->id)
                ->orderBy('starts_at', 'asc')
                ->get();

            return view('appointment-project.bookings', compact('project', 'bookings'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function destroySlot(int $id, int $slot)
    {
        $project = AppointmentProject::query()->findOrFail($id);

        AppointmentSlot::query()
            ->where('appointment_project_id', $project->id)
            ->whereKey($slot)
            ->delete();

        return redirect()->route('appointment-project.show', $project->id)
            ->with('success', 'Créneau supprimé.');
    }

    public function destroyDay(int $id, string $day)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            
            $deleted = AppointmentSlot::query()
                ->where('appointment_project_id', $project->id)
                ->whereDate('slot_date', $day)
                ->delete();

            return redirect()->route('appointment-project.show', $project->id)
                ->with('success', $deleted . ' créneau(x) supprimé(s) pour cette journée.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.show', $id)
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function destroyBooking(int $id, int $booking)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            AppointmentBooking::query()
                ->where('appointment_project_id', $project->id)
                ->whereKey($booking)
                ->delete();

            return redirect()->route('appointment-project.bookings', $project->id)
                ->with('success', 'Réservation supprimée. Le créneau est à nouveau disponible.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.bookings', $id)
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function toggleShowBookingNames(int $id)
    {
        try {
            $project = AppointmentProject::query()->findOrFail($id);
            $project->update(['show_booking_names' => !$project->show_booking_names]);

            return redirect()->route('appointment-project.index')
                ->with('success', $project->show_booking_names ? 'Noms des réservations affichés.' : 'Noms des réservations masqués.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('appointment-project.index')
                ->with('error', __('global.messages.not_found'));
        }
    }
}
