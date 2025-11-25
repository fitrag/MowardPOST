<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;

class ActivityLogManager extends Component
{
    public $logs;
    public $search = '';
    public $filterAction = '';
    public $perPage = 50;
    public $hasMore = false;
    public $showModal = false;
    public $selectedLog = null;
    public $isLive = false;

    public function mount()
    {
        $this->loadLogs();
    }

    public function updatedSearch()
    {
        $this->perPage = 50;
        $this->loadLogs();
    }

    public function updatedFilterAction()
    {
        $this->perPage = 50;
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Apply action filter
        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }

        // Get total count
        $totalCount = $query->count();

        // Apply pagination
        $this->logs = $query->take($this->perPage)->get();

        // Check if there are more items
        $this->hasMore = $totalCount > $this->perPage;
    }

    public function loadMore()
    {
        $this->perPage += 50;
        $this->loadLogs();
    }

    public function exportPdf()
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Apply action filter
        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }

        $logs = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.activity-logs', ['logs' => $logs]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'activity-logs-' . now()->format('Y-m-d-H-i') . '.pdf');
    }

    public function showLogDetail($logId)
    {
        $this->selectedLog = ActivityLog::with('user')->find($logId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedLog = null;
    }

    public function toggleLive()
    {
        $this->isLive = !$this->isLive;
    }

    public function render()
    {
        return view('livewire.admin.activity-log-manager')->layout('layouts.app');
    }
}
