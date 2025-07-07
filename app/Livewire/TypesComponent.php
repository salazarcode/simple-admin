<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class TypesComponent extends Component
{
    use WithPagination;

    public $showTypeModal = false;
    public $showDeleteTypeModal = false;
    
    // Type properties
    public $selectedType = null;
    public $typeName = '';
    public $typeSlug = '';
    public $typeIsPrimitive = false;
    public $typeIsAbstract = false;
    public $typeToDelete = null;
    
    // Dynamic attributes
    public $typeAttributes = [];
    public $newAttribute = [
        'name' => '',
        'attribute_type_id' => '',
        'is_composition' => true,
        'is_array' => false
    ];
    public $editingAttributeIndex = null;
    
    // Search properties
    public $searchTypes = '';
    public $searchAttributeTypes = '';
    
    // Inheritance properties
    public $selectedParentTypes = [];
    public $searchParentTypes = '';
    public $showInheritedAttributes = false;

    public function mount()
    {
        $this->typeAttributes = [];
        $this->selectedParentTypes = [];
    }

    public function updatingSearchTypes()
    {
        $this->resetPage();
    }

    public function updatingTypeName()
    {
        $this->typeSlug = Str::slug($this->typeName);
    }

    public function selectType($typeId)
    {
        $this->selectedType = Type::with(['attributes.attributeType', 'parents', 'children'])->find($typeId);
        $this->showInheritedAttributes = false;
    }

    public function createType()
    {
        $this->resetTypeForm();
        $this->showTypeModal = true;
    }

    public function editType($typeId)
    {
        $this->selectedType = Type::with(['attributes.attributeType', 'parents'])->findOrFail($typeId);
        $this->typeName = $this->selectedType->Name;
        $this->typeSlug = $this->selectedType->Slug;
        $this->typeIsPrimitive = $this->selectedType->IsPrimitive;
        $this->typeIsAbstract = $this->selectedType->IsAbstract;
        
        // Load existing attributes
        $this->typeAttributes = $this->selectedType->attributes->map(function($attr) {
            return [
                'id' => $attr->ID,
                'name' => $attr->Name,
                'attribute_type_id' => $attr->AttributeTypeID,
                'attribute_type_name' => $attr->attributeType->Name,
                'is_composition' => $attr->IsComposition,
                'is_array' => $attr->IsArray
            ];
        })->toArray();
        
        // Load existing parent types
        $this->selectedParentTypes = $this->selectedType->parents->pluck('ID')->toArray();
        
        $this->showTypeModal = true;
    }

    public function saveType()
    {
        $this->validate([
            'typeName' => ['required', 'string', 'max:255'],
            'typeSlug' => ['required', 'string', 'max:255'],
        ], [
            'typeName.required' => 'El nombre del tipo es obligatorio.',
            'typeSlug.required' => 'El slug es obligatorio.',
        ]);

        // Validate inheritance cycles
        if (!$this->validateInheritanceCycles()) {
            session()->flash('error', 'La herencia seleccionada crearía un ciclo infinito.');
            return;
        }

        if ($this->selectedType) {
            // Update existing type
            $this->selectedType->update([
                'Name' => $this->typeName,
                'Slug' => $this->typeSlug,
                'IsPrimitive' => $this->typeIsPrimitive,
                'IsAbstract' => $this->typeIsAbstract,
            ]);

            // Update attributes
            $this->updateTypeAttributes();
            
            // Update inheritance relationships
            $this->updateInheritanceRelationships();
            
            session()->flash('success', 'Tipo actualizado exitosamente.');
        } else {
            // Create new type
            $type = Type::create([
                'Name' => $this->typeName,
                'Slug' => $this->typeSlug,
                'IsPrimitive' => $this->typeIsPrimitive,
                'IsAbstract' => $this->typeIsAbstract,
            ]);

            // Create attributes
            $this->createTypeAttributes($type->ID);
            
            // Create inheritance relationships
            $this->createInheritanceRelationships($type->ID);
            
            session()->flash('success', 'Tipo creado exitosamente.');
        }

        $this->showTypeModal = false;
        $this->resetTypeForm();
    }

    private function updateTypeAttributes()
    {
        // Delete existing attributes that are not in the current list
        $currentAttributeIds = collect($this->typeAttributes)->pluck('id')->filter();
        $this->selectedType->attributes()->whereNotIn('ID', $currentAttributeIds)->delete();

        // Update or create attributes
        foreach ($this->typeAttributes as $attr) {
            if (isset($attr['id'])) {
                // Update existing
                Attribute::where('ID', $attr['id'])->update([
                    'Name' => $attr['name'],
                    'AttributeTypeID' => $attr['attribute_type_id'],
                    'IsComposition' => $attr['is_composition'],
                    'IsArray' => $attr['is_array']
                ]);
            } else {
                // Create new
                Attribute::create([
                    'OwnerTypeID' => $this->selectedType->ID,
                    'Name' => $attr['name'],
                    'AttributeTypeID' => $attr['attribute_type_id'],
                    'IsComposition' => $attr['is_composition'],
                    'IsArray' => $attr['is_array']
                ]);
            }
        }
    }

    private function createTypeAttributes($typeId)
    {
        foreach ($this->typeAttributes as $attr) {
            Attribute::create([
                'OwnerTypeID' => $typeId,
                'Name' => $attr['name'],
                'AttributeTypeID' => $attr['attribute_type_id'],
                'IsComposition' => $attr['is_composition'],
                'IsArray' => $attr['is_array']
            ]);
        }
    }

    public function addAttribute()
    {
        $this->validate([
            'newAttribute.name' => ['required', 'string', 'max:255'],
            'newAttribute.attribute_type_id' => ['required', 'exists:Types,ID'],
        ], [
            'newAttribute.name.required' => 'El nombre del atributo es obligatorio.',
            'newAttribute.attribute_type_id.required' => 'El tipo del atributo es obligatorio.',
        ]);

        $attributeType = Type::find($this->newAttribute['attribute_type_id']);
        
        if ($this->editingAttributeIndex !== null) {
            // Update existing attribute
            $this->typeAttributes[$this->editingAttributeIndex] = [
                'id' => $this->typeAttributes[$this->editingAttributeIndex]['id'] ?? null,
                'name' => $this->newAttribute['name'],
                'attribute_type_id' => $this->newAttribute['attribute_type_id'],
                'attribute_type_name' => $attributeType->Name,
                'is_composition' => $this->newAttribute['is_composition'],
                'is_array' => $this->newAttribute['is_array']
            ];
            $this->editingAttributeIndex = null;
        } else {
            // Add new attribute
            $this->typeAttributes[] = [
                'name' => $this->newAttribute['name'],
                'attribute_type_id' => $this->newAttribute['attribute_type_id'],
                'attribute_type_name' => $attributeType->Name,
                'is_composition' => $this->newAttribute['is_composition'],
                'is_array' => $this->newAttribute['is_array']
            ];
        }

        $this->resetNewAttribute();
    }

    public function editAttribute($index)
    {
        $attribute = $this->typeAttributes[$index];
        $this->newAttribute = [
            'name' => $attribute['name'],
            'attribute_type_id' => $attribute['attribute_type_id'],
            'is_composition' => $attribute['is_composition'],
            'is_array' => $attribute['is_array']
        ];
        $this->editingAttributeIndex = $index;
    }

    public function cancelEditAttribute()
    {
        $this->editingAttributeIndex = null;
        $this->resetNewAttribute();
    }

    public function removeAttribute($index)
    {
        if ($this->editingAttributeIndex === $index) {
            $this->cancelEditAttribute();
        }
        unset($this->typeAttributes[$index]);
        $this->typeAttributes = array_values($this->typeAttributes);
        
        // Adjust editing index if necessary
        if ($this->editingAttributeIndex !== null && $this->editingAttributeIndex > $index) {
            $this->editingAttributeIndex--;
        }
    }

    public function resetNewAttribute()
    {
        $this->newAttribute = [
            'name' => '',
            'attribute_type_id' => '',
            'is_composition' => true,
            'is_array' => false
        ];
        $this->editingAttributeIndex = null;
    }

    public function confirmDeleteType($typeId)
    {
        $this->typeToDelete = Type::find($typeId);
        $this->showDeleteTypeModal = true;
    }

    public function deleteType()
    {
        if ($this->typeToDelete) {
            $this->typeToDelete->delete();
            session()->flash('success', 'Tipo eliminado exitosamente.');
        }

        $this->showDeleteTypeModal = false;
        $this->typeToDelete = null;
    }

    public function cancelDeleteType()
    {
        $this->showDeleteTypeModal = false;
        $this->typeToDelete = null;
    }

    public function resetTypeForm()
    {
        $this->selectedType = null;
        $this->typeName = '';
        $this->typeSlug = '';
        $this->typeIsPrimitive = false;
        $this->typeIsAbstract = false;
        $this->typeAttributes = [];
        $this->selectedParentTypes = [];
        $this->resetNewAttribute();
    }

    public function closeTypeModal()
    {
        $this->showTypeModal = false;
        $this->resetTypeForm();
    }

    // Inheritance management methods
    public function addParentType($parentTypeId)
    {
        if (!in_array($parentTypeId, $this->selectedParentTypes)) {
            $this->selectedParentTypes[] = $parentTypeId;
        }
    }

    public function removeParentType($parentTypeId)
    {
        $this->selectedParentTypes = array_filter(
            $this->selectedParentTypes,
            fn($id) => $id !== $parentTypeId
        );
        $this->selectedParentTypes = array_values($this->selectedParentTypes);
    }

    private function validateInheritanceCycles()
    {
        if (empty($this->selectedParentTypes)) {
            return true;
        }

        $typeId = $this->selectedType ? $this->selectedType->ID : null;
        
        foreach ($this->selectedParentTypes as $parentId) {
            if ($this->wouldCreateCycle($typeId, $parentId)) {
                return false;
            }
        }

        return true;
    }

    private function wouldCreateCycle($typeId, $parentId)
    {
        if ($typeId === $parentId) {
            return true;
        }

        if (!$typeId) {
            return false;
        }

        $visited = [];
        return $this->checkCycleRecursive($parentId, $typeId, $visited);
    }

    private function checkCycleRecursive($currentId, $targetId, &$visited)
    {
        if ($currentId === $targetId) {
            return true;
        }

        if (in_array($currentId, $visited)) {
            return false;
        }

        $visited[] = $currentId;
        
        $type = Type::find($currentId);
        if (!$type) {
            return false;
        }

        foreach ($type->parents as $parent) {
            if ($this->checkCycleRecursive($parent->ID, $targetId, $visited)) {
                return true;
            }
        }

        return false;
    }

    private function updateInheritanceRelationships()
    {
        if (!$this->selectedType) {
            return;
        }

        $this->selectedType->parents()->sync($this->selectedParentTypes);
        $this->clearInheritanceCache($this->selectedType->ID);
    }

    private function createInheritanceRelationships($typeId)
    {
        if (empty($this->selectedParentTypes)) {
            return;
        }

        $type = Type::find($typeId);
        if ($type) {
            $type->parents()->sync($this->selectedParentTypes);
            $this->clearInheritanceCache($typeId);
        }
    }

    private function clearInheritanceCache($typeId)
    {
        Cache::forget("type_{$typeId}_inherited_attributes");
        
        // Also clear cache for child types
        $type = Type::find($typeId);
        if ($type) {
            foreach ($type->children as $child) {
                Cache::forget("type_{$child->ID}_inherited_attributes");
            }
        }
    }

    public function render()
    {
        $types = Type::query()
            ->when($this->searchTypes, function ($query) {
                $searchTerm = strtolower($this->searchTypes);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(Name) like ?', ['%' . $searchTerm . '%'])
                      ->orWhereRaw('LOWER(Slug) like ?', ['%' . $searchTerm . '%']);
                });
            })
            ->withCount('attributes')
            ->orderBy('Name')
            ->paginate(12);

        $availableTypes = Type::query()
            ->when($this->searchAttributeTypes, function ($query) {
                $searchTerm = strtolower($this->searchAttributeTypes);
                $query->whereRaw('LOWER(Name) like ?', ['%' . $searchTerm . '%'])
                      ->orWhereRaw('LOWER(Slug) like ?', ['%' . $searchTerm . '%']);
            })
            ->orderBy('Name')
            ->get();

        // Get available parent types (excluding self if editing)
        $availableParentTypes = Type::query()
            ->when($this->searchParentTypes, function ($query) {
                $searchTerm = strtolower($this->searchParentTypes);
                $query->whereRaw('LOWER(Name) like ?', ['%' . $searchTerm . '%'])
                      ->orWhereRaw('LOWER(Slug) like ?', ['%' . $searchTerm . '%']);
            })
            ->when($this->selectedType && isset($this->selectedType->ID), function ($query) {
                // Only exclude self when editing an existing type
                $query->where('ID', '!=', $this->selectedType->ID);
            })
            ->orderBy('Name')
            ->get();


        return view('livewire.types-component', [
            'types' => $types,
            'availableTypes' => $availableTypes,
            'availableParentTypes' => $availableParentTypes
        ])->layout('layouts.app');
    }
}