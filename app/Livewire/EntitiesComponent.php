<?php

namespace App\Livewire;

use App\Models\Entity;
use App\Models\Type;
use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class EntitiesComponent extends Component
{
    use WithPagination;

    public $showEntityModal = false;
    public $showDeleteEntityModal = false;
    
    // Entity properties
    public $selectedEntity = null;
    public $selectedTypeId = '';
    public $entityToDelete = null;
    
    // Form step control
    public $currentStep = 1; // 1 = Type selection, 2 = Attribute form
    public $selectedType = null;
    
    // Dynamic attributes for form
    public $entityAttributes = [];
    
    // Search properties
    public $searchEntities = '';
    public $filterByType = '';
    
    // Entity searcher properties
    public $currentSearcherIndex = null;
    
    // Type filtering
    public $typeSlug = null;

    public function mount($typeSlug = null)
    {
        $this->entityAttributes = [];
        $this->typeSlug = $typeSlug;
        
        // If type slug is provided, set the filter
        if ($typeSlug) {
            $type = Type::where('Slug', $typeSlug)->first();
            if ($type) {
                $this->filterByType = $type->ID;
            }
        }
    }

    public function updatingSearchEntities()
    {
        $this->resetPage();
    }

    public function updatingFilterByType()
    {
        $this->resetPage();
    }

    public function selectEntity($entityId)
    {
        $this->selectedEntity = Entity::with([
            'type.attributes.attributeType',
            'stringValues.attribute',
            'intValues.attribute', 
            'doubleValues.attribute',
            'dateTimeValues.attribute',
            'booleanValues.attribute',
            'relationValues.attribute',
            'relationValues.relatedEntity.type'
        ])->find($entityId);
    }

    public function createEntity()
    {
        $this->resetEntityForm();
        $this->currentStep = 1; // Start with type selection
        $this->showEntityModal = true;
    }

    public function editEntity($entityId)
    {
        $this->selectedEntity = Entity::with([
            'type.attributes.attributeType',
            'stringValues.attribute',
            'intValues.attribute', 
            'doubleValues.attribute',
            'dateTimeValues.attribute',
            'booleanValues.attribute',
            'relationValues.attribute',
            'relationValues.relatedEntity'
        ])->findOrFail($entityId);
        $this->selectedTypeId = $this->selectedEntity->TypeID;
        $this->selectedType = $this->selectedEntity->type;
        
        // For editing, skip to attribute form since type is already known
        $this->currentStep = 2;
        
        // Load current attribute values
        $this->loadEntityAttributeValues();
        
        $this->showEntityModal = true;
    }

    public function selectTypeForEntity($typeId)
    {
        $this->selectedTypeId = $typeId;
        $this->selectedType = Type::with(['attributes.attributeType'])->find($typeId);
        
        if ($this->selectedType) {
            $this->loadTypeAttributes();
            $this->currentStep = 2; // Move to attribute form
        }
    }

    public function goBackToTypeSelection()
    {
        $this->currentStep = 1;
        $this->selectedTypeId = '';
        $this->selectedType = null;
        $this->entityAttributes = [];
    }

    public function updatedSelectedTypeId()
    {
        if ($this->selectedTypeId) {
            $this->loadTypeAttributes();
        } else {
            $this->entityAttributes = [];
        }
    }

    private function loadTypeAttributes()
    {
        $type = Type::find($this->selectedTypeId);
        if ($type) {
            // Use getAllInheritedAttributes to include inherited attributes from parent types
            $allAttributes = $type->getAllInheritedAttributes();
            
            $this->entityAttributes = $allAttributes
                ->filter(function($attribute) {
                    // Filter out any attributes that might conflict with standard fields
                    $standardFields = ['ID', 'TypeID', 'created_at', 'updated_at'];
                    return !in_array($attribute->Name, $standardFields);
                })
                ->map(function($attribute) {
                    return [
                        'attribute_id' => $attribute->ID,
                        'name' => $attribute->Name,
                        'slug' => $attribute->Slug,
                        'type' => $attribute->attributeType->Slug,
                        'type_name' => $attribute->attributeType->Name,
                        'is_primitive' => $attribute->attributeType->IsPrimitive,
                        'is_composition' => $attribute->IsComposition,
                        'is_array' => $attribute->IsArray,
                        'value' => null
                    ];
                })->values()->toArray();
        }
    }

    private function loadEntityAttributeValues()
    {
        if (!$this->selectedEntity) return;
        
        $type = Type::find($this->selectedTypeId);
        if ($type) {
            // Use getAllInheritedAttributes to include inherited attributes from parent types
            $allAttributes = $type->getAllInheritedAttributes();
            
            $this->entityAttributes = $allAttributes
                ->filter(function($attribute) {
                    // Filter out any attributes that might conflict with standard fields
                    $standardFields = ['ID', 'TypeID', 'created_at', 'updated_at'];
                    return !in_array($attribute->Name, $standardFields);
                })
                ->map(function($attribute) {
                    $value = $this->selectedEntity->getDynamicAttributeValue($attribute->Slug);
                    
                    return [
                        'attribute_id' => $attribute->ID,
                        'name' => $attribute->Name,
                        'slug' => $attribute->Slug,
                        'type' => $attribute->attributeType->Slug,
                        'type_name' => $attribute->attributeType->Name,
                        'is_primitive' => $attribute->attributeType->IsPrimitive,
                        'is_composition' => $attribute->IsComposition,
                        'is_array' => $attribute->IsArray,
                        'value' => $this->formatValueForForm($value, $attribute->attributeType)
                    ];
                })->values()->toArray();
        }
    }

    private function formatValueForForm($value, $type)
    {
        if ($value === null) return '';
        
        // Handle non-primitive types (relations)
        if (!$type->IsPrimitive) {
            // Handle array of entities
            if (is_array($value)) {
                return array_map(function($entity) {
                    return $entity instanceof Entity ? $entity->ID : $entity;
                }, $value);
            }
            // Handle single entity
            if ($value instanceof Entity) {
                return $value->ID;
            }
        }
        
        if ($type->Slug === 'datetime' && $value instanceof \DateTime) {
            return $value->format('Y-m-d\TH:i');
        }
        
        return $value;
    }

    public function saveEntity()
    {
        $this->validate([
            'selectedTypeId' => ['required', 'exists:Types,ID'],
        ], [
            'selectedTypeId.required' => 'El tipo de entidad es obligatorio.',
        ]);

        if ($this->selectedEntity && $this->selectedEntity->ID) {
            // Update existing entity
            $entity = $this->selectedEntity;
        } else {
            // Create new entity
            $entity = Entity::create([
                'TypeID' => $this->selectedTypeId,
            ]);
            // Load the type relationship immediately after creation
            $entity->load('type.attributes.attributeType');
        }

        // Save attribute values
        $this->saveEntityAttributeValues($entity);
        
        if ($this->selectedEntity && $this->selectedEntity->ID) {
            session()->flash('success', 'Entidad actualizada exitosamente.');
        } else {
            session()->flash('success', 'Entidad creada exitosamente.');
        }

        $this->showEntityModal = false;
        $this->resetEntityForm();
    }

    private function saveEntityAttributeValues($entity)
    {
        foreach ($this->entityAttributes as $attrData) {
            $value = $attrData['value'];
            
            // Skip empty values for optional attributes
            if ($value === '' || $value === null) {
                continue;
            }
            
            // Skip if this looks like a standard model attribute
            $attributeName = $attrData['name'] ?? '';
            $standardFields = ['ID', 'TypeID', 'created_at', 'updated_at'];
            if (in_array($attributeName, $standardFields)) {
                continue;
            }
            
            // Convert form values to appropriate types
            $processedValue = $this->processValueForStorage($value, $attrData);
            
            try {
                // Use the attribute name converted to slug format
                $slug = \Illuminate\Support\Str::slug($attrData['name']);
                $entity->setDynamicAttributeValue($slug, $processedValue);
            } catch (\Exception $e) {
                // If slug fails, try with the original slug from attrData
                try {
                    $entity->setDynamicAttributeValue($attrData['slug'], $processedValue);
                } catch (\Exception $e2) {
                    // Log error but don't fail the entire save operation
                    \Log::error("Failed to save attribute '{$attrData['name']}' for entity {$entity->ID}: " . $e2->getMessage());
                }
            }
        }
    }

    private function processValueForStorage($value, $attrData)
    {
        switch ($attrData['type']) {
            case 'int':
                return (int) $value;
            case 'double':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            case 'datetime':
                return new \DateTime($value);
            case 'string':
                return (string) $value;
            default:
                // For non-primitive types (relations)
                if (!$attrData['is_primitive']) {
                    return $value; // Should be an Entity ID
                }
                return $value;
        }
    }

    public function confirmDeleteEntity($entityId)
    {
        $this->entityToDelete = Entity::find($entityId);
        $this->showDeleteEntityModal = true;
    }

    public function deleteEntity()
    {
        if ($this->entityToDelete) {
            $this->entityToDelete->delete();
            session()->flash('success', 'Entidad eliminada exitosamente.');
        }

        $this->showDeleteEntityModal = false;
        $this->entityToDelete = null;
        $this->selectedEntity = null;
    }

    public function cancelDeleteEntity()
    {
        $this->showDeleteEntityModal = false;
        $this->entityToDelete = null;
    }

    public function resetEntityForm()
    {
        $this->selectedEntity = null;
        $this->selectedTypeId = '';
        $this->selectedType = null;
        $this->currentStep = 1;
        $this->entityAttributes = [];
    }

    public function closeEntityModal()
    {
        $this->showEntityModal = false;
        $this->resetEntityForm();
    }

    // Entity searcher methods
    public function openEntitySearcher($attributeIndex, $entityType, $isMultiple)
    {
        $this->currentSearcherIndex = $attributeIndex;
        
        // Get current selected entities for this attribute
        $currentValue = $this->entityAttributes[$attributeIndex]['value'] ?? '';
        $selectedEntities = $isMultiple === 'true' || $isMultiple === true
            ? (is_array($currentValue) ? $currentValue : ($currentValue ? [$currentValue] : []))
            : ($currentValue ? [$currentValue] : []);
        
        $this->dispatch('openEntitySearcher', $entityType, $isMultiple === 'true' || $isMultiple === true, $selectedEntities);
    }

    public function removeSelectedEntity($attributeIndex, $entityId)
    {
        $currentValue = $this->entityAttributes[$attributeIndex]['value'] ?? '';
        
        if (is_array($currentValue)) {
            // Remove from array
            $this->entityAttributes[$attributeIndex]['value'] = array_values(
                array_filter($currentValue, function($id) use ($entityId) {
                    return $id !== $entityId;
                })
            );
        } else {
            // Clear single value
            $this->entityAttributes[$attributeIndex]['value'] = '';
        }
    }

    protected $listeners = ['entitiesSelected'];

    public function entitiesSelected($selectedEntities)
    {
        if ($this->currentSearcherIndex !== null) {
            $attribute = $this->entityAttributes[$this->currentSearcherIndex] ?? null;
            
            if ($attribute) {
                if ($attribute['is_array']) {
                    // For array fields, replace all selected entities
                    $this->entityAttributes[$this->currentSearcherIndex]['value'] = $selectedEntities;
                } else {
                    // For single fields, use only the first selected entity
                    $this->entityAttributes[$this->currentSearcherIndex]['value'] = 
                        count($selectedEntities) > 0 ? $selectedEntities[0] : '';
                }
            }
        }
        
        $this->currentSearcherIndex = null;
    }

    public function render()
    {
        $entities = Entity::query()
            ->with([
                'type',
                'stringValues.attribute',
                'intValues.attribute',
                'doubleValues.attribute',
                'dateTimeValues.attribute',
                'booleanValues.attribute'
            ])
            ->when($this->searchEntities, function ($query) {
                $query->whereHas('type', function ($q) {
                    $q->where('Name', 'like', '%' . $this->searchEntities . '%');
                });
            })
            ->when($this->filterByType, function ($query) {
                $query->where('TypeID', $this->filterByType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $availableTypes = Type::withCount('attributes')->orderBy('Name')->get();
        
        // For relation attributes, get available entities
        $availableEntities = collect();
        if (!empty($this->entityAttributes)) {
            $relationAttributes = collect($this->entityAttributes)->where('is_primitive', false);
            if ($relationAttributes->isNotEmpty()) {
                $relationTypeIds = $relationAttributes->pluck('type')->unique();
                $availableEntities = Entity::with([
                        'type',
                        'stringValues.attribute',
                        'intValues.attribute',
                        'doubleValues.attribute',
                        'dateTimeValues.attribute',
                        'booleanValues.attribute'
                    ])
                    ->whereHas('type', function($q) use ($relationTypeIds) {
                        $q->whereIn('Slug', $relationTypeIds);
                    })
                    ->get()
                    ->groupBy('type.Slug');
            }
        }

        return view('livewire.entities-component', [
            'entities' => $entities,
            'availableTypes' => $availableTypes,
            'availableEntities' => $availableEntities
        ])->layout('layouts.app');
    }
}