<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Entity;
use App\Models\Type;
use Carbon\Carbon;

class EntitySearcherComponent extends Component
{
    use WithPagination;

    public $entityType;
    public $isMultiple = false;
    public $selectedEntities = [];
    public $showModal = false;
    
    // Search and filters
    public $searchTerm = '';
    public $filters = [];
    
    // Available filter fields for this entity type
    public $availableFilters = [];

    protected $listeners = ['openEntitySearcher'];

    public function mount($entityType = null, $isMultiple = false, $selectedEntities = [])
    {
        $this->entityType = $entityType;
        $this->isMultiple = $isMultiple;
        $this->selectedEntities = is_array($selectedEntities) ? $selectedEntities : [];
        $this->loadAvailableFilters();
    }

    public function openEntitySearcher($entityType, $isMultiple = false, $selectedEntities = [])
    {
        $this->entityType = $entityType;
        $this->isMultiple = $isMultiple;
        $this->selectedEntities = is_array($selectedEntities) ? $selectedEntities : [];
        $this->showModal = true;
        $this->loadAvailableFilters();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->filters = [];
        $this->resetPage();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    private function loadAvailableFilters()
    {
        if (!$this->entityType) return;

        $type = Type::where('Slug', $this->entityType)->first();
        if (!$type) return;

        $this->availableFilters = $type->attributes()
            ->with('attributeType')
            ->get()
            ->filter(function ($attribute) {
                // Exclude string type filters - search bar handles string searching
                return $attribute->attributeType && $attribute->attributeType->Slug !== 'string';
            })
            ->map(function ($attribute) {
                return [
                    'id' => $attribute->ID,
                    'name' => $attribute->Name,
                    'type' => $attribute->attributeType->Slug ?? 'string'
                ];
            })->toArray();
    }

    public function selectEntity($entityId)
    {
        if ($this->isMultiple) {
            if (!in_array($entityId, $this->selectedEntities)) {
                $this->selectedEntities[] = $entityId;
            }
        } else {
            $this->selectedEntities = [$entityId];
        }
    }

    public function unselectEntity($entityId)
    {
        $this->selectedEntities = array_filter($this->selectedEntities, function($id) use ($entityId) {
            return $id !== $entityId;
        });
        $this->selectedEntities = array_values($this->selectedEntities);
    }

    public function confirmSelection()
    {
        $this->dispatch('entitiesSelected', $this->selectedEntities);
        $this->closeModal();
    }

    public function render()
    {
        $entities = collect();
        
        if ($this->entityType && $this->showModal) {
            $query = Entity::with([
                'type',
                'stringValues.attribute',
                'intValues.attribute',
                'doubleValues.attribute',
                'dateTimeValues.attribute',
                'booleanValues.attribute'
            ])
            ->whereHas('type', function($q) {
                $q->where('Slug', $this->entityType);
            });

            // Apply search term
            if ($this->searchTerm) {
                $query->where(function($q) {
                    $q->whereHas('stringValues', function($sq) {
                        $sq->where('Value', 'like', '%' . $this->searchTerm . '%');
                    });
                });
            }

            // Apply filters
            foreach ($this->filters as $filterId => $filterValue) {
                if (empty($filterValue)) continue;

                $filter = collect($this->availableFilters)->firstWhere('id', $filterId);
                if (!$filter) continue;

                switch ($filter['type']) {
                    case 'integer':
                        $query->whereHas('intValues', function($q) use ($filterId, $filterValue) {
                            $q->where('AttributeID', $filterId)
                              ->where('Value', $filterValue);
                        });
                        break;
                    
                    case 'double':
                        $query->whereHas('doubleValues', function($q) use ($filterId, $filterValue) {
                            $q->where('AttributeID', $filterId)
                              ->where('Value', $filterValue);
                        });
                        break;
                    
                    case 'boolean':
                        $query->whereHas('booleanValues', function($q) use ($filterId, $filterValue) {
                            $q->where('AttributeID', $filterId)
                              ->where('Value', $filterValue === 'true');
                        });
                        break;
                    
                    case 'datetime':
                        if (isset($filterValue['from']) && $filterValue['from']) {
                            $query->whereHas('dateTimeValues', function($q) use ($filterId, $filterValue) {
                                $q->where('AttributeID', $filterId)
                                  ->where('Value', '>=', Carbon::parse($filterValue['from']));
                            });
                        }
                        if (isset($filterValue['to']) && $filterValue['to']) {
                            $query->whereHas('dateTimeValues', function($q) use ($filterId, $filterValue) {
                                $q->where('AttributeID', $filterId)
                                  ->where('Value', '<=', Carbon::parse($filterValue['to']));
                            });
                        }
                        break;
                }
            }

            $entities = $query->paginate(10);
        }

        return view('livewire.entity-searcher-component', [
            'entities' => $entities
        ]);
    }
}