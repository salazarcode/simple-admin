<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

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

    public function mount()
    {
        $this->typeAttributes = [];
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
        $this->selectedType = Type::with(['attributes.attributeType'])->find($typeId);
    }

    public function createType()
    {
        $this->resetTypeForm();
        $this->showTypeModal = true;
    }

    public function editType($typeId)
    {
        $this->selectedType = Type::with(['attributes.attributeType'])->findOrFail($typeId);
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
        $this->resetNewAttribute();
    }

    public function closeTypeModal()
    {
        $this->showTypeModal = false;
        $this->resetTypeForm();
    }

    public function render()
    {
        $types = Type::query()
            ->when($this->searchTypes, function ($query) {
                $query->where(function ($q) {
                    $q->where('Name', 'like', '%' . $this->searchTypes . '%')
                      ->orWhere('Slug', 'like', '%' . $this->searchTypes . '%');
                });
            })
            ->withCount('attributes')
            ->orderBy('Name')
            ->paginate(12);

        $availableTypes = Type::query()
            ->when($this->searchAttributeTypes, function ($query) {
                $query->where('Name', 'like', '%' . $this->searchAttributeTypes . '%');
            })
            ->orderBy('Name')
            ->get();

        return view('livewire.types-component', [
            'types' => $types,
            'availableTypes' => $availableTypes
        ])->layout('layouts.app');
    }
}