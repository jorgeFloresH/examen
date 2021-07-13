<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Persona;

class Personas extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $nombre, $carnet;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.personas.view', [
            'personas' => Persona::latest()
						->orWhere('nombre', 'LIKE', $keyWord)
						->orWhere('carnet', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->nombre = null;
		$this->carnet = null;
    }

    public function store()
    {
        $this->validate([
		'nombre' => 'required',
		'carnet' => 'required',
        ]);

        Persona::create([ 
			'nombre' => $this-> nombre,
			'carnet' => $this-> carnet
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Persona Successfully created.');
    }

    public function edit($id)
    {
        $record = Persona::findOrFail($id);

        $this->selected_id = $id; 
		$this->nombre = $record-> nombre;
		$this->carnet = $record-> carnet;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'nombre' => 'required',
		'carnet' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Persona::find($this->selected_id);
            $record->update([ 
			'nombre' => $this-> nombre,
			'carnet' => $this-> carnet
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Persona Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Persona::where('id', $id);
            $record->delete();
        }
    }
}
